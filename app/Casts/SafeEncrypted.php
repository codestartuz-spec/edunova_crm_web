<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * 'encrypted' cast bilan bir xil, lekin qiymat boshqa APP_KEY bilan shifrlangan (yoki buzilgan) bo'lsa
 * 500 xato bermaydi: null qaytaradi. Sahifalar ochiq qoladi, parolni qayta kiritish mumkin.
 */
class SafeEncrypted implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value === null || $value === '' ? null : Crypt::encryptString((string) $value);
    }
}
