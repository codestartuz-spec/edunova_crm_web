<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/** v14: "Parolni unutdingizmi?" — telefon → kod (Telegram/SMS) → (akkaunt tanlash) → yangi parol. */
class PasswordResetController extends Controller
{
    public function phone()
    {
        return view('auth.forgot.phone');
    }

    public function sendCode(Request $request, PasswordResetService $service): RedirectResponse
    {
        $data = $request->validate(['phone' => ['required', 'string', 'max:40']], [], ['phone' => 'Telefon raqami']);

        $service->request($data['phone']);
        $request->session()->put('pr', ['phone' => $data['phone'], 'at' => now()->timestamp]);

        // Hisob bor-yo'qligidan qat'i nazar bir xil javob
        return redirect()->route('password.code')->with('success', "Agar raqam tizimda mavjud bo'lsa, tasdiqlash kodi yuborildi.");
    }

    public function code(Request $request)
    {
        abort_unless($request->session()->has('pr.phone'), 404);

        return view('auth.forgot.code');
    }

    public function verify(Request $request, PasswordResetService $service): RedirectResponse
    {
        $pr = $request->session()->get('pr');
        abort_unless($pr, 404);

        $data = $request->validate(['code' => ['required', 'digits:6']], [], ['code' => 'Kod']);
        $accounts = $service->verify($pr['phone'], $data['code']);

        $request->session()->put('pr.accounts', $accounts->pluck('id')->all());
        $request->session()->put('pr.verified_at', now()->timestamp);

        if ($accounts->count() === 1) {
            $request->session()->put('pr.user', $accounts->first()->id);

            return redirect()->route('password.new');
        }

        return redirect()->route('password.choose');
    }

    public function choose(Request $request)
    {
        $accounts = $this->verifiedAccounts($request);
        abort_if($accounts === null, 404);

        return view('auth.forgot.choose', ['accounts' => User::withoutGlobalScopes()->with('branch')->whereIn('id', $accounts)->get()]);
    }

    public function pick(Request $request): RedirectResponse
    {
        $accounts = $this->verifiedAccounts($request);
        abort_if($accounts === null, 404);

        $id = (int) $request->input('user_id');
        abort_unless(in_array($id, $accounts, true), 403);

        $request->session()->put('pr.user', $id);

        return redirect()->route('password.new');
    }

    public function newPassword(Request $request)
    {
        abort_unless($this->selectedUser($request), 404);

        return view('auth.forgot.new');
    }

    public function update(Request $request, PasswordResetService $service): RedirectResponse
    {
        $user = $this->selectedUser($request);
        abort_unless($user, 404);

        $data = $request->validate(['password' => ['required', 'confirmed', Password::min(8)]], [], ['password' => 'Parol']);

        $service->reset($user, $data['password']);
        $request->session()->forget('pr');

        return redirect()->route('login')->with('success', "Parol o'zgartirildi. Yangi parol bilan kiring.");
    }

    private function verifiedAccounts(Request $request): ?array
    {
        $pr = $request->session()->get('pr');

        if (! $pr || empty($pr['accounts']) || ($pr['verified_at'] ?? 0) < now()->subMinutes(15)->timestamp) {
            return null;
        }

        return $pr['accounts'];
    }

    private function selectedUser(Request $request): ?User
    {
        $accounts = $this->verifiedAccounts($request);
        $id = (int) $request->session()->get('pr.user');

        return $accounts && in_array($id, $accounts, true) ? User::withoutGlobalScopes()->find($id) : null;
    }
}
