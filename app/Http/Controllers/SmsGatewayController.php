<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Services\Sms\SmsDispatcher;
use App\Services\Sms\UsbModemGateway;
use App\Support\Format;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** v14: USB modem SMS shlyuzi sozlamalari (sms.gateway ruxsati: sAdmin va unga ruxsat berilgan direktor). */
class SmsGatewayController extends Controller
{
    public function edit(SmsDispatcher $dispatcher)
    {
        $this->authorize('sms.gateway');

        $keys = ['enabled', 'base_url', 'path', 'method', 'body_format', 'auth_type', 'auth_header', 'basic_user', 'phone_field', 'message_field', 'phone_format', 'extra', 'success_field', 'success_value', 'timeout'];
        $v = [];
        foreach ($keys as $k) {
            $v[$k] = AppSetting::get("sms.modem.$k");
        }

        return view('sms.gateway', [
            'v' => $v,
            'hasSecret' => AppSetting::has('sms.modem.auth_value'),
            'used' => $dispatcher->usedToday(),
            'breaker' => $dispatcher->breakerOpen(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('sms.gateway');

        $d = $request->validate([
            'base_url' => ['nullable', 'url', 'max:255'],
            'path' => ['nullable', 'string', 'max:255'],
            'method' => ['required', 'in:post,get'],
            'body_format' => ['required', 'in:json,form'],
            'auth_type' => ['required', 'in:none,bearer,header,basic'],
            'auth_header' => ['nullable', 'string', 'max:60'],
            'auth_value' => ['nullable', 'string', 'max:500'],
            'basic_user' => ['nullable', 'string', 'max:120'],
            'phone_field' => ['required', 'string', 'max:40'],
            'message_field' => ['required', 'string', 'max:40'],
            'phone_format' => ['required', 'in:plus,digits12,digits9'],
            'extra' => ['nullable', 'json', 'max:2000'],
            'success_field' => ['nullable', 'string', 'max:60'],
            'success_value' => ['nullable', 'string', 'max:40'],
            'timeout' => ['required', 'integer', 'min:3', 'max:60'],
        ], [], ['base_url' => 'Shlyuz manzili', 'extra' => "Qo'shimcha maydonlar (JSON)"]);

        AppSetting::put('sms.modem.enabled', $request->boolean('enabled'));
        foreach (['base_url', 'path', 'method', 'body_format', 'auth_type', 'auth_header', 'basic_user', 'phone_field', 'message_field', 'phone_format', 'extra', 'success_field', 'success_value', 'timeout'] as $k) {
            AppSetting::put("sms.modem.$k", $d[$k] ?? null);
        }
        // Maxfiy qiymat: bo'sh qoldirilsa eskisi saqlanadi
        if (filled($d['auth_value'] ?? null)) {
            AppSetting::put('sms.modem.auth_value', $d['auth_value'], true);
        }
        if ($request->boolean('clear_secret')) {
            AppSetting::forget('sms.modem.auth_value');
        }

        AuditLog::record('sms.gateway_updated', null, 'USB modem SMS shlyuzi sozlamalari yangilandi');

        return back()->with('success', 'Shlyuz sozlamalari saqlandi.');
    }

    /** Bitta tekshiruv tugmasi: ko'rsatilgan raqamga sinov SMS yuboradi va shlyuz javobini ko'rsatadi. */
    public function test(Request $request, UsbModemGateway $modem): RedirectResponse
    {
        $this->authorize('sms.gateway');

        $data = $request->validate(['test_phone' => ['required', 'string']], [], ['test_phone' => 'Sinov raqami']);
        $phone = Format::normalizePhone($data['test_phone']);
        abort_unless($phone, 422, "Telefon raqami noto'g'ri.");

        if (blank(AppSetting::get('sms.modem.base_url'))) {
            return back()->with('error', 'Avval shlyuz manzilini saqlang.');
        }

        try {
            $r = $modem->send($phone, \App\Support\Brand::name().': USB modem sinov xabari '.now('Asia/Tashkent')->format('H:i'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Shlyuzga ulanib bo\'lmadi: '.$e->getMessage());
        }

        AuditLog::record('sms.gateway_tested', null, 'USB modem sinovi: '.($r->ok ? 'muvaffaqiyatli' : 'xato'));

        return back()->with($r->ok ? 'success' : 'error', ($r->ok ? 'Sinov SMS yuborildi. ' : 'Shlyuz xato qaytardi. ').'Javob: '.$r->response);
    }
}
