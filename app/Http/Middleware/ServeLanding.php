<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LandingController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * v25: tizimga KIRMAGAN mehmon asosiy manzilni ("/") ochsa, login'ga yo'naltirish o'rniga ochiq landing ko'rsatiladi
 * (qidiruv tizimlari ham aynan shu sahifani ko'radi). Kirgan foydalanuvchi avvalgidek boshqaruv panelini oladi.
 * Web guruhining oxirida turadi: sessiya allaqachon boshlangan, "auth" esa undan keyin ishlaydi.
 */
class ServeLanding
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->path() === '/' && ! $request->user()) {
            return app(LandingController::class)->show($request, 'uz');
        }

        return $next($request);
    }
}
