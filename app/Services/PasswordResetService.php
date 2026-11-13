<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\PasswordResetCode;
use App\Models\PasswordResetLog;
use App\Models\User;
use App\Services\Telegram\TelegramService;
use App\Support\Format;
use App\Support\PhoneLookup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * v14: telefon raqami orqali parolni tiklash. 6 xonali kod Telegram bot orqali (bog'langan bo'lsa),
 * aks holda SMS orqali yuboriladi. sAdmin kodi FAQAT bot sozlamalaridagi Admin ID larga boradi.
 * Hisob mavjud-yo'qligi oshkor qilinmaydi (har doim bir xil javob). Eski parol hech qachon yuborilmaydi.
 */
class PasswordResetService
{
    public const CODE_TTL_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    public function __construct(private TelegramService $tg, private SmsService $sms) {}

    /** Kod so'rash. Hech qachon xato tashlamaydi (anti-enumeration), faqat juda ko'p urinishda ValidationException. */
    public function request(string $phoneInput): void
    {
        $phone = Format::normalizePhone($phoneInput);

        $limiterKey = 'pwreset:'.($phone ?? sha1($phoneInput)).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($limiterKey, 3)) {
            PasswordResetLog::record('blocked', null, $phoneInput, null, 'Juda ko\'p so\'rov');
            throw ValidationException::withMessages(['phone' => "Juda ko'p urinish. 15 daqiqadan keyin qayta urinib ko'ring."]);
        }
        RateLimiter::hit($limiterKey, 900);

        if (! $phone) {
            PasswordResetLog::record('requested', null, $phoneInput, null, "Raqam formati noto'g'ri");

            return;
        }

        $users = PhoneLookup::users($phone, onlyActive: true)->filter(fn (User $u) => $u->isGlobal() || ($u->branch && $u->branch->isActive()));

        if ($users->isEmpty()) {
            PasswordResetLog::record('requested', null, $phoneInput, null, 'Akkaunt topilmadi');

            return;
        }

        // Bitta raqamga bir nechta akkaunt bo'lsa - bitta kod; keyin foydalanuvchi akkauntni tanlaydi.
        $code = (string) random_int(100000, 999999);
        $hash = Hash::make($code);
        $delivered = false;
        $channel = null;

        foreach ($users as $user) {
            PasswordResetCode::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);
            PasswordResetLog::record('requested', $user, $phoneInput, null);
        }

        // Yetkazish: sAdmin -> Admin ID lar; qolganlar -> o'z Telegram chati; bo'lmasa SMS.
        $chatIds = $users->map(fn (User $u) => $u->role === Role::SAdmin ? null : $u->telegram_chat_id)->filter()->unique();
        $hasSAdmin = $users->contains(fn (User $u) => $u->role === Role::SAdmin);
        if ($hasSAdmin) {
            $chatIds = $chatIds->merge($this->tg->adminIds())->unique();
        }

        $brand = \App\Support\Brand::name();
        $text = "🔐 {$brand}: parolni tiklash kodi <b>{$code}</b>\nKod ".self::CODE_TTL_MINUTES." daqiqa amal qiladi. Hech kimga aytmang. Siz so'ramagan bo'lsangiz, e'tibor bermang.";
        foreach ($chatIds as $chat) {
            if ($this->tg->sendMessage($chat, $text)) {
                $delivered = true;
                $channel = 'telegram';
            }
        }

        if (! $delivered) {
            $smsUser = $users->first(fn (User $u) => $u->role !== Role::SAdmin && $u->branch);
            if ($smsUser) {
                $plain = "{$brand}: parolni tiklash kodi {$code}. 10 daqiqa amal qiladi. Hech kimga aytmang.";
                $masked = $brand.': parolni tiklash kodi ••••••. 10 daqiqa amal qiladi.';
                $this->sms->queue($smsUser->branch, $phone, $masked, 'password_reset_otp', $smsUser, null, $plain);
                $delivered = true;
                $channel = 'sms';
            }
        }

        foreach ($users as $user) {
            PasswordResetCode::create(['user_id' => $user->id, 'code_hash' => $hash, 'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES), 'channel' => $channel ?? 'none']);
            PasswordResetLog::record($delivered ? 'code_sent' : 'failed_send', $user, $phoneInput, $channel, $delivered ? null : 'Telegram ulanmagan va SMS yuborib bo\'lmadi');
        }
    }

    /**
     * Kodni tekshiradi. @return Collection<int, User> kod mos kelgan akkauntlar (bo'sh bo'lsa - xato)
     *
     * @throws ValidationException
     */
    public function verify(string $phoneInput, string $code): Collection
    {
        $phone = Format::normalizePhone($phoneInput);
        $users = $phone ? PhoneLookup::users($phone, onlyActive: true) : collect();
        $fail = fn () => throw ValidationException::withMessages(['code' => "Kod noto'g'ri yoki muddati o'tgan."]);

        $matched = collect();
        $attempted = false;

        foreach ($users as $user) {
            $row = PasswordResetCode::where('user_id', $user->id)->whereNull('used_at')->where('expires_at', '>', now())->latest('id')->first();
            if (! $row || $row->attempts >= self::MAX_ATTEMPTS) {
                continue;
            }
            $attempted = true;

            if (Hash::check($code, $row->code_hash)) {
                $matched->push($user);
            } else {
                $row->increment('attempts');
            }
        }

        if ($matched->isEmpty()) {
            PasswordResetLog::record('code_wrong', $users->first(), $phoneInput, null, $attempted ? null : 'Faol kod yo\'q');
            $fail();
        }

        return $matched->values();
    }

    /** Yangi parolni o'rnatadi, kodni ishlatilgan deb belgilaydi, boshqa seanslarni tugatadi. */
    public function reset(User $user, string $newPassword): void
    {
        DB::transaction(function () use ($user, $newPassword) {
            PasswordResetCode::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);
            $user->forceFill(['password' => $newPassword, 'password_changed_at' => now(), 'remember_token' => Str::random(60)])->save();

            if (config('session.driver') === 'database' && Schema::hasTable(config('session.table', 'sessions'))) {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }
        });

        PasswordResetLog::record('reset_done', $user, $user->phone, null);
        AuditLog::record('auth.password_reset', $user, "Parol telefon/Telegram kodi orqali tiklandi: {$user->name}", branchId: $user->branch_id);

        $brand = \App\Support\Brand::name();
        $chats = $user->role === Role::SAdmin ? $this->tg->adminIds() : array_filter([$user->telegram_chat_id]);
        foreach ($chats as $chat) {
            $this->tg->sendMessage($chat, "✅ {$brand}: <b>{$user->name}</b> akkauntining paroli o'zgartirildi. Bu siz bo'lmasangiz, darhol administratorga xabar bering.");
        }
    }
}
