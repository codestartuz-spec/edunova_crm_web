<?php

namespace App\Support;

use App\Enums\Role;
use Illuminate\Support\Facades\Auth;

/**
 * Joriy so'rov qaysi filial ma'lumotlari bilan ishlashini aniqlaydi.
 *
 *  - admin / menejer / o'qituvchi / o'quvchi: doim o'z filiali;
 *  - sAdmin va direktor (veb): tanlangan filial (sessiyada), tanlanmagan bo'lsa barcha filiallar;
 *  - tizimga kirmagan holat (konsol, navbat): cheklov yo'q.
 */
class BranchContext
{
    public const SESSION_KEY = 'current_branch_id';

    /** Ma'lumotlar cheklanishi kerak bo'lgan filial ID si, yoki null (cheklovsiz). */
    public static function id(): ?int
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        if ($user->role->isGlobal()) {
            $allowed = self::restrictedIds($user);

            // v20: ba'zi filiallarga cheklangan direktor
            if ($allowed !== null) {
                if ($allowed === []) {
                    return 0;                       // hech qaysi filial berilmagan: hech narsa ko'rmaydi
                }

                if (count($allowed) === 1) {
                    return $allowed[0];             // bitta filial: u doim tanlangan
                }

                $wanted = (int) session(self::SESSION_KEY);

                return $wanted && in_array($wanted, $allowed, true) ? $wanted : null;
            }

            $id = session(self::SESSION_KEY);

            return $id ? (int) $id : null;
        }

        // v13: qo'shimcha filialli operator - o'ziga ruxsat berilgan filiallardan birini tanlay oladi
        if ($user->role === Role::Operator && $user->branch_id) {
            $extra = self::extraBranchIds($user);

            if ($extra !== []) {
                $wanted = (int) session(self::SESSION_KEY);

                if ($wanted > 0 && ($wanted === (int) $user->branch_id || in_array($wanted, $extra, true))) {
                    return $wanted;
                }
            }
        }

        return $user->branch_id ? (int) $user->branch_id : 0;
    }

    /**
     * v13: foydalanuvchiga (operator) berilgan faol qo'shimcha filial ID lari. Global scope bo'lmagan oddiy so'rov
     * (rekursiya bo'lmasligi uchun) va so'rov davomida bir marta keshlanadi.
     *
     * @return array<int, int>
     */
    public static function extraBranchIds(\App\Models\User $user): array
    {
        $key = 'extra_branch_ids_'.$user->id;
        $attrs = request()?->attributes;

        if ($attrs && $attrs->has($key)) {
            return $attrs->get($key);
        }

        $ids = \Illuminate\Support\Facades\DB::table('user_branches')
            ->join('branches', 'branches.id', '=', 'user_branches.branch_id')
            ->where('user_branches.user_id', $user->id)->where('branches.status', 'active')
            ->pluck('user_branches.branch_id')->map(fn ($v) => (int) $v)->all();

        $attrs?->set($key, $ids);

        return $ids;
    }

    /**
     * v20: direktor faqat ba'zi filiallarga ruxsat olgan bo'lsa - o'sha filial ID lari; cheklov yo'q bo'lsa (sAdmin,
     * barcha filialli direktor, boshqa rollar) null. So'rov davomida bir marta keshlanadi.
     *
     * @return array<int, int>|null
     */
    public static function restrictedIds(?\App\Models\User $user = null): ?array
    {
        $user ??= Auth::user();

        if (! $user || $user->role !== Role::Director || (bool) ($user->all_branches ?? true)) {
            return null;
        }

        $key = 'director_branch_ids_'.$user->id;
        $attrs = request()?->attributes;

        if ($attrs && $attrs->has($key)) {
            return $attrs->get($key);
        }

        $ids = \Illuminate\Support\Facades\DB::table('director_branches')->where('user_id', $user->id)
            ->pluck('branch_id')->map(fn ($v) => (int) $v)->all();

        $attrs?->set($key, $ids);

        return $ids;
    }

    /** v20: keshni tozalash (direktor filiallari shu so'rovda o'zgargan bo'lsa). */
    public static function forgetRestricted(\App\Models\User $user): void
    {
        request()?->attributes->remove('director_branch_ids_'.$user->id);
    }

    /** Foydalanuvchi ishlay oladigan barcha filial ID lari (o'zi + qo'shimcha). */
    public static function accessibleBranchIds(\App\Models\User $user): array
    {
        return array_values(array_unique(array_filter([(int) $user->branch_id, ...self::extraBranchIds($user)])));
    }

    /** Keshni tozalash (ruxsatlar shu so'rovda o'zgargan bo'lsa). */
    public static function forgetExtra(\App\Models\User $user): void
    {
        request()?->attributes->remove('extra_branch_ids_'.$user->id);
    }

    /** v20: `branches` jadvaliga `exists` qoidasi; cheklangan direktor uchun faqat o'ziga ruxsat berilgan filiallar. */
    public static function branchExists(bool $activeOnly = true): \Illuminate\Validation\Rules\Exists
    {
        $rule = \Illuminate\Validation\Rule::exists('branches', 'id');

        if ($activeOnly) {
            $rule->where('status', 'active');
        }

        $allowed = self::restrictedIds();

        return $allowed === null ? $rule : $rule->whereIn('id', $allowed ?: [0]);
    }

    /** Faqat joriy filialdagi yozuvni qabul qiladigan `exists` qoidasi. */
    public static function exists(string $table, string $column = 'id'): \Illuminate\Validation\Rules\Exists
    {
        return \Illuminate\Validation\Rule::exists($table, $column)->where('branch_id', self::id() ?: 0);
    }

    public static function select(?int $branchId): void
    {
        if ($branchId) {
            session([self::SESSION_KEY => $branchId]);
        } else {
            session()->forget(self::SESSION_KEY);
        }
    }
}
