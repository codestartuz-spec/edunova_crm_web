<?php

namespace App\Support;

use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * v25: ochiq sahifalar uchun SEO teglari (title, description, canonical, hreflang, Open Graph, JSON-LD).
 * Panel va login ortidagi sahifalar bu yerdan o'tmaydi - ular `noindex` (SecurityHeaders) bilan yopiladi.
 */
class Seo
{
    public const LOCALES = ['uz' => 'uz_UZ', 'ru' => 'ru_RU'];

    /** Saytning asosiy manzili: APP_URL (https bo'lsa), aks holda joriy so'rov manzili. Oxirida "/" yo'q. */
    public static function base(): string
    {
        $configured = rtrim((string) config('app.url'), '/');
        $local = $configured === '' || preg_match('~^https?://(localhost|127\.|0\.0\.0\.0|\[::1\])~i', $configured);

        return $local ? rtrim(RequestFacade::root(), '/') : $configured;
    }

    /** Til bo'yicha landing manzili: uz - ildiz ("/"), ru - "/ru". */
    public static function landingUrl(string $locale): string
    {
        return self::base().($locale === 'ru' ? '/ru' : '/');
    }

    public static function ogImage(): string
    {
        return self::base().'/'.ltrim((string) config('seo.og_image'), '/');
    }

    /** hreflang juftliklari (x-default - o'zbekcha). */
    public static function alternates(): array
    {
        return [
            'uz' => self::landingUrl('uz'),
            'ru' => self::landingUrl('ru'),
            'x-default' => self::landingUrl('uz'),
        ];
    }

    /** schema.org JSON-LD grafi: tashkilot, sayt, dastur va FAQ. */
    public static function jsonLd(string $locale, array $content): array
    {
        $name = Brand::name();
        $url = self::landingUrl($locale);
        $logo = Brand::hasLogo() ? self::base().parse_url((string) Brand::logoUrl(), PHP_URL_PATH) : self::base().'/icons/icon-512.png';

        $graph = [
            [
                '@type' => 'Organization',
                '@id' => self::base().'/#organization',
                'name' => $name,
                'url' => self::base().'/',
                'logo' => ['@type' => 'ImageObject', 'url' => $logo, 'width' => 512, 'height' => 512],
            ],
            [
                '@type' => 'WebSite',
                '@id' => self::base().'/#website',
                'url' => self::base().'/',
                'name' => $name,
                'inLanguage' => ['uz', 'ru'],
                'publisher' => ['@id' => self::base().'/#organization'],
            ],
            [
                '@type' => 'SoftwareApplication',
                '@id' => $url.'#software',
                'name' => $name,
                'url' => $url,
                'description' => $content['meta']['description'],
                'applicationCategory' => 'BusinessApplication',
                'applicationSubCategory' => 'EducationApplication',
                'operatingSystem' => 'Web, Android, iOS (PWA)',
                'inLanguage' => $locale,
                'image' => self::ogImage(),
                'publisher' => ['@id' => self::base().'/#organization'],
                'featureList' => array_column($content['features'], 'title'),
            ],
            [
                '@type' => 'FAQPage',
                '@id' => $url.'#faq',
                'inLanguage' => $locale,
                'mainEntity' => array_map(fn ($f) => [
                    '@type' => 'Question',
                    'name' => $f['q'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                ], $content['faq']),
            ],
        ];

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }
}
