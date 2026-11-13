<?php

namespace App\Services;

use App\Jobs\SendSmsJob;
use App\Models\Branch;
use App\Models\GroupStudent;
use App\Models\SmsMessage;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Support\Format;
use App\Services\Sms\SmsDispatcher;
use App\Support\SmsTemplates;
use Illuminate\Support\Collection;

/**
 * SMS xizmati: shablonlar, navbat, ommaviy yuborish. Yuborish kanali - SmsDispatcher (USB modem, so'ng Eskiz).
 * Xabarlar avval bazaga yoziladi va navbat (queue) orqali yuboriladi.
 */
class SmsService
{
    /** v8 B9: bitta ommaviy yuborishda maksimal qabul qiluvchi soni. */
    public const BULK_LIMIT = 1000;

    /** @return array{body:string, enabled:bool} */
    public function template(Branch $branch, string $key): array
    {
        $default = SmsTemplates::all()[$key] ?? null;
        $row = SmsTemplate::withoutGlobalScopes()->where('branch_id', $branch->id)->where('key', $key)->first();

        return [
            'body' => $row->body ?? $default['body'] ?? '',
            'enabled' => $row ? $row->is_enabled : ($default['enabled'] ?? false),
        ];
    }

    public function render(string $body, array $vars): string
    {
        $map = [];
        foreach ($vars as $k => $v) {
            $map['{'.$k.'}'] = (string) $v;
        }

        return strtr($body, $map);
    }

    /**
     * Xabarni navbatga qo'yadi. Raqam noto'g'ri bo'lsa, "failed" holatida yoziladi.
     * $message tarixda/ro'yxatda ko'rinadigan matn (parol bo'lsa berkitilgan bo'lishi kerak);
     * $secret - agar berilsa, HAQIQIY (masalan, ochiq parolli) matn - faqat yuborish uchun ishlatiladi
     * va yuborilgach (muvaffaqiyatli yoki xato bo'lsa ham) darhol o'chiriladi (v8: SMS tarixida parol saqlanmaydi).
     */
    public function queue(Branch $branch, ?string $phone, string $message, ?string $templateKey = null, ?User $recipient = null, ?User $actor = null, ?string $secret = null): SmsMessage
    {
        $normalized = Format::normalizePhone($phone);

        $sms = SmsMessage::create([
            'branch_id' => $branch->id,
            'recipient_id' => $recipient?->id,
            'phone' => $normalized ?? Format::digits($phone),
            'message' => $message,
            'secret' => $normalized ? $secret : null,
            'template_key' => $templateKey,
            'status' => $normalized ? SmsMessage::QUEUED : SmsMessage::FAILED,
            'provider_response' => $normalized ? null : "Telefon raqami noto'g'ri",
            'created_by' => $actor?->id,
        ]);

        if ($normalized) {
            SendSmsJob::dispatch($sms->id);
        }

        return $sms;
    }

    /**
     * v8 B9: ommaviy yuborishdan OLDIN ko'rish — nechta o'quvchiga yetadi (telefon takrorlari va
     * noto'g'ri raqamlarsiz), birinchi qabul qiluvchiga qarab to'ldirilgan namuna matn va shu matnga
     * asoslangan taxminiy SMS segment soni (real narx tizimda yo'q, shuning uchun pul emas — segment
     * ko'rsatiladi: har 160 belgi — 1 segment).
     *
     * @return array{count:int, sample:?string, segments:int}
     */
    public function previewBulk(Branch $branch, string $audience, ?int $groupId, string $message): array
    {
        $seen = [];
        $count = 0;
        $sample = null;

        foreach ($this->bulkRecipients($audience, $groupId) as $student) {
            $phone = Format::normalizePhone($student->phone);
            if (! $phone || isset($seen[$phone])) {
                continue;
            }
            $seen[$phone] = true;
            $count++;

            if ($sample === null) {
                $sample = $this->render($message, $this->bulkVars($student, $branch));
            }

            if ($count >= self::BULK_LIMIT) {
                break;
            }
        }

        return ['count' => $count, 'sample' => $sample, 'segments' => $sample ? (int) ceil(mb_strlen($sample) / 160) : 0];
    }

    /**
     * v8 B9: haqiqiy ommaviy yuborish (tasdiqlangandan keyin). `previewBulk` bilan bir xil so'rov, lekin
     * ro'yxat tasdiqlash bilan yuborish payti orasida o'zgargan bo'lishi mumkin (masalan, o'quvchi to'lov
     * qilib qarzdorlikdan chiqishi mumkin) — shuning uchun HAQIQIY navbatga qo'yilgan son qaytariladi.
     */
    public function sendBulk(Branch $branch, string $audience, ?int $groupId, string $message, ?User $actor): int
    {
        $seen = [];
        $queued = 0;

        foreach ($this->bulkRecipients($audience, $groupId) as $student) {
            $phone = Format::normalizePhone($student->phone);
            if (! $phone || isset($seen[$phone])) {
                continue;
            }
            $seen[$phone] = true;

            $text = $this->render($message, $this->bulkVars($student, $branch));
            $this->queue($branch, $phone, $text, null, $student, $actor);

            if (++$queued >= self::BULK_LIMIT) {
                break;
            }
        }

        return $queued;
    }

    /** @return Collection<int, User> */
    private function bulkRecipients(string $audience, ?int $groupId): Collection
    {
        return User::visibleToContext()->where('role', 'student')->whereNull('archived_at')
            ->when($audience === 'debtors', fn ($q) => $q->where('balance', '<', 0))
            ->when($audience === 'group', fn ($q) => $q->whereIn('users.id', GroupStudent::where('group_id', $groupId)->where('is_active', true)->select('student_id')))
            ->get();
    }

    /** @return array<string,string> */
    private function bulkVars(User $student, Branch $branch): array
    {
        return [
            'name' => $student->name,
            'branch' => $branch->name,
            'balance' => Format::money($student->balance),
            'debt' => Format::money(max(0, -$student->balance)),
        ];
    }

    /**
     * Navbatdagi xabarni yuboradi (job chaqiradi): avval USB modem, kerak bo'lsa Eskiz (SmsDispatcher).
     * Sozlama xatolari qayta urinilmaydi, xabar "failed" bo'ladi; tarmoq xatolari job orqali qayta uriniladi.
     * `secret` bo'lsa o'shanday yuboriladi, keyin tozalanadi.
     */
    public function deliver(SmsMessage $sms): void
    {
        $text = $sms->secret ?? $sms->message;

        ['result' => $result, 'fallback' => $fallback] = app(SmsDispatcher::class)->send(Branch::findOrFail($sms->branch_id), $sms->phone, $text);

        $sms->update([
            'status' => $result->ok ? SmsMessage::SENT : SmsMessage::FAILED,
            'sent_at' => $result->ok ? now() : null,
            'provider_response' => $result->response,
            'provider' => $result->provider,
            'fallback_used' => $fallback,
            'secret' => null,
        ]);
    }
}
