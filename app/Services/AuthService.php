<?php

namespace App\Services;

use App\Models\User;
use App\Support\Format;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Veb uchun umumiy kirish qoidalari. */
class AuthService
{
    private const MAX_ATTEMPTS = 5;

    /** Bir IP dan 15 daqiqada shuncha xato urinishdan keyin shu IP vaqtincha bloklanadi (turli loginlarni sinab ko'rishga qarshi). */
    private const MAX_IP_FAILURES = 30;

    /** Bitta login (akkaunt) uchun 15 daqiqada shuncha xato urinishdan keyin bloklanadi (turli IP lardan hujumga qarshi). */
    private const MAX_LOGIN_FAILURES = 15;

    private const LONG_WINDOW = 900;

    /**
     * v14: login (username / email / telefon raqami) va parol bo'yicha mos FAOL akkauntlarni qaytaradi.
     * Telefon bir nechta akkauntga tegishli bo'lishi mumkin (masalan, ota-ona va farzand) - parol
     * mos kelganlarining hammasi qaytadi, tanlovni controller (akkaunt tanlash oynasi) hal qiladi.
     *
     * @return \Illuminate\Support\Collection<int, User>
     *
     * @throws ValidationException
     */
    public function attempt(string $login, string $password, Request $request): \Illuminate\Support\Collection
    {
        $login = trim($login);
        $key = Str::lower($login).'|'.$request->ip();
        $ipKey = 'login-ip|'.$request->ip();
        $loginKey = 'login-user|'.Str::lower($login);

        $blockedKey = collect([
            [$key, self::MAX_ATTEMPTS],
            [$ipKey, self::MAX_IP_FAILURES],
            [$loginKey, self::MAX_LOGIN_FAILURES],
        ])->first(fn ($k) => RateLimiter::tooManyAttempts($k[0], $k[1]))[0] ?? null;

        if ($blockedKey !== null) {
            $seconds = RateLimiter::availableIn($blockedKey);

            throw ValidationException::withMessages([
                'login' => "Juda ko'p urinish. {$seconds} soniyadan keyin qayta urinib ko'ring.",
            ]);
        }

        $matched = $this->candidates($login)->filter(fn (User $u) => Hash::check($password, $u->password))->values();

        if ($matched->isEmpty()) {
            RateLimiter::hit($key, 60);
            RateLimiter::hit($ipKey, self::LONG_WINDOW);
            RateLimiter::hit($loginKey, self::LONG_WINDOW);

            throw ValidationException::withMessages(['login' => "Login yoki parol noto'g'ri."]);
        }

        RateLimiter::clear($key);
        RateLimiter::clear($loginKey);

        $usable = $matched->filter(fn (User $u) => $this->canLogin($u))->values();

        if ($usable->isEmpty()) {
            $user = $matched->first();
            $message = ! $user->isActive()
                ? "Akkauntingiz bloklangan. Administrator bilan bog'laning."
                : 'Sizning filialingiz yopilgan yoki biriktirilmagan.';

            throw ValidationException::withMessages(['login' => $message]);
        }

        return $usable;
    }

    /** Akkaunt tizimga kira oladimi (faol; global rollardan boshqasi uchun filiali ham faol). */
    public function canLogin(User $user): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        return $user->isGlobal() || ($user->branch && $user->branch->isActive());
    }

    /** Login qiymati bo'yicha nomzod akkauntlar: username/email, bo'lmasa telefon raqami. */
    private function candidates(string $login): \Illuminate\Support\Collection
    {
        $users = User::with('branch')
            ->where(fn ($q) => $q->where('username', $login)->orWhere('email', Str::lower($login)))
            ->get();

        if ($users->isNotEmpty()) {
            return $users;
        }

        return \App\Support\PhoneLookup::users($login);
    }
}
