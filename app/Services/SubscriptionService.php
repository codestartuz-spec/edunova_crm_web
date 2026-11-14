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
        $path = $this->storeReceipt($receipt);

        try {
            $req = SubscriptionRequest::create([
                'branch_id' => $branch->id,
                'user_id' => $user->id,
                'plan' => $plan,
                'amount' => self::price($plan),
                'receipt_path' => $path,
                'status' => SubscriptionRequest::PENDING,
                'note' => $note,
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            // Poyga holati: shu filial uchun chek allaqachon yuborilgan (baza kafolati)
            Storage::disk('local')->delete($path);

            throw new \RuntimeException('Bu filial uchun chek allaqachon tekshiruvda. Natijani kuting.');
        }

        AuditLog::record('subscription.requested', $branch, "{$branch->name}: {$req->planLabel()} obuna uchun chek yuborildi — ".Format::money($req->amount), [], [], $branch->id);
        $this->notifyAdmins($req, $branch, $user);

        return $req;
    }

    /**
     * Chek rasmini kichraytirib (eng uzun tomoni 1600 px) JPEG qilib saqlaydi: hajmi ~200–400 KB bo'ladi (zaxiraga
     * sig'adi) va rasm faylining "yashirin" tarkibi (metadata) olib tashlanadi. GD bo'lmasa asl fayl saqlanadi.
     */
    private function storeReceipt(UploadedFile $file): string
    {
        $dir = 'subscription-receipts';

        if (function_exists('imagecreatefromstring') && ($img = @imagecreatefromstring((string) file_get_contents($file->getRealPath())))) {
            $w = imagesx($img);
            $h = imagesy($img);
            $scale = min(1.0, 1600 / max($w, $h));
            if ($scale < 1.0) {
                $img = imagescale($img, max(1, (int) round($w * $scale)), max(1, (int) round($h * $scale))) ?: $img;
            }
            // shaffof PNG ni oq fonga qo'yamiz
            $canvas = imagecreatetruecolor(imagesx($img), imagesy($img));
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
            imagecopy($canvas, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
            ob_start();
            imagejpeg($canvas, null, 82);
            $bytes = (string) ob_get_clean();

            if ($bytes !== '') {
                $path = $dir.'/'.\Illuminate\Support\Str::uuid().'.jpg';
                Storage::disk('local')->put($path, $bytes);

                return $path;
            }
        }

        return $file->store($dir, 'local');
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

            AuditLog::record('subscription.approved', $branch, "{$branch->name}: obuna tasdiqlandi — {$newEnd->copy()->subDay()->format('d.m.Y')} kunigacha", [], [], $branch->id);

            $this->tellDirectors($branch, "✅ «{$branch->name}» filiali obunasi tasdiqlandi. Yangi muddat: {$newEnd->copy()->subDay()->format('d.m.Y')} kunigacha.");

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

    /** Filial o'chirilganda uning chek rasmlarini ham diskdan o'chiradi. */
    public static function deleteReceiptsOf(array $paths): void
    {
        foreach (array_filter($paths) as $p) {
            Storage::disk('local')->delete($p);
        }
    }

    // ---- Telegram -------------------------------------------------------------------------------------------

    private function notifyAdmins(SubscriptionRequest $req, Branch $branch, User $user): void
    {
        try {
            $tg = app(TelegramService::class);
            if (! $tg->configured()) {
                return;
            }
            $caption = "💳 Yangi obuna cheki\nFilial: {$branch->name}\nTarif: {$req->planLabel()}\nSumma: ".Format::money($req->amount)."\nYuborgan: {$user->name}\nTekshirish: ".route('subscriptions.index');
            $file = Storage::disk('local')->path($req->receipt_path);

            foreach ($tg->adminIds() as $id) {
                // chek rasmi bilan birga (sAdmin Telegramdan ham ko'ra oladi); rasm yuborilmasa oddiy xabar
                $sent = is_file($file) ? $tg->sendDocument($id, $file, $caption, 'chek.jpg') : ['ok' => false];
                if (! ($sent['ok'] ?? false)) {
                    $tg->sendMessage($id, e($caption));
                }
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
                if ($tg->sendMessage($d->telegram_chat_id, e($text))) {
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
                : "⏰ «{$b->name}» filiali obunasi {$left} kundan keyin (oxirgi kun: {$b->subscriptionLastDay()->format('d.m.Y')}) tugaydi. Uzaytirish: ".route('subscriptions.index');
            $this->tellDirectors($b, $text);
            \Illuminate\Support\Facades\Cache::put($key, 1, now()->addDays(10));
            $count++;
        }

        return $count;
    }
}
