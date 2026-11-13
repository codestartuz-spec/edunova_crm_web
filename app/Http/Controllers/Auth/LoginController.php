<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, AuthService $auth): RedirectResponse|View
    {
        $accounts = $auth->attempt($request->input('login'), $request->input('password'), $request);

        if ($accounts->count() > 1) {
            // Bir telefon + parolga bir nechta akkaunt mos: tanlash oynasi (parol allaqachon tekshirilgan).
            $request->session()->put('login_choice', [
                'ids' => $accounts->pluck('id')->all(),
                'remember' => $request->boolean('remember'),
                'expires' => now()->addMinutes(5)->timestamp,
            ]);

            return view('auth.choose-account', ['accounts' => $accounts]);
        }

        return $this->finish($request, $accounts->first(), $request->boolean('remember'));
    }

    public function choose(Request $request, AuthService $auth): RedirectResponse
    {
        $choice = $request->session()->pull('login_choice');

        if (! $choice || $choice['expires'] < now()->timestamp) {
            return redirect()->route('login')->withErrors(['login' => 'Vaqt tugadi. Qaytadan kiring.']);
        }

        $id = (int) $request->input('user_id');
        $user = in_array($id, $choice['ids'], true) ? User::with('branch')->find($id) : null;

        if (! $user || ! $auth->canLogin($user)) {
            return redirect()->route('login')->withErrors(['login' => 'Akkaunt tanlanmadi. Qaytadan kiring.']);
        }

        return $this->finish($request, $user, (bool) $choice['remember']);
    }

    private function finish(Request $request, User $user, bool $remember): RedirectResponse
    {
        Auth::login($user, $remember);
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();
        AuditLog::record('auth.login', $user, 'Tizimga kirdi');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        AuditLog::record('auth.logout', $request->user(), 'Tizimdan chiqdi');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
