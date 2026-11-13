<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Certificate;

/** Ommaviy tekshiruv sahifasi (QR orqali): faqat ism, kurs, sana, filial va raqam. Telefon/manzil ko'rsatilmaydi. */
class CertificateVerifyController extends Controller
{
    public function show(string $code)
    {
        $cert = Certificate::withoutGlobalScopes()->where('code', strtoupper($code))->first();
        $branch = $cert ? Branch::withoutGlobalScopes()->find($cert->branch_id) : null;

        return response()->view('certificates.verify', ['c' => $cert, 'branch' => $branch], $cert ? 200 : 404)
            ->header('X-Robots-Tag', 'noindex, nofollow')->header('Cache-Control', 'no-store');
    }
}
