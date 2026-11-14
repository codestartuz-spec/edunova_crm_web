<?php

namespace App\Console\Commands;

use App\Services\DailyReportService;
use Illuminate\Console\Command;

class SendDailyReport extends Command
{
    protected $signature = 'report:daily {--now : Vaqtga qaramay hammaga hozir yuborish}';

    protected $description = "Kunlik hisobotni Telegram orqali Admin ID, direktor va filial adminlariga yuboradi";

    public function handle(DailyReportService $svc): int
    {
        $this->info('Yuborildi: '.$svc->send(null, onlyDue: ! $this->option('now')).' ta xabar');

        return self::SUCCESS;
    }
}
