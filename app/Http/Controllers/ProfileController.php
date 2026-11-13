<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Rules\UniquePhonePerRole;
use App\Rules\UzPhone;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $svc = app(SessionService::class);
        $available = $svc->available();

        return view('profile.edit', [
            'user' => $request->user()->load('branch'),
            'devices' => $available ? $svc->list($request->user(), $request->session()->getId()) : [],
            'devicesAvailable' => $available,
        ]);
    }

    public function revokeSession(Request $request, string $session): RedirectResponse
    {
        abort_if($session === $request->session()->getId(), 422);
        app(SessionService::class)->revoke($request->user(), $session);
        AuditLog::record('auth.session_revoked', $request->user(), 'Qurilmadan chiqardi');

        return back()->with('success', 'Qurilma chiqarildi.');
    }

    public function revokeOthers(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']], [], ['password' => 'Parol']);
        $n = app(SessionService::class)->revokeOthers($request->user(), $request->session()->getId());
        AuditLog::record('auth.sessions_revoked_others', $request->user(), "Boshqa qurilmalardan chiqdi ({$n})");

        return back()->with('success', 'Boshqa barcha qurilmalardan chiqildi.');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => [$user->isGlobal() ? 'nullable' : 'required', 'string', new UzPhone, new UniquePhonePerRole($user->branch_id, $user->role, $user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [], ['name' => 'F.I.O', 'phone' => 'Telefon', 'email' => 'Email']);

        // v14: o'quvchi faqat telefonini o'zgartira oladi (F.I.O va email xodimlar tomonidan boshqariladi)
        $user->update($user->isStudent() ? ['phone' => $data['phone']] : $data);

        return back()->with('success', "Ma'lumotlaringiz saqlandi.");
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ], [], ['current_password' => 'Joriy parol', 'password' => 'Yangi parol']);

        $user = $request->user();
        $user->update(['password' => $data['password']]);

        // v8 A5: parol o'zgarganda boshqa qurilmalardagi kirishlar bekor qilinadi va joriy
        // sessiya identifikatori yangilanadi (o'zi hozir chiqarilmaydi, faqat xavfsizlik uchun yangilanadi).
        $request->session()->regenerate();
        app(SessionService::class)->available() && app(SessionService::class)->revokeOthers($user, $request->session()->getId());

        AuditLog::record('auth.password_changed', $user, "Parolni o'zgartirdi");

        return back()->with('success', 'Parol yangilandi.');
    }
}
