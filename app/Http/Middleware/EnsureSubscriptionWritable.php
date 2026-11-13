<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * v22: obunasi tugagan filial faqat-ko'rish rejimida: yozuvchi so'rovlar (POST/PUT/PATCH/DELETE) rad etiladi.
 * sAdmin va o'quvchilar uchun cheklov yo'q. Obuna, profil, chiqish va filial almashtirish ruxsat etiladi.
 */
class EnsureSubscriptionWritable
{
    private const ALLOWED = ['subscriptions.*', 'logout', 'branches.switch', 'profile.*', 'legal.accept', 'push.*'];

    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true) || ! $request->user() || $request->routeIs(...self::ALLOWED)) {
            return $next($request);
        }

        if ($branch = SubscriptionService::readOnlyBranchFor($request->user())) {
            $message = "«{$branch->name}» filiali obunasi tugagan: faqat ko'rish mumkin. Obunani uzaytiring.";

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : redirect()->back()->with('error', $message);
        }

        return $next($request);
    }
}
