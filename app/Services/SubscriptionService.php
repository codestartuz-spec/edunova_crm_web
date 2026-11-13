<?php

namespace App\Services;

use App\Enums\Role;
use App\Exceptions\SubscriptionExpiredException;
use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\BranchPayment;
use App\Models\SubscriptionRequest;
use App\Models\User;
use App\Services\Telegram\TelegramService;
use App\Support\Format;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** v22: filial obunasi: narxlar, faqat-ko'rish rejimi, so'rovni tasdiqlash/rad etish, Telegram xabarlari. */
class SubscriptionService
{
    public const KEYS = ['price_monthly', 'price_yearly', 'card_number', 'card_holder', 'terms', 'trial_days'];

    public const DEFAULT_TERMS = "1. Obuna narxi tanlangan tarifga qarab belgilanadi va barcha filiallar uchun bir xil.\n2. Summani ko'rsatilgan karta raqamiga o'tkazing va to'lov chekini rasm ko'rinishida yuklang.\n3. Chek administrator tomonidan tekshiriladi. Tasdiqlangach obuna avtomatik faollashadi yoki uzaytiriladi.\n4. Obuna tugagach filial faqat ko'rish rejimida qoladi (yangi ma'lumot kiritib bo'lmaydi).\n5. Noto'g'ri yoki o'qib bo'lmaydigan chek rad etiladi — qayta yuboring.";

    public static function setting(string $key, mixed $default = null): mixed
    {
        return AppSetting::get('subscription.'.$key, $default);
    }

    public static function trialDays(): int
    {
        try {
            return max(0, (int) self::setting('trial_days', 30));
        } catch (\Throwable) {
            return 30;
        }
    }

    public static function price(string $plan): int
    {
        return (int) self::setting('price_'.$plan, 0);
    }

    public static function priceConfigured(): bool
    {
        return self::price('monthly') > 0 && self::price('yearly') > 0 && filled(self::setting('card_number'));
    }

    public static function months(string $plan): int
    {
        return $plan === 'yearly' ? 12 : 1;
    }

    // ---- Faqat-ko'rish rejimi -------------------------------------------------------------------------------

    /** Joriy foydalanuvchi shu filialga yoza oladimi (sAdmin va o'quvchilar uchun cheklov yo'q). */
    public static function assertWritable(?int $branchId): void
    {
        $user = Auth::user();

        if (! $branchId || ! $user || $user->role === Role::SAdmin || $user->role === Role::Student) {
            return;
        }

        $branch = Branch::withoutGlobalScopes()->find($branchId, ['id', 'subscription_ends_on', 'subscription_exempt']);

        if ($branch && $branch->subscriptionExpired()) {
            throw new SubscriptionExpiredException;
        }
    }

    /** Foydalanuvchi ko'rayotgan filial faqat-ko'rish rejimidami (banner va middleware uchun). */
    public static function readOnlyBranchFor(?User $user): ?Branch
    {
        if (! $user || $user->role === Role::SAdmin || $user->role === Role::Student) {
            return null;
        }

        $id = \App\Support\BranchContext::id() ?: ($user->isGlobal() ? null : $user->branch_id);

        if (! $id) {
            return null;
        }

        $b = Branch::withoutGlobalScopes()->find($id);

        return $b && $b->subscriptionExpired() ? $b : null;
    }

    // ---- So'rovlar ------------------------------------------------------------------------------------------

    public function submit(Branch $branch, User $user, string $plan, UploadedFile $receipt, ?string $note): SubscriptionRequest
    {
        $path = $receipt->store('subscription-receipts', 'local');

        $req = SubscriptionRequest::create([
            'branch_id' => $branch->id,
            'user_id' => $user->id,
            'plan' => $plan,
            'amount' => self::price($plan),
            'receipt_path' => $path,
            'status' => SubscriptionRequest::PENDING,
            'note' => $note,
        ]);

        AuditLog::record('subscription.requested', $branch, "{$branch->name}: {$req->planLabel()} obuna uchun chek yuborildi — ".Format::money($req->amount), [], [], $branch->id);
        $this->notifyAdmins($req, $branch, $user);

        return $req;
    }

