<?php

/**
 * v25: qidiruv tizimlari (SEO) sozlamalari. Hammasi ixtiyoriy - .env orqali beriladi.
 * Tasdiqlash kodlari Google Search Console va Yandex Webmaster saytni tasdiqlashda beradi (meta teg usuli).
 */
return [
    'google_verification' => env('SEO_GOOGLE_VERIFICATION'),
    'yandex_verification' => env('SEO_YANDEX_VERIFICATION'),
    'bing_verification' => env('SEO_BING_VERIFICATION'),

    // Havola ulashilganda (Telegram, Instagram, Facebook) ko'rinadigan rasm: public/ ichidagi yo'l, 1200x630
    'og_image' => env('SEO_OG_IMAGE', 'images/og-edunova.png'),
];
