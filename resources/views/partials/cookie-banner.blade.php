{{-- Cookie roziligi (mehmonlar uchun). Tanlov «cookie_consent» cookie sida 1 yil saqlanadi. Tizimga kirganlarda esa rozilik sahifasi ishlaydi. --}}
<div id="cookie-banner" class="fixed inset-x-0 bottom-0 z-[90] hidden p-3 sm:p-4" role="dialog" aria-label="Cookie roziligi">
    <div class="mx-auto flex max-w-3xl flex-col gap-3 rounded-2xl border border-ink-100 bg-white p-4 shadow-2xl ring-1 ring-black/5 dark:border-ink-800 dark:bg-ink-900 sm:flex-row sm:items-center sm:gap-5 sm:p-5">
        <div class="flex min-w-0 flex-1 items-start gap-3">
            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950/60 dark:text-brand-300"><x-icon name="shield" class="h-5 w-5" /></span>
            <p class="text-sm leading-relaxed text-ink-600 dark:text-ink-300">
                Sayt to'g'ri ishlashi uchun zarur cookie lardan foydalanadi. Batafsil:
                <a href="{{ route('legal.show', 'cookies') }}" class="font-semibold text-brand-600 hover:underline">Cookie siyosati</a>,
                <a href="{{ route('legal.show', 'privacy') }}" class="font-semibold text-brand-600 hover:underline">Maxfiylik siyosati</a>.
            </p>
        </div>
        <div class="grid shrink-0 grid-cols-2 gap-2 sm:flex">
            <button type="button" data-cookie="necessary" class="btn-secondary justify-center">Faqat zarur</button>
            <button type="button" data-cookie="all" class="btn-primary justify-center">Qabul qilaman</button>
        </div>
    </div>
</div>
<script>
(function () {
    var name = 'cookie_consent', el = document.getElementById('cookie-banner');
    if (!el || document.cookie.split('; ').some(function (c) { return c.indexOf(name + '=') === 0; })) return;
    el.classList.remove('hidden');
    el.addEventListener('click', function (e) {
        var b = e.target.closest('[data-cookie]');
        if (!b) return;
        document.cookie = name + '=' + b.dataset.cookie + '; max-age=31536000; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
        el.classList.add('hidden');
    });
})();
</script>
