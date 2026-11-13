<?php

namespace App\Services\Sms;

use App\Models\AppSetting;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * v14: USB modem SMS shlyuzi (sms.elshod.date). Shlyuz hujjati faqat login ortida, shuning uchun
 * so'rov shakli (manzil, usul, maydon nomlari, autentifikatsiya) "SMS sozlamalari"dan qo'lda kiritiladi.
 */
class UsbModemGateway implements SmsGateway
{
    public function name(): string
    {
        return 'modem';
    }

    public function isConfigured(): bool
    {
        return AppSetting::bool('sms.modem.enabled') && filled(AppSetting::get('sms.modem.base_url'));
    }

    public function send(string $phone, string $text): SmsResult
    {
        $base = rtrim((string) AppSetting::get('sms.modem.base_url'), '/');
        $path = '/'.ltrim((string) AppSetting::get('sms.modem.path', '/api/send'), '/');
        $method = strtolower((string) AppSetting::get('sms.modem.method', 'post')) === 'get' ? 'get' : 'post';

        $body = $this->extra() + [
            (string) AppSetting::get('sms.modem.phone_field', 'phone') => $this->formatPhone($phone),
            (string) AppSetting::get('sms.modem.message_field', 'message') => $text,
        ];

        $http = $this->client();
        $url = $base.$path;

        $response = match (true) {
            $method === 'get' => $http->get($url, $body),
            AppSetting::get('sms.modem.body_format', 'json') === 'form' => $http->asForm()->post($url, $body),
            default => $http->asJson()->post($url, $body),
        };

        $raw = mb_substr($response->body(), 0, 1000);

        if (! $response->successful()) {
            return SmsResult::fail('modem', "HTTP {$response->status()}: {$raw}");
        }

        // Ixtiyoriy: JSON javobda muvaffaqiyat maydoni (masalan success = true)
        $field = trim((string) AppSetting::get('sms.modem.success_field', ''));
        if ($field !== '') {
            $expected = strtolower((string) AppSetting::get('sms.modem.success_value', 'true'));
            $actual = $response->json($field);
            $actual = is_bool($actual) ? ($actual ? 'true' : 'false') : strtolower((string) $actual);

            if ($actual !== $expected) {
                return SmsResult::fail('modem', "Shlyuz rad etdi ({$field}={$actual}): {$raw}");
            }
        }

        return SmsResult::ok('modem', $raw);
    }

    private function client(): PendingRequest
    {
        $http = Http::timeout(max(3, AppSetting::int('sms.modem.timeout', 15)))->acceptJson();
        $auth = AppSetting::get('sms.modem.auth_type', 'none');
        $value = (string) AppSetting::get('sms.modem.auth_value', '');

        return match ($auth) {
            'bearer' => $http->withToken($value),
            'header' => $http->withHeaders([(string) AppSetting::get('sms.modem.auth_header', 'X-API-Key') => $value]),
            'basic' => $http->withBasicAuth((string) AppSetting::get('sms.modem.basic_user', ''), $value),
            default => $http,
        };
    }

    private function formatPhone(string $phone): string
    {
        $d = preg_replace('/\D+/', '', $phone);

        return match (AppSetting::get('sms.modem.phone_format', 'plus')) {
            'digits12' => $d,
            'digits9' => strlen($d) === 12 ? substr($d, 3) : $d,
            default => '+'.$d,
        };
    }

    /** @return array<string,mixed> qo'shimcha doimiy maydonlar (JSON) */
    private function extra(): array
    {
        $json = AppSetting::get('sms.modem.extra');
        $arr = $json ? json_decode((string) $json, true) : null;

        return is_array($arr) ? $arr : [];
    }
}
