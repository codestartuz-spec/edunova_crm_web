<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

/** v22: obunasi tugayotgan filiallar haqida direktorlarga Telegram eslatma (kuniga bir marta). */
class NotifySubscriptions extends Command
{
    protected $signature = 'subscriptions:notify';

    protected $description = "Filial obunasi tugashi haqida eslatma yuboradi (7/3/1 kun oldin va tugagan kuni)";

    public function handle(SubscriptionService $service): int
    {
        $this->info('Yuborilgan eslatmalar: '.$service->sendReminders());

        return self::SUCCESS;
    }
}