    /** Tasdiqlash: faol obunani tugash kunidan uzaytiradi, tugagan bo'lsa bugundan boshlaydi. Ikki marta tasdiqlanmaydi. */
    public function approve(SubscriptionRequest $req, User $by): SubscriptionRequest
    {
        return DB::transaction(function () use ($req, $by) {
            $req = SubscriptionRequest::lockForUpdate()->findOrFail($req->id);

            if (! $req->isPending()) {
                return $req;
            }

            $branch = Branch::withoutGlobalScopes()->lockForUpdate()->findOrFail($req->branch_id);
            $from = ($branch->subscription_ends_on && $branch->subscription_ends_on->startOfDay()->gt(today())) ? $branch->subscription_ends_on->startOfDay() : today();
            $newEnd = Carbon::parse($from)->addMonthsNoOverflow(self::months($req->plan))->startOfDay();

            $branch->subscription_ends_on = $newEnd->toDateString();
            $branch->subscription_exempt = false;
            $branch->save();

            $req->update(['status' => SubscriptionRequest::APPROVED, 'reviewed_by' => $by->id, 'reviewed_at' => now(), 'new_ends_on' => $newEnd->toDateString(), 'reject_reason' => null]);

            BranchPayment::create([
                'branch_id' => $branch->id, 'amount' => $req->amount, 'method' => BranchPayment::CARD,
                'note' => "Obuna ({$req->planLabel()}) — {$newEnd->format('d.m.Y')} gacha", 'created_by' => $by->id, 'created_at' => now(),
            ]);

            AuditLog::record('subscription.approved', $branch, "{$branch->name}: obuna tasdiqlandi — {$newEnd->format('d.m.Y')} gacha", [], [], $branch->id);

            $this->tellDirectors($branch, "✅ «{$branch->name}» filiali obunasi tasdiqlandi. Yangi muddat: {$newEnd->format('d.m.Y')}.");

            return $req;
        });
    }

    public function reject(SubscriptionRequest $req, User $by, string $reason): SubscriptionRequest
    {
        if (! $req->isPending()) {
            return $req;
        }

        $req->update(['status' => SubscriptionRequest::REJECTED, 'reviewed_by' => $by->id, 'reviewed_at' => now(), 'reject_reason' => $reason]);
        $branch = Branch::withoutGlobalScopes()->find($req->branch_id);
        AuditLog::record('subscription.rejected', $branch, "{$branch?->name}: obuna cheki rad etildi — {$reason}", [], [], $req->branch_id);
        $branch && $this->tellDirectors($branch, "❌ «{$branch->name}» obuna cheki rad etildi: {$reason}. Iltimos, to'g'ri chekni qayta yuboring.");

        return $req;
    }

    public function deleteReceipt(SubscriptionRequest $req): void
    {
        Storage::disk('local')->delete($req->receipt_path);
    }

    // ---- Telegram -------------------------------------------------------------------------------------------

    private function notifyAdmins(SubscriptionRequest $req, Branch $branch, User $user): void
    {
        try {
            $tg = app(TelegramService::class);
            if (! $tg->configured()) {
                return;
            }
            $text = "💳 Yangi obuna cheki\nFilial: {$branch->name}\nTarif: {$req->planLabel()}\nSumma: ".Format::money($req->amount)."\nYuborgan: {$user->name}\nSahifa: ".route('subscriptions.index');
            foreach ($tg->adminIds() as $id) {
                $tg->sendMessage($id, $text);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Filialni ko'ra oladigan direktorlarning Telegramiga xabar. */
    public function tellDirectors(Branch $branch, string $text): int
    {
        $sent = 0;

        try {
            $tg = app(TelegramService::class);
            if (! $tg->configured()) {
                return 0;
            }
            foreach ($this->directorsOf($branch) as $d) {
                if ($tg->sendMessage($d->telegram_chat_id, $text)) {
                    $sent++;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $sent;
    }

    /** @return \Illuminate\Support\Collection<int,User> Telegram ulangan, faol direktorlar (filialga ruxsati borlar) */
    public function directorsOf(Branch $branch)
    {
        return User::withoutGlobalScopes()->where('role', Role::Director)->where('status', 'active')->whereNotNull('telegram_chat_id')
            ->where(function ($q) use ($branch) {
                $q->where('all_branches', true)->orWhereIn('id', DB::table('director_branches')->where('branch_id', $branch->id)->pluck('user_id'));
            })->get();
    }

    /** Kunlik eslatma: 7/3/1 kun qolganda va tugagan kuni. Bir xil eslatma ikki marta yuborilmaydi. */
    public function sendReminders(): int
    {
        $count = 0;

        foreach (Branch::withoutGlobalScopes()->where('status', 'active')->where('subscription_exempt', false)->whereNotNull('subscription_ends_on')->get() as $b) {
            $left = $b->subscriptionDaysLeft();
            if ($left === null || ! in_array($left, [7, 3, 1, 0], true)) {
                continue;
            }
            $key = "subscription.reminded.{$b->id}.{$b->subscription_ends_on->toDateString()}.{$left}";
            if (\Illuminate\Support\Facades\Cache::has($key)) {
                continue;
            }
            $text = $left === 0
                ? "⛔ «{$b->name}» filiali obunasi bugun tugadi. Filial faqat ko'rish rejimida. Davom etish uchun obunani uzaytiring: ".route('subscriptions.index')
                : "⏰ «{$b->name}» filiali obunasi {$left} kundan keyin ({$b->subscription_ends_on->format('d.m.Y')}) tugaydi. Uzaytirish: ".route('subscriptions.index');
            $this->tellDirectors($b, $text);
            \Illuminate\Support\Facades\Cache::put($key, 1, now()->addDays(10));
            $count++;
        }

        return $count;
    }
}
