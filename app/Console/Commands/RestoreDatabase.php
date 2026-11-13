<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use ZipArchive;

/** v14: zaxiradan tiklash (faqat konsol orqali — veb orqali tiklash yo'q). */
class RestoreDatabase extends Command
{
    protected $signature = 'app:restore {file : Parollangan .zip yoki oddiy .sql.gz fayl yo\'li} {--password= : ZIP paroli (bo\'sh bo\'lsa saqlangan parol ishlatiladi)} {--force : Tasdiqsiz bajarish}';

    protected $description = "Bazani zaxira faylidan tiklaydi (joriy ma'lumotlar ustiga yoziladi!)";

    public function handle(BackupService $backups): int
    {
        $file = $this->argument('file');
        if (! File::exists($file)) {
            $this->error('Fayl topilmadi.');

            return self::FAILURE;
        }

        $cfg = config('database.connections.'.config('database.default'));
        if (! in_array($cfg['driver'] ?? null, ['mysql', 'mariadb'], true)) {
            $this->error('Tiklash faqat MySQL/MariaDB uchun.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm("DIQQAT: '{$cfg['database']}' bazasi shu fayl bilan almashtiriladi. Davom etasizmi?")) {
            return self::FAILURE;
        }

        $tmp = storage_path('app/restore_tmp');
        File::deleteDirectory($tmp);
        File::ensureDirectoryExists($tmp);
        $gz = $file;

        if (str_ends_with($file, '.zip')) {
            $zip = new ZipArchive;
            $zip->open($file);
            $zip->setPassword($this->option('password') ?: $backups->zipPassword());
            $name = collect(range(0, $zip->numFiles - 1))->map(fn ($i) => $zip->getNameIndex($i))->first(fn ($n) => str_ends_with($n, '.sql.gz'));
            if (! $name || ! $zip->extractTo($tmp, $name)) {
                $this->error("ZIP ochilmadi (parol noto'g'ri bo'lishi mumkin).");

                return self::FAILURE;
            }
            $zip->close();
            $gz = $tmp.'/'.$name;
        }

        $sql = gzdecode(File::get($gz));
        File::put($tmp.'/restore.sql', $sql);

        if (\App\Services\PdoDumper::isPdoDump($sql)) {
            app(\App\Services\PdoDumper::class)->restore($sql);
            File::deleteDirectory($tmp);
            $this->info('Baza tiklandi.');

            return self::SUCCESS;
        }

        $r = Process::timeout(1800)->env(array_filter(['MYSQL_PWD' => $cfg['password'] ?? null]))->input($sql)->run([
            'mysql', '-h', (string) $cfg['host'], '-P', (string) $cfg['port'], '-u', (string) $cfg['username'], $cfg['database'],
        ]);

        File::deleteDirectory($tmp);

        if (! $r->successful()) {
            $this->error('mysql xatosi: '.trim($r->errorOutput()));

            return self::FAILURE;
        }

        $this->info('Baza tiklandi.');

        return self::SUCCESS;
    }
}
