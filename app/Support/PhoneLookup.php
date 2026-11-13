<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

/** v14: telefon raqami bo'yicha foydalanuvchilarni topish (raqam bazada turli ko'rinishda saqlangan bo'lishi mumkin). */
class PhoneLookup
{
    /** @return Collection<int, User> telefon yoki 2-telefon mos kelgan akkauntlar (filial filtrisiz) */
    public static function users(?string $phone, bool $onlyActive = false): Collection
    {
        $normalized = Format::normalizePhone($phone);
        if (! $normalized) {
            return collect();
        }

        $tail = substr($normalized, -4);

        return User::withoutGlobalScopes()->with('branch')
            ->where(fn ($q) => $q->where('phone', 'like', "%{$tail}%")->orWhere('phone2', 'like', "%{$tail}%"))
            ->when($onlyActive, fn ($q) => $q->whereNull('archived_at')->where('status', 'active'))
            ->get()
            ->filter(fn (User $u) => Format::normalizePhone($u->phone) === $normalized || Format::normalizePhone($u->phone2) === $normalized)
            ->values();
    }
}
