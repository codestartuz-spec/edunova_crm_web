<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\LegalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** v14: hujjatlarni o'qish (hamma uchun ochiq) va tizimga kirgan foydalanuvchining roziligi. */
class LegalController extends Controller
{
    public function show(string $type, LegalService $legal)
    {
        abort_unless(array_key_exists($type, LegalService::TYPES), 404);

        return view('legal.show', ['doc' => $legal->document($type), 'html' => LegalService::render($legal->document($type)->body), 'types' => LegalService::TYPES]);
    }

    public function consent(Request $request, LegalService $legal)
    {
        $pending = $legal->pending($request->user());

        if ($pending->isEmpty()) {
            return redirect()->intended(route('dashboard'));
        }

        // Oldin qabul qilgan, lekin hujjat yangilangan bo'lsa - boshqacha matn ko'rsatiladi
        $updated = \App\Models\LegalAcceptance::where('user_id', $request->user()->id)->whereIn('document_type', $pending->pluck('type'))->exists();

        return view('legal.consent', ['pending' => $pending, 'updated' => $updated]);
    }

    public function accept(Request $request, LegalService $legal): RedirectResponse
    {
        $user = $request->user();
        $pending = $legal->pending($user);
        $types = $pending->pluck('type')->all();

        $request->validate(
            ['agree' => ['required', 'array'], 'agree.*' => ['in:'.implode(',', $types)]],
            ['agree.required' => "Davom etish uchun barcha hujjatlarni qabul qilishingiz kerak."],
        );

        $agreed = (array) $request->input('agree');
        if (array_diff($types, $agreed)) {
            return back()->withErrors(['agree' => "Davom etish uchun barcha hujjatlarni qabul qilishingiz kerak."]);
        }

        $legal->accept($user, $request, $types);
        AuditLog::record('legal.accepted', $user, 'Huquqiy hujjatlar qabul qilindi: '.implode(', ', array_map(fn ($t) => LegalService::TYPES[$t], $types)));

        return redirect()->intended(route('dashboard'))
            ->withCookie(cookie(config('legal.cookie'), 'all', 60 * 24 * 365, '/', null, $request->isSecure(), false, false, 'Lax'));
    }
}
