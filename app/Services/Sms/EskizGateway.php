<?php

namespace App\Services\Sms;

use App\Models\Branch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/** Eskiz.uz orqali yuborish. Filialning o'z akkaunti bo'lsa o'sha, bo'lmasa .env dagi umumiy akkaunt. */
class EskizGateway implements SmsGateway
{
    public function __construct(private Branch $branch) {}

    public function name(): string
    {
        return 'eskiz';
    }

    public function isConfigured(): bool
    {
        return $this->branch->hasOwnSmsAccount() || (filled(config('services.eskiz.email')) && filled(config('services.eskiz.password')));
    }

    public function send(string $phone, string $text): SmsResult
    {
        try {
            [$email, $password, $from] = $this->credentials();
            $response = $this->post($email, $password, $from, $phone, $text);
        } catch (RuntimeException $e) {
            return SmsResult::fail('eskiz', $e->getMessage());
        }

        $ok = in_array($response['status'] ?? null, ['waiting', 'success', 'delivered', 'ok'], true);

        return new SmsResult($ok, mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE), 0, 1000), 'eskiz');
    }

    /** @return array{0:string,1:string,2:string} */
    private function credentials(): array
    {
        if ($this->branch->hasOwnSmsAccount()) {
            return [$this->branch->eskiz_email, $this->branch->eskiz_password, $this->branch->eskiz_from ?: config('services.eskiz.from')];
        }

        $email = config('services.eskiz.email');
        $password = config('services.eskiz.password');

        if (blank($email) || blank($password)) {
            throw new RuntimeException('Eskiz akkaunti sozlanmagan (.env yoki filial sozlamalari).');
        }

        return [$email, $password, config('services.eskiz.from')];
    }

    private function post(string $email, string $password, string $from, string $phone, string $message, bool $retry = true): array
    {
        $base = rtrim(config('services.eskiz.base_url'), '/');
        $response = Http::withToken($this->token($email, $password))->timeout(20)->post("{$base}/message/sms/send", [
            'mobile_phone' => $phone, 'message' => $message, 'from' => $from,
        ]);

        // Token eskirgan bo'lsa, yangilab bir marta qayta urinamiz
        if ($response->status() === 401 && $retry) {
            Cache::forget($this->tokenKey($email));

            return $this->post($email, $password, $from, $phone, $message, retry: false);
        }

        return (array) $response->json();
    }

    private function token(string $email, string $password): string
    {
        return Cache::remember($this->tokenKey($email), now()->addDays(25), function () use ($email, $password) {
            $base = rtrim(config('services.eskiz.base_url'), '/');
            $response = Http::timeout(20)->post("{$base}/auth/login", ['email' => $email, 'password' => $password]);

            if (! $response->successful() || ! $response->json('data.token')) {
                throw new RuntimeException('Eskiz akkauntiga kirib bo\'lmadi (login yoki parol noto\'g\'ri).');
            }

            return $response->json('data.token');
        });
    }

    private function tokenKey(string $email): string
    {
        return 'eskiz_token_'.md5($email);
    }
}
