<?php

namespace App\Support;

use App\Enums\Role;
use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Barcha direktorlar uchun BITTA umumiy ruxsatlar shabloni (global sozlama `director_permissions`).
 * sAdmin shablonni bir joyda belgilaydi; saqlanganda u barcha faol direktorlarga qo'llanadi,
 * yangi direktor va eski bazani ko'chirishdan keyingi direktorlar ham shu shablonni oladi.
 * Direktorning amaldagi ruxsatlari avvalgidek `user_permissions` da turadi (Gate/hasPermission o'zgarmaydi).
 */
class DirectorPermissions
{
    public const KEY = 'director_permissions';

    /** Saqlangan shablon (to'g'ri kalitlar). Hali saqlanmagan bo'lsa - bo'sh. */
    public static function keys(): array
    {
        $raw = AppSetting::get(self::KEY);
        $list = is_string($raw) ? json_decode($raw, true) : null;

        return is_array($list) ? self::clean($list) : [];
    }

    public static function isConfigured(): bool
    {
        return AppSetting::has(self::KEY);
    }

    /** Faqat direktorga berilishi mumkin bo'lgan, ro'yxatda bor kalitlar. */
    public static function clean(array $keys): array
    {
        return array_values(array_intersect(PermissionRegistry::forRole(Role::Director), array_map('strval', $keys)));
    }

    /** Shablon hali saqlanmagan bo'lsa: hozirgi direktorlarning umumiy (hammasida bor) ruxsatlari. */
    public static function commonOfExisting(): array
    {
        $sets = self::directors()->map(fn (User $d) => $d->permissionKeys())->all();
        if ($sets === []) {
            return [];
        }

        return self::clean(array_values(array_intersect(...$sets)));
    }

    /** Shablonni saqlaydi va barcha faol direktorlarga qo'llaydi. @return int ta'sirlangan direktorlar soni */
    public static function save(array $requested, ?User $actor = null): int
    {
        $old = self::keys();
        $keys = self::clean($requested);

        AppSetting::put(self::KEY, json_encode($keys));
        $count = self::applyToAll();

        AuditLog::record(
            'directors.permissions_template',
            null,
            "Direktorlarning umumiy ruxsatlari yangilandi ({$count} ta direktor)",
            ['removed' => array_values(array_diff($old, $keys))],
            ['added' => array_values(array_diff($keys, $old))],
        );

        return $count;
    }

    /** @return int direktorlar soni */
    public static function applyToAll(): int
    {
        $keys = self::keys();
        $directors = self::directors();
        foreach ($directors as $director) {
            $director->syncPermissions($keys);
        }

        return $directors->count();
    }

    public static function applyTo(User $director): void
    {
        $director->syncPermissions(self::keys());
    }

    private static function directors()
    {
        return User::where('role', Role::Director)->whereNull('archived_at')->orderBy('name')->get();
    }
}
