<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/** v15: PWA - manifest, service worker (push + oflayn sahifa + statik fayllar keshi), oflayn sahifa. */
class PwaController extends Controller
{
    public function manifest()
    {
        $base = rtrim(url('/'), '/');

        return response()->json([
            'name' => \App\Support\Brand::name(),
            'short_name' => \App\Support\Brand::short(),
            'description' => "O'quv markazi boshqaruv tizimi",
            'start_url' => $base.'/login', // kirmagan foydalanuvchi login'ni ko'radi (landing emas); kirganni login panelga o'tkazadi
            'scope' => $base.'/',
            'display' => 'standalone',
            'orientation' => 'portrait-primary',
            'background_color' => '#ffffff',
            'theme_color' => config('app.pwa_theme', '#5d46e2'),
            'lang' => 'uz',
            'icons' => [
                ['src' => \App\Support\Brand::iconUrl(192), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => \App\Support\Brand::iconUrl(512), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => \App\Support\Brand::maskableUrl(), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ], 200, ['Content-Type' => 'application/manifest+json', 'Cache-Control' => 'public, max-age=3600']);
    }

    public function offline()
    {
        return response()->view('pwa.offline');
    }

    public function worker(): Response
    {
        $offline = json_encode(url('/offline'));
        $icon = json_encode(\App\Support\Brand::iconUrl(192));
        $title = json_encode(\App\Support\Brand::name(), JSON_UNESCAPED_UNICODE);
        $js = <<<JS
const CACHE = 'novacrm-static-v1';
const OFFLINE = {$offline};
self.addEventListener('install', e => {
    e.waitUntil(caches.open(CACHE).then(c => c.add(OFFLINE)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', e => {
    e.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', e => {
    const req = e.request;
    if (req.method !== 'GET') return;
    const url = new URL(req.url);
    if (url.origin !== location.origin) return;
    // Sahifalar (HTML) hech qachon keshlanmaydi: faqat tarmoq yo'q bo'lsa oflayn sahifa ko'rsatiladi
    if (req.mode === 'navigate') {
        e.respondWith(fetch(req).catch(() => caches.match(OFFLINE)));
        return;
    }
    // Faqat ochiq statik fayllar (build, ikonkalar, rasmlar) keshlanadi
    if (/\/(build|icons|images)\//.test(url.pathname) || url.pathname.endsWith('/favicon.svg')) {
        e.respondWith(caches.open(CACHE).then(async c => {
            const hit = await c.match(req);
            const net = fetch(req).then(r => { if (r.ok) c.put(req, r.clone()); return r; }).catch(() => hit);
            return hit || net;
        }));
    }
});
self.addEventListener('push', e => {
    let d = {};
    try { d = e.data.json(); } catch (_) {}
    e.waitUntil(self.registration.showNotification(d.title || {$title}, {body: d.body || '', icon: {$icon}, tag: 'n' + (d.id || ''), requireInteraction: !!d.important, data: {url: d.url || '/'}}));
});
self.addEventListener('notificationclick', e => {
    e.notification.close();
    e.waitUntil(clients.openWindow(e.notification.data.url || '/'));
});
JS;

        return response($js, 200, ['Content-Type' => 'application/javascript', 'Service-Worker-Allowed' => '/', 'Cache-Control' => 'no-cache']);
    }
}
