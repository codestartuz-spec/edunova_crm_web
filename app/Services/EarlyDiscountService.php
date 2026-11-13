<?php

namespace App\Services;

use App\Models\BalanceTransaction;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;

/**
 * Oldindan to'lov chegirmasi (avtomatik). Har bir to'lovdan va guruhga qo'shilgandan keyin chaqiriladi,
 * shuning uchun bir nechta kichik to'lov ham hisobga olinadi.
 *
 * Chegirma faqat BIR HOLATDA beriladi: o'quvchining HOZIRGI balansida shu guruh uchun `narx − chegirma`
 * yetarli bo'lsa VA bugun chegirma muddati (DiscountWindow: boshlanishidan `oldin` kun oldin ... `keyin` kun keyin)
 * ichida bo'lsa. Muddat tugagan bo'lsa chegirma HECH QACHON berilmaydi (to'lov qancha bo'lmasin).
 *
 * "Yetarli mablag'" tarixiy to'lovlar yig'indisi bilan emas, balansning o'zi bilan o'lchanadi: balansga
 * qo'shilgan guruhlar narxi allaqachon yechilgan, shuning uchun
 *     bo'sh mablag' = balans + (chegirma kutayotgan guruhlar narxi yig'indisi)
 * Bu eski guruhga sarflangan, qaytarilgan yoki storno qilingan pulni qayta hisoblashga yo'l qo'ymaydi.
 * Bir nechta guruh bo'lsa, guruhlar boshlanish sanasi bo'yicha tartiblanib, talab qilingan summalar yig'ilib boriladi
 * (bitta pul ikki guruhga ikki marta hisoblanmaydi).
 * Bir o'quvchiga bir guruh uchun chegirma faqat bir marta beriladi.
 */
class EarlyDiscountService
{
    public function __construct(private BalanceService $balance, private DiscountWindow $window) {}

    /**
     * @return array<int, Payment> berilgan chegirmalar
     */
    public function grantDue(User $student, User $actor): array
    {
        // Faqat: faol a'zolik + chegirma bor + bugun muddat ichida + hali chegirma olmagan guruhlar
        $pending = GroupStudent::where('student_id', $student->id)->where('is_active', true)
            ->with('group')->get()->pluck('group')->filter()
            ->filter(fn (Group $g) => $g->early_discount > 0 && $this->window->allows($g))
            ->reject(fn (Group $g) => $this->hasDiscount($student, $g))
            ->sortBy([['starts_on', 'asc'], ['id', 'asc']])
            ->values();

        $granted = [];
        $cumulative = 0;

        foreach ($pending as $group) {
            $cumulative += $this->need($group);

            $balance = (int) User::whereKey($student->id)->value('balance');

            // Hali chegirma olmagan guruhlar narxi balansdan yechilgan: o'sha pul shu guruhlar uchun "bo'sh" hisoblanadi
            $stillPending = $pending->reject(fn (Group $g) => isset($granted[$g->id]));
            $available = $balance + (int) $stillPending->sum('price');

            if ($available < $cumulative) {
                continue;
            }

            $granted[$group->id] = $this->grant($student, $group, $group->early_discount, "Oldindan to'lov uchun chegirma", BalanceTransaction::DISCOUNT, $actor);
            $cumulative -= $this->need($group);
        }

        return array_values($granted);
    }

    /** Chegirmadan keyin o'quvchi shu guruh uchun to'lashi kerak bo'lgan summa. */
    private function need(Group $group): int
    {
        return max(0, (int) $group->price - (int) $group->early_discount);
    }

    /** Chegirmani to'lovlar jadvaliga va o'quvchi balansiga yozadi. */
    public function grant(User $student, Group $group, int $amount, string $note, string $balanceType, User $actor): Payment
    {
        $payment = Payment::create([
            'branch_id' => $student->branch_id, 'student_id' => $student->id, 'group_id' => $group->id, 'type' => Payment::DISCOUNT,
            'amount' => $amount, 'description' => $note, 'created_by' => $actor->id,
        ]);

        $this->balance->post($student, $amount, $balanceType, $group, $note, $actor, $payment);

        return $payment;
    }

    public function hasDiscount(User $student, Group $group): bool
    {
        return Payment::where('student_id', $student->id)->where('group_id', $group->id)->where('type', Payment::DISCOUNT)->whereNull('reversed_at')->exists();
    }

    /**
     * Ma'lumot uchun (hisobot/tekshiruv): chegirma qarori BU summaga EMAS, joriy balansga asoslanadi ({@see grantDue()}).
     * Chegirma muddati boshlanganidan beri sof to'langan summa (to'lovlar − qaytarishlar).
     * Storno qilingan to'lovlar "to'langan" summaga kirmaydi; rad etilgan (pul qaytarilgan) qaytarishlar
     * esa "qaytarilgan" summaga kirmaydi — ikkalasi ham "go'yo bo'lmagandek" hisoblanadi.
     */
    public function paidSinceWindowOpened(User $student, Group $group): int
    {
        [$before] = $this->window->days($group->branch_id);
        $from = CarbonImmutable::parse($group->starts_on)->subDays($before)->startOfDay();

        $sums = Payment::where('student_id', $student->id)->where('created_at', '>=', $from)
            ->whereIn('type', [Payment::PAYMENT, Payment::REFUND])
            ->selectRaw("
                coalesce(sum(case when type = 'payment' and reversed_at is null then amount end), 0) as paid,
                coalesce(sum(case when type = 'refund' and refund_rejected_at is null then amount end), 0) as refunded
            ")
            ->first();

        return (int) $sums->paid - (int) $sums->refunded;
    }
}
