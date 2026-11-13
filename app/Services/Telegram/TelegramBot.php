<?php

namespace App\Services\Telegram;

use App\Models\User;
use App\Support\Format;
use App\Support\PhoneLookup;

/** v14: bot buyruqlari. Telefonni bog'lash (contact.user_id == from.id tekshiruvi bilan), /status va /backup (faqat Admin ID). */
class TelegramBot
{
    public function __construct(private TelegramService $tg) {}

    public function handle(array $update): void
    {
        $m = $update['message'] ?? null;
        if (! $m || ($m['chat']['type'] ?? '') !== 'private') {
            return;
        }

        $chatId = (int) $m['chat']['id'];
        $fromId = (int) ($m['from']['id'] ?? 0);
        $text = trim((string) ($m['text'] ?? ''));

        if (isset($m['contact'])) {
            $this->linkContact($chatId, $fromId, $m['contact'], $m['from']['username'] ?? null);

            return;
        }

        $cmd = strtolower(strtok($text, " @\n") ?: '');

        match ($cmd) {
            '/start' => $this->start($chatId),
            '/status' => $this->adminOnly($fromId, $chatId, fn () => $this->status($chatId)),
            '/backup' => $this->adminOnly($fromId, $chatId, fn () => $this->backup($chatId)),
            '/unlink' => $this->unlink($chatId),
            '/id' => $this->tg->sendMessage($chatId, "Sizning Telegram ID: <code>{$fromId}</code>"),
            default => $this->tg->sendMessage($chatId, "Buyruqlar: /start — raqamni bog'lash, /unlink — uzish, /id — Telegram ID".($this->tg->isAdmin($fromId) ? ', /status, /backup' : '')),
        };
    }

    private function start(int $chatId): void
    {
        $this->tg->sendMessage($chatId, "Assalomu alaykum! ".\App\Support\Brand::name()." boti.\nParolni tiklash kodlarini olish uchun telefon raqamingizni bog'lang: pastdagi tugmani bosing.", [
            'reply_markup' => ['keyboard' => [[['text' => '📱 Telefon raqamini yuborish', 'request_contact' => true]]], 'resize_keyboard' => true, 'one_time_keyboard' => true],
        ]);
    }

    private function linkContact(int $chatId, int $fromId, array $contact, ?string $username): void
    {
        // Boshqa odamning kontaktini yuborib akkauntni "o'g'irlash"ning oldini olish
        if ((int) ($contact['user_id'] ?? 0) !== $fromId) {
            $this->tg->sendMessage($chatId, "Faqat o'zingizning raqamingizni yuboring (tugma orqali).", ['reply_markup' => ['remove_keyboard' => true]]);

            return;
        }

        $users = PhoneLookup::users($contact['phone_number'] ?? '', onlyActive: true);

        if ($users->isEmpty()) {
            $this->tg->sendMessage($chatId, "Bu raqam ".Format::canonicalPhone($contact['phone_number'] ?? '')." tizimda topilmadi. Markaz administratoriga murojaat qiling.", ['reply_markup' => ['remove_keyboard' => true]]);

            return;
        }

        User::withoutGlobalScopes()->whereIn('id', $users->pluck('id'))->update(['telegram_chat_id' => $chatId, 'telegram_username' => $username, 'telegram_linked_at' => now()]);

        $this->tg->sendMessage($chatId, "✅ Ulandi. Akkauntlar: ".e($users->pluck('name')->implode(', '))."\nEndi parolni tiklash kodi shu yerga keladi.", ['reply_markup' => ['remove_keyboard' => true]]);
    }

    private function unlink(int $chatId): void
    {
        $n = User::withoutGlobalScopes()->where('telegram_chat_id', $chatId)->update(['telegram_chat_id' => null, 'telegram_username' => null, 'telegram_linked_at' => null]);
        $this->tg->sendMessage($chatId, $n ? 'Telegram uzildi.' : 'Bu chat hech qaysi akkauntga ulanmagan.');
    }

    private function adminOnly(int $fromId, int $chatId, \Closure $fn): void
    {
        if (! $this->tg->isAdmin($fromId)) {
            $this->tg->sendMessage($chatId, 'Bu buyruq faqat administratorlar uchun.');

            return;
        }

        $fn();
    }

    private function status(int $chatId): void
    {
        $this->tg->sendMessage($chatId, app(\App\Services\HealthService::class)->telegramSummary());
    }

    private function backup(int $chatId): void
    {
        $this->tg->sendMessage($chatId, '⏳ Zaxira olinmoqda...');
        $b = app(\App\Services\BackupService::class)->run('manual', null, onlyChats: [$chatId]);
        $this->tg->sendMessage($chatId, $b->status === 'ok' ? '✅ Zaxira tayyor.' : '❌ Xato: '.e((string) $b->error));
    }
}
