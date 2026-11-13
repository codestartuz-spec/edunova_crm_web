<?php

namespace App\Services\Sms;

/** v14: SMS yuborish kanali (USB modem yoki Eskiz). */
interface SmsGateway
{
    public function name(): string;

    /** Kanal sozlangan va ishlatishga tayyormi. */
    public function isConfigured(): bool;

    /**
     * Bitta SMS yuboradi. Sozlama/provayder xatolari SmsResult::fail bilan qaytadi;
     * tarmoq (ulanish) xatolari istisno sifatida tashlanishi mumkin.
     */
    public function send(string $phone, string $text): SmsResult;
}
