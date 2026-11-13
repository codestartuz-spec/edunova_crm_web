<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * v8 A3: ma'lumotlar bazasidan avtomatik zaxira nusxa oladi (storage/app/backups/*.sql.gz).
 * MySQL/MariaDB uchun `mysqldump`, SQLite uchun bazani to'g'ridan-to'g'ri o'qib gzip qiladi.
 * .env va boshqa fayllar zaxiraga kirmaydi - faqat baza. Eski nusxalar --keep kunidan keyin o'chiriladi.
 */
class BackupDatabase extends Command
{
    protected $signature = 'app:backup {--keep=14 : Nechta kunlik zaxira saqlanadi, eskilari o\'chiriladi} {--trigger=schedule : schedule|manual} {--by= : Ishga tushirgan foydalanuvchi ID}';

    protected $description = "Ma'lumotlar bazasidan zaxira nusxa oladi (storage/app/backups)";

    /** v12: oxirgi urinish natijasi shu faylga yoziladi - "Tizim holati" sahifasi buni o'qib,
     *  logni titkilamasdan aniq sababni ko'rsatadi (masalan "mysqldump topilmadi").
     */
    private const STATUS_FILE = '.status.json';

    public function handle(): int
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}", []);
        $driver = $config['driver'] ?? null;

        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $file = $dir.'/backup_'.now()->format('Y-m-d_His').'_'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4)).'.sql.gz';
        $started = (int) round(microtime(true) * 1000);
        $trigger = in_array($this->option('trigger'), ['schedule', 'manual'], true) ? $this->option('trigger') : 'schedule';
        $by = $this->option('by') ? (int) $this->option('by') : null;

        try {
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $this->backupMysql($config, $file);
            } elseif ($driver === 'sqlite') {
                $this->backupSqlite($config, $file);
            } else {
                throw new \RuntimeException("Zaxira faqat MySQL/MariaDB/SQLite uchun qo'llab-quvvatlanadi ('{$driver}' emas).");
            }
        } catch (\Throwable $e) {
            if (File::exists($file)) {
                File::delete($file);
            }
            Log::error('Zaxira nusxa olishda xatolik: '.$e->getMessage());
            $this->error('Xatolik: '.$e->getMessage());
            $this->recordStatus($dir, false, $e->getMessage());
            try {
                app(\App\Services\BackupService::class)->recordFailure($trigger, $e->getMessage(), $started, $by);
            } catch (\Throwable) {
            }

            return self::FAILURE;
        }

        $this->prune($dir, max(1, (int) $this->option('keep')));

        // v14: parollangan ZIP (AES-256) + Telegram + jadvalga yozish (xatolik zaxira natijasini buzmaydi)
        try {
            $backup = app(\App\Services\BackupService::class)->finalize($file, $trigger, $started, $by);
            if ($backup->status === 'failed') {
                $this->warn('ZIP/Telegram bosqichida xato: '.$backup->error);
            }
        } catch (\Throwable $e) {
            $this->warn('ZIP/Telegram bosqichida xato: '.$e->getMessage());
        }

        $size = $this->humanSize(File::size($file));
        $this->info('Zaxira saqlandi: '.basename($file).' ('.$size.')');
        $this->recordStatus($dir, true, 'Zaxira saqlandi: '.basename($file).' ('.$size.')');

        return self::SUCCESS;
    }

    private function recordStatus(string $dir, bool $ok, string $message): void
    {
        try {
            File::put($dir.'/'.self::STATUS_FILE, json_encode([
                'ok' => $ok, 'message' => $message, 'at' => now()->timestamp,
            ]));
        } catch (\Throwable) {
            // Holat fayli yozilmasa ham asosiy natija (zaxira olindi/olinmadi) o'zgarmaydi - jim o'tkaziladi.
        }
    }

    /** mysqldump orqali dump oladi (shell pipe emas - argumentlar massiv ko'rinishida, parol MYSQL_PWD orqali, ps aux'da ko'rinmaydi). */
    private function backupMysql(array $config, string $file): void
    {
        try {
            $this->backupMysqldump($config, $file);

            return;
        } catch (\Throwable $first) {
            $e = $first;
            // mysqldump yo'q yoki ulana olmadi (masalan caching_sha2_password) - PHP orqali dump olamiz
            Log::warning('mysqldump ishlamadi, PHP orqali dump olinmoqda: '.$e->getMessage());
        }

        try {
            $dump = app(\App\Services\PdoDumper::class)->dump();
        } catch (\Throwable $e2) {
            throw new \RuntimeException($first->getMessage().' | PHP orqali dump ham ishlamadi: '.$e2->getMessage());
        }
        File::put($file, gzencode($dump, 9));
    }

    private function backupMysqldump(array $config, string $file): void
    {
        $this->ensureMysqldumpAvailable();

        $args = [
            'mysqldump',
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '-h', (string) ($config['host'] ?? '127.0.0.1'),
            '-P', (string) ($config['port'] ?? 3306),
            '-u', (string) ($config['username'] ?? 'root'),
        ];

        if (! empty($config['unix_socket'])) {
            $args[] = '--socket='.$config['unix_socket'];
        }

        $args[] = $config['database'];

        $result = Process::timeout(600)
            ->env(array_filter(['MYSQL_PWD' => $config['password'] ?? null]))
            ->run($args);

        if (! $result->successful()) {
            throw new \RuntimeException('mysqldump ishlamadi: '.trim($result->errorOutput()));
        }

        $dump = $result->output();

        if (trim($dump) === '') {
            throw new \RuntimeException("Zaxira bo'sh chiqdi.");
        }

        File::put($file, gzencode($dump, 9));
    }

    /**
     * v12: haqiqiy dump'dan OLDIN tekshiradi - shu bilan xato sababi ("proc_open o'chirilgan"
     * yoki "mysqldump topilmadi") darhol aniq bo'ladi, umumiy "mysqldump ishlamadi" o'rniga.
     * Ko'p cPanel/shared hosting'da xavfsizlik uchun `proc_open` o'chirilgan bo'ladi - bu holda
     * `Process` fasadi umuman ishlay olmaydi.
     */
    private function ensureMysqldumpAvailable(): void
    {
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        if (! function_exists('proc_open') || in_array('proc_open', $disabled, true)) {
            throw new \RuntimeException(
                "PHP'da 'proc_open' funksiyasi o'chirilgan (odatda cPanel/shared hosting xavfsizlik sozlamasi) - ".
                "shuning uchun serverda buyruq ishga tushirib bo'lmaydi. Hosting-provayderdan 'proc_open'ni ".
                "yoqishni so'rang, yoki serverga SSH orqali kirib qo'lda zaxira oling (mysqldump)."
            );
        }

        $check = Process::run(['mysqldump', '--version']);

        if (! $check->successful()) {
            throw new \RuntimeException(
                "'mysqldump' dasturi serverda topilmadi yoki ishga tushmadi. Terminalda 'mysqldump --version' ".
                "buyrug'ini sinab ko'ring - 'command not found' chiqsa, hosting-provayderdan MySQL/MariaDB ".
                "klient vositalarini (mysqldump) o'rnatishni so'rang."
            );
        }
    }

    private function backupSqlite(array $config, string $file): void
    {
        $path = $config['database'] ?? null;

        if (! $path || $path === ':memory:' || ! File::exists($path)) {
            throw new \RuntimeException("SQLite baza fayli topilmadi (xotiradagi baza zaxiralanmaydi).");
        }

        File::put($file, gzencode(File::get($path), 9));
    }

    private function prune(string $dir, int $keepDays): void
    {
        $cutoff = now()->subDays($keepDays)->getTimestamp();

        foreach (File::files($dir) as $f) {
            if (str_ends_with($f->getFilename(), '.sql.gz') && $f->getMTime() < $cutoff) {
                File::delete($f->getPathname());
            }
        }
    }

    private function humanSize(int $bytes): string
    {
        return $bytes >= 1048576 ? round($bytes / 1048576, 1).' MB' : round($bytes / 1024, 1).' KB';
    }
}
