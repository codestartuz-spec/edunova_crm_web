<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Support\LandingContent;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * v25: ochiq landing sahifasi va qidiruv tizimlari fayllari (robots.txt, sitemap.xml).
 * Landing "/" (o'zbekcha) va "/ru" (ruscha) manzillarida beriladi; "/" ni tizimga kirmagan mehmonlarga
 * ServeLanding middleware ko'rsatadi, kirganlar esa avvalgidek boshqaruv panelini ko'radi.
 */
class LandingController extends Controller
{
    public function show(Request $request, string $locale = 'uz'): Response
    {
        $locale = $locale === 'ru' ? 'ru' : 'uz';
        $content = LandingContent::get($locale);

        // SecurityHeaders: faqat shu sahifalar qidiruv tizimlariga ochiq (qolganlari avtomatik noindex)
        $request->attributes->set('seo.indexable', true);

        $html = view('landing.index', [
            'c' => $content,
            'locale' => $locale,
            'canonical' => Seo::landingUrl($locale),
            'alternates' => Seo::alternates(),
            'jsonLd' => Seo::jsonLd($locale, $content),
            'contact' => $this->contact(),
        ])->render();

        return response($html)->header('Content-Language', $locale);
    }

    public function ru(Request $request): Response
    {
        return $this->show($request, 'ru');
    }

    /** "/uz" ildiz sahifaga ko'chiriladi: bitta kanonik manzil bo'lsin. */
    public function uz()
    {
        return redirect('/', 301);
    }

    public function robots(): Response
    {
        // Ruxsat ro'yxati: faqat landing va uning fayllari ochiq, qolgan hamma narsa (panel, login, o'quvchi
        // kabineti, API) yopiq. Yangi ochiq sahifa (masalan, blog) qo'shilsa, shu yerga "Allow" qo'shing.
        $lines = [
            'User-agent: *',
            'Allow: /$',
            'Allow: /ru$',
            'Allow: /sitemap.xml',
            'Allow: /build/',
            'Allow: /images/',
            'Allow: /icons/',
            'Allow: /brand/',
            'Disallow: /',
            '',
            'Sitemap: '.Seo::base().'/sitemap.xml',
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8', 'Cache-Control' => 'public, max-age=3600']);
    }

    public function sitemap(): Response
    {
        $alt = Seo::alternates();
        $lastmod = date('Y-m-d', max(
            (int) @filemtime(resource_path('views/landing/index.blade.php')),
            (int) @filemtime(app_path('Support/LandingContent.php')),
        ) ?: time());

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach (['uz', 'ru'] as $loc) {
            $xml .= "  <url>\n    <loc>".e(Seo::landingUrl($loc))."</loc>\n    <lastmod>{$lastmod}</lastmod>\n    <changefreq>weekly</changefreq>\n    <priority>".($loc === 'uz' ? '1.0' : '0.9')."</priority>\n";
            foreach ($alt as $hreflang => $href) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="'.$hreflang.'" href="'.e($href).'"/>'."\n";
            }
            $xml .= "  </url>\n";
        }

        return response($xml.'</urlset>'."\n", 200, ['Content-Type' => 'application/xml; charset=UTF-8', 'Cache-Control' => 'public, max-age=3600']);
    }

    /** Aloqa ma'lumotlari: Brend sahifasida sAdmin kiritadi; bo'sh bo'lsa landingda ko'rsatilmaydi. */
    private function contact(): array
    {
        $tg = trim((string) AppSetting::get('landing.telegram', ''));
        $ig = trim((string) AppSetting::get('landing.instagram', ''));
        $phone = trim((string) AppSetting::get('landing.phone', ''));
        $email = trim((string) AppSetting::get('landing.email', ''));

        return array_filter([
            'telegram' => $tg !== '' ? ['label' => '@'.ltrim(preg_replace('~^https?://t\.me/~i', '', $tg), '@'), 'url' => str_starts_with($tg, 'http') ? $tg : 'https://t.me/'.ltrim($tg, '@')] : null,
            'instagram' => $ig !== '' ? ['label' => '@'.ltrim(preg_replace('~^https?://(www\.)?instagram\.com/~i', '', rtrim($ig, '/')), '@'), 'url' => str_starts_with($ig, 'http') ? $ig : 'https://instagram.com/'.ltrim($ig, '@')] : null,
            'phone' => $phone !== '' ? ['label' => $phone, 'url' => 'tel:'.preg_replace('/[^\d+]/', '', $phone)] : null,
            'email' => $email !== '' ? ['label' => $email, 'url' => 'mailto:'.$email] : null,
        ]);
    }
}
