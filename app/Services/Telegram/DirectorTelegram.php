<?php

namespace App\Services\Telegram;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** v26: direktorni Telegram'ga bog'lash - ID qo'lda kiritiladi yoki bir martalik havola orqali. */
class DirectorTelegram
{
    public const LINK_TTL_MINUTES = 30;

    public function __construct(private TelegramService $tg) {}

    /** Telegram ID ni saqlaydi (bo'sh bo'lsa uzadi). Avval bot unga xabar yubora olishini tekshiradi. */
    public function setChatId(User $d, ?string $raw): void
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            $this->unlink($d);

            return;
        }
        if (! preg_match('/^\d{5,15}$/', $raw)) {
            throw ValidationException::withMessages(['telegram_chat_id' => "Telegram ID faqat raqamlardan iborat bo'lishi kerak (masalan 123456789)."]);
        }
        $id = (int) $raw;
        if ($d->telegram_chat_id !== null && (int) $d->telegram_chat_id === $id) {
            return;   // o'zgarmagan
        }
        if (User::withoutGlobalScopes()->where('role', Role::Director->value)->where('id', '!=', $d->id)->where('telegram_chat_id', $id)->exists()) {
            throw ValidationException::withMessages(['telegram_chat_id' => "Bu Telegram ID boshqa direktorga bog'langan."]);
        }
        if (! $this->tg->configured()) {
            throw ValidationException::withMessages(['telegram_chat_id' => "Telegram bot hali sozlanmagan (Sozlamalar → Telegram bot)."]);
        }

        $res = $this->tg->deliver($id, "✅ <b>".e(\App\Support\Brand::name())."</b> botiga ulandingiz.\nKunlik hisobot shu yerga keladi.");
        if (! $res['ok']) {
            throw ValidationException::withMessages(['telegram_chat_id' => "Xabar yuborib bo'lmadi: direktor avval botga kirib /start yozishi va ID to'g'riligini tekshirishi kerak."]);
        }

        $d->forceFill(['telegram_chat_id' => $id, 'telegram_username' => null, 'telegram_linked_at' => now()])->save();
    }

    public function unlink(User $d): void
    {
        if ($d->telegram_chat_id !== null) {
            $d->forceFill(['telegram_chat_id' => null, 'telegram_username' => null, 'telegram_linked_at' => null])->save();
        }
    }

    /** @return array{url:string,minutes:int}|null bot sozlanmagan bo'lsa null */
    public function makeLink(User $d): ?array
    {
        $bot = $this->tg->botUsername();
        if (! $bot) {
            return null;
        }
        $token = 'd'.Str::lower(Str::random(24));
        Cache::put('tg.link.'.$token, $d->id, now()->addMinutes(self::LINK_TTL_MINUTES));

        return ['url' => "https://t.me/{$bot}?start={$token}", 'minutes' => self::LINK_TTL_MINUTES];
    }

    /** Botdagi `/start d...` ni qayta ishlaydi; muvaffaqiyatli bo'lsa direktorni qaytaradi. */
    public function consume(string $token, int $chatId, ?string $username): ?User
    {
        $userId = Cache::pull('tg.link.'.$token);
        $d = $userId ? User::withoutGlobalScopes()->where('role', Role::Director->value)->whereNull('archived_at')->find($userId) : null;
        if (! $d) {
            return null;
        }
        // shu chat boshqa direktorda turgan bo'lsa, u yerdan olinadi (bitta chat - bitta direktor)
        User::withoutGlobalScopes()->where('role', Role::Director->value)->where('id', '!=', $d->id)->where('telegram_chat_id', $chatId)
            ->update(['telegram_chat_id' => null, 'telegram_username' => null, 'telegram_linked_at' => null]);
        $d->forceFill(['telegram_chat_id' => $chatId, 'telegram_username' => $username, 'telegram_linked_at' => now()])->save();

        return $d;
    }
}
