<?php

namespace App\Support;

use App\Models\Branch;

/**
 * Filial rangi: tanlangan (yoki foydalanuvchining) filialida `brand_color` belgilangan bo'lsa, butun panel
 * (Tailwind `brand-*` ranglari CSS o'zgaruvchilari orqali) shu rangga o'tadi. Aks holda standart Edunova qizili.
 * Rang tanlovidan 50..950 shkalasi yasaladi; 600 darajasi oq matn bilan o'qiladigan (kontrast >= 4.5) qilib to'g'rilanadi.
 */
class BrandTheme
{
    private const LIGHT = [50 => .93, 100 => .86, 200 => .74, 300 => .55, 400 => .30, 500 => .12];

    private const DARK = [700 => .14, 800 => .28, 900 => .42, 950 => .62];

    /** Joriy so'rov uchun `:root { --brand-N: r g b; ... }` matni (standart rang bo'lsa bo'sh). */
    public static function css(): string
    {
        $hex = self::currentHex();

        if (! $hex) {
            return '';
        }

        $vars = '';
        foreach (self::palette($hex) as $step => [$r, $g, $b]) {
            $vars .= "--brand-{$step}:{$r} {$g} {$b};";
        }

        return ":root{{$vars}--brand-hex:{$hex};}";
    }

    public static function currentHex(): ?string
    {
        if (! auth()->check()) {
            return null;
        }

        $id = BranchContext::id();

        if (! $id) {
            return null;
        }

        static $cache = [];

        return $cache[$id] ??= self::valid(Branch::whereKey($id)->value('brand_color'));
    }

    /** @return array<int, array{0:int,1:int,2:int}> */
    public static function palette(string $hex): array
    {
        $base = self::rgb($hex);

        // 600 - tugma foni: oq matn bilan kontrast yetarli bo'lguncha qoraytiriladi
        for ($i = 0; $i < 24 && self::contrast($base, [255, 255, 255]) < 4.5; $i++) {
            $base = self::mix($base, [0, 0, 0], .06);
        }

        $scale = [600 => $base];
        foreach (self::LIGHT as $step => $t) {
            $scale[$step] = self::mix($base, [255, 255, 255], $t);
        }
        foreach (self::DARK as $step => $t) {
            $scale[$step] = self::mix($base, [0, 0, 0], $t);
        }
        ksort($scale);

        return $scale;
    }

    public static function valid(?string $hex): ?string
    {
        return $hex && preg_match('/^#[0-9a-fA-F]{6}$/', $hex) ? strtolower($hex) : null;
    }

    private static function rgb(string $hex): array
    {
        return [hexdec(substr($hex, 1, 2)), hexdec(substr($hex, 3, 2)), hexdec(substr($hex, 5, 2))];
    }

    private static function mix(array $c, array $to, float $t): array
    {
        return array_map(fn ($a, $b) => (int) round($a * (1 - $t) + $b * $t), $c, $to);
    }

    private static function luminance(array $c): float
    {
        [$r, $g, $b] = array_map(function ($v) {
            $v /= 255;

            return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        }, $c);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    private static function contrast(array $a, array $b): float
    {
        $l1 = self::luminance($a);
        $l2 = self::luminance($b);

        return (max($l1, $l2) + 0.05) / (min($l1, $l2) + 0.05);
    }
}
