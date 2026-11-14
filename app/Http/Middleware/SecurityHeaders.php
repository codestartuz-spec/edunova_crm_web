<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * v8 A5: barcha javoblarga asosiy xavfsizlik sarlavhalarini qo'shadi
 * (MIME-sniffing, clickjacking, ortiqcha referrer oqishi, kerak bo'lmagan brauzer ruxsatlari).
 * Diqqat: "Varonka" bo'limidagi ochiq ariza shakli (apply.*) boshqa saytlarga <iframe> orqali
 * joylashtiriladi - shu sahifalarda X-Frame-Options qo'yilmaydi, aks holda embed ishlamay qoladi.
 * HSTS faqat so'rov haqiqatan https bo'lganda qo'shiladi (http'da hali ishlamayotgan saytni
 * "faqat https" deb belgilab qo'ymaslik uchun) - trustProxies tufayli Nginx orqasida ham to'g'ri aniqlanadi.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content-Security-Policy: sahifa kodiga ta'sir qilmaydigan xavfsiz qismlar HAR DOIM qo'llanadi
        // (plaginlar, <base> almashtirish, boshqa saytga forma yuborish va iframe ichiga joylash taqiqlanadi).
        // To'liq (script/style) siyosat esa avval faqat HISOBOT rejimida - sahifalarni buzmaydi, brauzer konsolida ko'rinadi.
        $embeddable = $request->routeIs('apply.*');
        $enforced = ["object-src 'none'", "base-uri 'self'", "form-action 'self'"];
        if (! $embeddable) {
            $enforced[] = "frame-ancestors 'none'";
        }
        $response->headers->set('Content-Security-Policy', implode('; ', $enforced));
        $response->headers->set('Content-Security-Policy-Report-Only', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; font-src 'self' data:; connect-src 'self'; media-src 'self' blob:; frame-src 'self' https:");

        if (! $embeddable) {
            $response->headers->set('X-Frame-Options', 'DENY');
        }

        // v25: faqat landing (LandingController belgilaydi) qidiruv tizimlariga ochiq; qolgan hamma sahifa
        // (panel, login, o'quvchi kabineti, hujjatlar, ariza shakllari, xatolar) indekslanmaydi.
        // robots.txt va sitemap.xml'ga teg qo'yilmaydi - ularni qidiruv robotlari o'qiy olishi kerak.
        if (! $request->attributes->get('seo.indexable') && ! $request->routeIs('seo.*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=15552000; includeSubDomains');
        }

        return $response;
    }
}
