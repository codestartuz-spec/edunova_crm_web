<?php

namespace App\Services\Legacy;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;

/**
 * Eski bazadagi shifrlangan maydonlarni (masalan, branches.eskiz_password) hozirgi APP_KEY bilan
 * qayta shifrlaydi. Eski tizim kaliti .env dagi LEGACY_APP_KEY da turadi.
 */
class LegacySecrets
{
    private ?Encrypter $legacy = null;

    public function __construct()
    {
        $key = (string) config('services.legacy.app_key');
        if ($key !== '') {
            $raw = str_starts_with($key, 'base64:') ? base64_decode(substr($key, 7)) : $key;
            try {
                $this->legacy = new Encrypter($raw, config('app.cipher'));
            } catch (\Throwable) {
                $this->legacy = null; // noto'g'ri kalit formati: qiymat NULL qilinadi, ko'chirish to'xtamaydi
            }
        }
    }

    /**
     * @return array{0:?string,1:string} [hozirgi kalit bilan shifrlangan qiymat yoki null, holat: ok|reencrypted|plain|lost|empty]
     */
    public function normalize(?string $value): array
    {
        if ($value === null || $value === '') {
            return [null, 'empty'];
        }

        try { // allaqachon hozirgi kalit bilan shifrlangan
            Crypt::decryptString($value);

            return [$value, 'ok'];
        } catch (DecryptException) {
        }

        if ($this->legacy) { // eski kalit bilan shifrlangan
            try {
                return [Crypt::encryptString($this->legacy->decryptString($value)), 'reencrypted'];
            } catch (DecryptException) {
            }
        }

        // shifrlanmagan oddiy matn (Laravel shifrlangan qiymati base64 JSON: eyJ... bilan boshlanadi)
        if (! str_starts_with($value, 'eyJ')) {
            return [Crypt::encryptString($value), 'plain'];
        }

        return [null, 'lost']; // ochib bo'lmadi: NULL, parol sozlamalardan qayta kiritiladi
    }
}
