<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * v14: global (filialga bog'liq bo'lmagan) sozlamalar: SMS modem, Telegram bot, VAPID kalitlari va h.k.
 * Maxfiy qiymatlar (token, parol) shifrlangan holda saqlanadi.
 */
class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'is_secret'];

    protected function casts(): array
    {
        return ['is_secret' => 'boolean'];
    }

    private const CACHE = 'app_settings.all';

    /** @return array<string,array{value:?string,secret:bool}> */
    private static function cached(): array
    {
        return Cache::remember(self::CACHE, 300, fn () => static::query()->get()->mapWithKeys(fn ($r) => [$r->key => ['value' => $r->value, 'secret' => $r->is_secret]])->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = self::cached()[$key] ?? null;
        if (! $row || $row['value'] === null || $row['value'] === '') {
            return $default;
        }

        if ($row['secret']) {
            try {
                return Crypt::decryptString($row['value']);
            } catch (\Throwable) {
                return $default;
            }
        }

        return $row['value'];
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $v = self::get($key);

        return $v === null ? $default : in_array((string) $v, ['1', 'true', 'on'], true);
    }

    public static function int(string $key, int $default = 0): int
    {
        $v = self::get($key);

        return $v === null ? $default : (int) $v;
    }

    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    public static function put(string $key, mixed $value, bool $secret = false): void
    {
        $stored = $value === null || $value === '' ? null : ($secret ? Crypt::encryptString((string) $value) : (is_bool($value) ? ($value ? '1' : '0') : (string) $value));

        static::updateOrCreate(['key' => $key], ['value' => $stored, 'is_secret' => $secret]);
        Cache::forget(self::CACHE);
    }

    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
        Cache::forget(self::CACHE);
    }
}
