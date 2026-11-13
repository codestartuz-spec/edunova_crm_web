<?php

namespace App\Services\Telegram;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/** v14: Telegram Bot API bilan ishlash. Token shifrlangan holda `app_settings`da saqlanadi. */
class TelegramService
{
    public function token(): ?string
    {
        return AppSetting::get('telegram.token');
    }

    public function configured(): bool
    {
        return filled($this->token());
    }

    /** @return array<string,mixed> Telegram javobi (ok=false bo'lsa ham massiv qaytadi) */
    public function call(string $method, array $params = []): array
    {
        if (! $this->configured()) {
            return ['ok' => false, 'description' => 'Bot tokeni kiritilmagan.'];
        }

        try {
            $r = Http::timeout(20)->asJson()->post("https://api.telegram.org/bot{$this->token()}/{$method}", $params);

            return (array) ($r->json() ?? ['ok' => false, 'description' => 'HTTP '.$r->status()]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'description' => 'Ulanib bo\'lmadi: '.$e->getMessage()];
        }
    }

    public function sendMessage(int|string $chatId, string $text, array $extra = []): bool
    {
        return (bool) ($this->call('sendMessage', ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML'] + $extra)['ok'] ?? false);
    }

    /** @return array{ok:bool,error:?string} */
    public function sendDocument(int|string $chatId, string $path, string $caption = '', ?string $filename = null): array
    {
        if (! $this->configured()) {
            return ['ok' => false, 'error' => 'Bot tokeni kiritilmagan.'];
        }

        try {
            $r = Http::timeout(180)->attach('document', fopen($path, 'r'), $filename ?? basename($path))
                ->post("https://api.telegram.org/bot{$this->token()}/sendDocument", ['chat_id' => $chatId, 'caption' => $caption]);
            $j = (array) $r->json();

            return ['ok' => (bool) ($j['ok'] ?? false), 'error' => ($j['ok'] ?? false) ? null : ($j['description'] ?? 'HTTP '.$r->status())];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    // ---- Admin ID lar -------------------------------------------------------------------------------------------

    /** @return array<int, array{id:int,label:string}> */
    public function admins(): array
    {
        $list = json_decode((string) AppSetting::get('telegram.admins', '[]'), true);

        return collect(is_array($list) ? $list : [])->filter(fn ($a) => isset($a['id']))->map(fn ($a) => ['id' => (int) $a['id'], 'label' => (string) ($a['label'] ?? '')])->values()->all();
    }

    public function adminIds(): array
    {
        return array_column($this->admins(), 'id');
    }

    public function isAdmin(int $id): bool
    {
        return in_array($id, $this->adminIds(), true);
    }

    public function addAdmin(int $id, string $label = ''): void
    {
        $admins = collect($this->admins())->reject(fn ($a) => $a['id'] === $id)->push(['id' => $id, 'label' => $label])->values()->all();
        AppSetting::put('telegram.admins', json_encode($admins, JSON_UNESCAPED_UNICODE));
    }

    public function removeAdmin(int $id): void
    {
        AppSetting::put('telegram.admins', json_encode(collect($this->admins())->reject(fn ($a) => $a['id'] === $id)->values()->all(), JSON_UNESCAPED_UNICODE));
    }

    // ---- Webhook ------------------------------------------------------------------------------------------------

    public function webhookUrl(): string
    {
        return url('/telegram/webhook');
    }

    public function secret(): string
    {
        $s = AppSetting::get('telegram.secret');
        if (! $s) {
            $s = Str::random(48);
            AppSetting::put('telegram.secret', $s, true);
        }

        return $s;
    }

    public function setWebhook(): array
    {
        return $this->call('setWebhook', ['url' => $this->webhookUrl(), 'secret_token' => $this->secret(), 'allowed_updates' => ['message'], 'drop_pending_updates' => true]);
    }
}
