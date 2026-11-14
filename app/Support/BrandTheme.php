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

        // v28: filial rangiga uyg'un ikkinchi (urg'u) rang va grafik ranglari - butun panel bir uyg'unlikda bo'ladi
        [$a4, $a5] = self::accent($hex);
        $pal = self::palette($hex);
        $vars .= '--accent-400:'.implode(' ', $a4).';--accent-500:'.implode(' ', $a5).';';
        $vars .= '--viz-s1:rgb('.implode(' ', $pal[600]).');--viz-f1:rgb('.implode(' ', $pal[300]).');--viz-f2:rgb('.implode(' ', $pal[400]).');--viz-f3:rgb('.implode(' ', $pal[600]).');--viz-f4:rgb('.implode(' ', $pal[800]).');';
        $dark = '--viz-s1:rgb('.implode(' ', $pal[400]).');--viz-f1:rgb('.implode(' ', $pal[600]).');--viz-f2:rgb('.implode(' ', $pal[400]).');--viz-f3:rgb('.implode(' ', $pal[300]).');--viz-f4:rgb('.implode(' ', $pal[100]).');';

        return ":root{{$vars}--brand-hex:{$hex};}.dark{{$dark}}";
    }

    /**
     * Brend rangiga mos keladigan ikkinchi (urg'u) rang: rang doirasida ~150 gradus (qizil/to'q sariq brendda ~190) siljitiladi.
     *
     * @return array{0:array{0:int,1:int,2:int},1:array{0:int,1:int,2:int}}
     */
    public static function accent(string $hex): array
    {
        [$r, $g, $b] = self::rgb($hex);
        [$h] = self::toHsl($r, $g, $b);
        $h = ($h >= 330 || $h <= 30) ? $h + 190 : $h + 150;

        return [self::fromHsl(fmod($h, 360), .88, .64), self::fromHsl(fmod($h, 360), .90, .55)];
    }

    /** @return array{0:float,1:float,2:float} h(0-360), s, l */
    private static function toHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        if ($max === $min) {
            return [0.0, 0.0, $l];
        }
        $d = $max - $min;
        $s = $l > .5 ? $d / (2 - $max - $min) : $d / ($max + $min);
        $h = match ($max) {
            $r => (($g - $b) / $d + ($g < $b ? 6 : 0)),
            $g => (($b - $r) / $d + 2),
            default => (($r - $g) / $d + 4),
        } * 60;

        return [$h, $s, $l];
    }

    /** @return array{0:int,1:int,2:int} */
    private static function fromHsl(float $h, float $s, float $l): array
    {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;
        [$r, $g, $b] = match (true) {
            $h < 60 => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        return [(int) round(($r + $m) * 255), (int) round(($g + $m) * 255), (int) round(($b + $m) * 255)];
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

        // So'rov doirasida keshlanadi (static emas: uzoq yashaydigan jarayon va testlarda eski rang qolib ketmasin)
        $attrs = request()->attributes;
        $key = 'brand_theme.hex.'.$id;
        if (! $attrs->has($key)) {
            $attrs->set($key, self::valid(Branch::whereKey($id)->value('brand_color')));
        }

        return $attrs->get($key);
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
