<?php

namespace App\Support;

use App\Models\AppSetting;

/**
 * v20: tizim brendi - loyiha nomi, shiori va logotiplar. Qiymatlar `app_settings` da (brand.*), fayllar
 * `storage/app/brand/` da turadi va `/brand/{fayl}` marshruti orqali beriladi. Sozlanmagan bo'lsa - avvalgi Edunova.
 */
class Brand
{
    public const DEFAULT_NAME = 'Edunova CRM';

    public const DEFAULT_SHORT = 'Edunova';

    public const DEFAULT_TAGLINE = "O'quv markazingizni bitta tizimdan boshqaring.";

    /** Qat'iy o'lchamlar (px): asosiy logotip kvadrat, gorizontal logotip 4:1. */
    public const LOGO_SIZE = [512, 512];

    public const WIDE_SIZE = [640, 160];

    public const MAX_KB = 1024;

    /** Asosiy logotipdan yasaladigan ikonkalar. */
    public const ICON_SIZES = [32, 180, 192, 512];

    public static function name(): string
    {
        return (string) AppSetting::get('brand.name', self::DEFAULT_NAME);
    }

    public static function short(): string
    {
        return (string) AppSetting::get('brand.short', self::DEFAULT_SHORT);
    }

    public static function tagline(): string
    {
        return (string) AppSetting::get('brand.tagline', self::DEFAULT_TAGLINE);
    }

    public static function initial(): string
    {
        return mb_strtoupper(mb_substr(trim(self::short()) ?: 'E', 0, 1));
    }

    public static function hasLogo(): bool
    {
        return AppSetting::get('brand.logo_v') !== null;
    }

    public static function hasWide(): bool
    {
        return AppSetting::get('brand.wide_v') !== null;
    }

    public static function path(string $file = ''): string
    {
        return storage_path('app/brand'.($file !== '' ? '/'.$file : ''));
    }

    private static function url(string $file, ?string $version): string
    {
        return route('brand.asset', ['file' => $file, 'v' => $version]);
    }

    public static function logoUrl(): ?string
    {
        return self::hasLogo() ? self::url('logo.png', AppSetting::get('brand.logo_v')) : null;
    }

    public static function wideUrl(): ?string
    {
        return self::hasWide() ? self::url('logo-wide.png', AppSetting::get('brand.wide_v')) : null;
    }

    /** Ikonka URL i: logotip yuklangan bo'lsa - undan yasalgani, aks holda avvalgi standart fayl. */
    public static function iconUrl(int $size): string
    {
        if (self::hasLogo()) {
            return self::url("icon-{$size}.png", AppSetting::get('brand.logo_v'));
        }

        return asset('icons/'.[180 => 'apple-touch-icon.png', 192 => 'icon-192.png', 512 => 'icon-512.png'][$size] ?? 'icon-192.png');
    }

    public static function maskableUrl(): string
    {
        return self::hasLogo() ? self::url('maskable-512.png', AppSetting::get('brand.logo_v')) : asset('icons/maskable-512.png');
    }

    public static function faviconUrl(): string
    {
        return self::hasLogo() ? self::url('icon-32.png', AppSetting::get('brand.logo_v')) : asset('favicon.svg');
    }
}
