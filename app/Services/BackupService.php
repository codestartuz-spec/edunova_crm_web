<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Backup;
use App\Models\User;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * v14: zaxira — `app:backup` bazani .sql.gz qilib oladi, bu servis uni SHA-256 bilan parollangan ZIP (AES-256)
 * ga o'raydi, Telegram orqali Admin ID larga (faqat sAdmin) yuboradi va `backups` jadvaliga yozadi.
 */
class BackupService
{
    public const TELEGRAM_PART_BYTES = 45 * 1024 * 1024;

    public function __construct(private TelegramService $tg) {}

    public function dir(): string
    {
        return storage_path('app/backups/encrypted');
    }

    public function zipPassword(): string
    {
        // .env dagi BACKUP_ZIP_PASSWORD bazadan va APP_KEY dan alohida saqlanadi - mavjud bo'lsa, shu ishlatiladi
        if ($env = config('features.backup_zip_password')) {
            return (string) $env;
        }

        $p = AppSetting::get('backup.zip_password');
        if (! $p) {
            $p = Str::random(28);
            AppSetting::put('backup.zip_password', $p, true);
        }

        return $p;
    }

    /** Zaxirani hozir oladi (qo'lda / bot buyrug'i). */
    public function run(string $trigger = 'manual', ?User $by = null, ?array $onlyChats = null): Backup
    {
        $before = (int) Backup::max('id');
        if ($onlyChats !== null) {
            Cache::put('backup.only_chats', $onlyChats, 300);
        }

        Artisan::call('app:backup', ['--trigger' => $trigger] + ($by ? ['--by' => $by->id] : []));

        return Backup::where('id', '>', $before)->latest('id')->first() ?? new Backup(['status' => 'failed', 'error' => trim(Artisan::output())]);
    }

    public function recordFailure(string $trigger, string $error, int $startedAt, ?int $by = null): Backup
    {
        return Backup::create(['status' => 'failed', 'trigger' => $trigger, 'error' => mb_substr($error, 0, 1000), 'duration_ms' => $this->ms($startedAt), 'started_by' => $by]);
    }

    /** `.sql.gz` tayyor bo'lgach chaqiriladi. */
    public function finalize(string $gzPath, string $trigger, int $startedAt, ?int $by = null): Backup
    {
        $backup = Backup::create(['status' => 'running', 'trigger' => $trigger, 'started_by' => $by]);

        try {
            $sha = hash_file('sha256', $gzPath);
            File::ensureDirectoryExists($this->dir());
            $zipPath = $this->dir().'/'.pathinfo(basename($gzPath, '.gz'), PATHINFO_FILENAME).'.zip';

            if (File::exists($zipPath)) {
                @unlink($zipPath);
            }
            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('ZIP fayl yaratib bo\'lmadi: '.$zipPath);
            }
            $zip->addFile($gzPath, basename($gzPath));
            $zip->addFromString('SHA256.txt', "{$sha}  ".basename($gzPath)."\n");
            $password = $this->zipPassword();
            foreach ([basename($gzPath), 'SHA256.txt'] as $name) {
                $zip->setEncryptionName($name, ZipArchive::EM_AES_256, $password);
            }
            if (! $zip->close()) {
                throw new \RuntimeException('ZIP yozilmadi.');
            }

            $backup->update(['file' => basename($zipPath), 'size' => filesize($zipPath), 'sha256' => $sha]);

            $only = Cache::pull('backup.only_chats');
            $result = $this->send($zipPath, $sha, is_array($only) ? $only : $this->tg->adminIds());
            $backup->update(['status' => 'ok', 'telegram' => $result ?: null, 'duration_ms' => $this->ms($startedAt)]);
        } catch (\Throwable $e) {
            $backup->update(['status' => 'failed', 'error' => mb_substr($e->getMessage(), 0, 1000), 'duration_ms' => $this->ms($startedAt)]);
        }

        $this->prune();

        return $backup->fresh();
    }

    /** Telegramga qayta yuborish. @return array<string,string> chat_id => ok|xato matni */
    public function send(string $zipPath, string $sha, array $chatIds): array
    {
        $out = [];
        if (! $this->tg->configured()) {
            return $out;
        }

        $size = filesize($zipPath);
        $caption = '💾 '.\App\Support\Brand::name().' zaxirasi — '.now()->format('d.m.Y H:i')."\nHajmi: ".round($size / 1048576, 2)." MB\nSHA-256 (ichidagi .sql.gz): {$sha}";
        $parts = $size > self::TELEGRAM_PART_BYTES ? $this->split($zipPath) : [$zipPath];

        foreach ($chatIds as $chat) {
            $ok = true;
            $err = null;
            foreach ($parts as $i => $part) {
                $label = count($parts) > 1 ? ' (qism '.($i + 1).'/'.count($parts).')' : '';
                $r = $this->withRetry(fn () => $this->tg->sendDocument($chat, $part, $caption.$label));
                if (! $r['ok']) {
                    $ok = false;
                    $err = $r['error'];
                    break;
                }
            }
            $out[(string) $chat] = $ok ? 'ok' : (string) $err;
        }

        if (count($parts) > 1) {
            foreach ($parts as $p) {
                @unlink($p);
            }
        }

        return $out;
    }

    private function withRetry(\Closure $fn, int $times = 3): array
    {
        $r = ['ok' => false, 'error' => 'noma\'lum'];
        for ($i = 1; $i <= $times; $i++) {
            $r = $fn();
            if ($r['ok']) {
                return $r;
            }
            if ($i < $times && ! app()->runningUnitTests()) {
                sleep(2 * $i);
            }
        }

        return $r;
    }

    /** Katta fayl: .zip.001, .zip.002 ... (qismlarni `cat` / `copy /b` bilan birlashtirib, ZIP sifatida ochish mumkin). @return string[] */
    private function split(string $path): array
    {
        $parts = [];
        $in = fopen($path, 'rb');
        $n = 1;
        while (! feof($in)) {
            $chunk = fread($in, self::TELEGRAM_PART_BYTES);
            if ($chunk === '' || $chunk === false) {
                break;
            }
            $p = sprintf('%s.%03d', $path, $n++);
            file_put_contents($p, $chunk);
            $parts[] = $p;
        }
        fclose($in);

        return $parts;
    }

    public function prune(int $keepDays = 14): void
    {
        $cutoff = now()->subDays($keepDays)->getTimestamp();
        if (File::isDirectory($this->dir())) {
            foreach (File::files($this->dir()) as $f) {
                if ($f->getMTime() < $cutoff) {
                    File::delete($f->getPathname());
                }
            }
        }
    }

    public function path(Backup $b): ?string
    {
        $p = $b->file ? $this->dir().'/'.basename($b->file) : null;

        return $p && File::exists($p) ? $p : null;
    }

    private function ms(int $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt / 1000) * 1000);
    }
}
