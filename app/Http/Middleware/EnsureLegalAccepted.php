<?php

namespace App\Http\Middleware;

use App\Services\LegalService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** v14: hujjatlarning joriy versiyasini qabul qilmagan foydalanuvchi rozilik sahifasiga yo'naltiriladi. */
class EnsureLegalAccepted
{
    private const ALLOWED = ['legal.consent', 'legal.accept', 'legal.show', 'logout'];

    public function __construct(private LegalService $legal) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! LegalService::enforced() || $request->routeIs(...self::ALLOWED)) {
            return $next($request);
        }

        if ($this->legal->hasAcceptedAll($user, $request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Davom etish uchun foydalanish shartlarini qabul qiling.', 'redirect' => route('legal.consent')], 428);
        }

        if ($request->isMethod('GET')) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return redirect()->route('legal.consent');
    }
}
