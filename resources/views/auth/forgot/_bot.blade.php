{{-- Telegram bot: foydalanuvchi nomi ko'rsatiladi, bosilsa bot avtomatik ochiladi (t.me havolasi; telefonda Telegram ilovasi, kompyuterda Telegram yoki veb) --}}
@php
    $bot = ltrim((string) \App\Models\AppSetting::get('telegram.username'), '@');
    if ($bot === '' && app(\App\Services\Telegram\TelegramService::class)->configured()) {
        // Token bor, lekin foydalanuvchi nomi saqlanmagan (eski sozlash): bir marta Telegramdan so'rab saqlaymiz
        $bot = (string) (app(\App\Services\Telegram\TelegramService::class)->call('getMe')['result']['username'] ?? '');
        if ($bot !== '') { \App\Models\AppSetting::put('telegram.username', $bot); }
    }
@endphp
<div class="mt-6 rounded-2xl border border-ink-100 bg-ink-50/60 p-4 dark:border-ink-800 dark:bg-ink-900/60">
    @if ($bot !== '')
        <a href="https://t.me/{{ $bot }}?start=parol" target="_blank" rel="noopener" class="group flex items-center gap-3 rounded-xl bg-[#229ED9] px-4 py-3 text-white shadow-sm transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#229ED9]/50">
            <svg class="h-7 w-7 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.8 15.3 9.6 19c.4 0 .6-.2.8-.4l1.9-1.8 3.9 2.9c.7.4 1.2.2 1.4-.7l2.6-12.1c.3-1.1-.4-1.6-1.1-1.3L3.9 11.4c-1.1.4-1.1 1-.2 1.3l3.9 1.2 9-5.7c.4-.3.8-.1.5.2z"/></svg>
            <span class="min-w-0 flex-1">
                <span class="block text-xs/4 opacity-90">Telegram bot</span>
                <span class="block truncate text-base font-bold">&#64;{{ $bot }}</span>
            </span>
            <span class="rounded-lg bg-white/20 px-2.5 py-1 text-xs font-semibold transition group-hover:bg-white/30">Botni ochish</span>
        </a>
    @else
        <p class="text-sm text-ink-500 dark:text-ink-400">Telegram bot hali ulanmagan. Administratorga murojaat qiling.</p>
    @endif
    <ol class="mt-3 space-y-1.5 text-xs text-ink-500 dark:text-ink-400">
        <li class="flex gap-2"><b class="text-brand-600">1.</b> Botni oching va <b>Start</b> tugmasini bosing.</li>
        <li class="flex gap-2"><b class="text-brand-600">2.</b> «📱 Telefon raqamini yuborish» tugmasini bosing (faqat bir marta).</li>
        <li class="flex gap-2"><b class="text-brand-600">3.</b> Shu sahifaga qaytib, raqamingizni kiriting — kod botga keladi.</li>
    </ol>
</div>
