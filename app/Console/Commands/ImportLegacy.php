<?php

namespace App\Console\Commands;

use App\Services\Legacy\LegacyImporter;
use App\Models\User;
use App\Services\BackupService;
use App\Services\Legacy\LegacyProgress;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportLegacy extends Command
{
    protected $signature = 'app:import-legacy {file : .sql yoki .sql.gz dump} {--primary= : Fayldagi qaysi sAdmin ID hozirgi sAdminga birlashadi} {--dry-run : Faqat tekshirish} {--force : Tasdiqsiz} {--token= : Veb jarayon identifikatori} {--backup : Avval zaxira olish}';

    protected $description = "Eski NovaCrm bazasi dampini hozirgi tizimga ko'chiradi (sAdmin hisobi, sozlamalar saqlanadi)";

    public function handle(LegacyImporter $importer): int
    {
        $file = (string) $this->argument('file');
        if ($token = (string) $this->option('token')) {
            return $this->webMode($importer, $file, $token);
        }
        if (! is_file($file)) {
            $this->error('Fayl topilmadi.');

            return self::FAILURE;
        }
        $info = $importer->inspect($file);
        foreach ($info['errors'] as $e) {
            $this->error($e);
        }
        $this->table(['Jadval', 'Qator'], collect($info['tables'])->map(fn ($n, $t) => [$t, $n])->values()->all());
        $this->line('sAdminlar: '.collect($info['sadmins'])->map(fn ($s) => "{$s['id']}={$s['username']}")->implode(', '));
        if ($info['errors'] || $this->option('dry-run')) {
            return $info['errors'] ? self::FAILURE : self::SUCCESS;
        }
        if (! $this->option('force') && ! $this->confirm("Hozirgi barcha ma'lumotlar (sAdmin va sozlamalardan tashqari) o'chiriladi. Davom etasizmi?")) {
            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(100);
        $report = $importer->run($file, ['primary' => $this->option('primary')], function (string $l, int $p) use ($bar) {
            $bar->setProgress($p);
        });
        $bar->finish();
        $this->newLine(2);
        foreach ($report['checks'] as $c) {
            $this->line(($c['ok'] ? '✔ ' : '✘ ').$c['label'].': '.$c['detail']);
        }
        $this->info("Tayyor ({$report['seconds']} s).");

        return self::SUCCESS;
    }

    /** Veb bo'limdan fon jarayoni sifatida: holat faylga yoziladi. */
    private function webMode(LegacyImporter $importer, string $file, string $token): int
    {
        set_time_limit(0);
        LegacyProgress::write($token, ['status' => 'running', 'percent' => 1, 'label' => 'Boshlanmoqda', 'pid' => getmypid(), 'started_at' => time(), 'log' => []]);
        try {
            if ($this->option('backup')) {
                LegacyProgress::log($token, 'Joriy bazaning zaxirasi olinmoqda…', 2, 'Zaxira olinmoqda');
                $admin = User::where('role', 'sadmin')->orderBy('id')->first();
                $b = app(BackupService::class)->run('manual', $admin);
                if ($b->status !== 'ok') {
                    throw new \RuntimeException('Zaxira olinmadi, ko\'chirish to\'xtatildi: '.$b->error);
                }
                LegacyProgress::log($token, 'Zaxira tayyor.', 3, 'Zaxira tayyor');
            }
            $report = $importer->run($file, ['primary' => $this->option('primary') ?: null], fn (string $l, int $p) => LegacyProgress::log($token, $l, $p, $l));
            File::ensureDirectoryExists(storage_path('app/legacy-import'));
            file_put_contents(storage_path('app/legacy-import/last-report.json'), json_encode($report, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            @unlink($file);
            LegacyProgress::write($token, ['status' => 'done', 'percent' => 100, 'label' => 'Tayyor', 'report' => $report]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            report($e);
            LegacyProgress::write($token, ['status' => 'failed', 'label' => 'Xato', 'error' => "Ko'chirish bekor qilindi (hech narsa o'zgarmadi): ".$e->getMessage()]);

            return self::FAILURE;
        }
    }
}
