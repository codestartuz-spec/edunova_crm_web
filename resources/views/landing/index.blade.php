<!DOCTYPE html>
<html lang="{{ $c['lang'] }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.seo-head', ['title' => $c['meta']['title'], 'description' => $c['meta']['description'], 'canonical' => $canonical, 'locale' => $locale, 'alternates' => $alternates, 'jsonLd' => $jsonLd])
    @include('partials.pwa-head')
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white text-ink-800 antialiased dark:bg-ink-950 dark:text-ink-100" x-data="{ menu: false }" @keydown.escape.window="menu = false">

{{-- Yuqori panel --}}
<header class="sticky top-0 z-40 border-b border-ink-100/80 bg-white/85 backdrop-blur-xl dark:border-ink-800/80 dark:bg-ink-950/85">
    <div class="mx-auto flex h-16 max-w-6xl items-center gap-3 px-4 sm:px-6">
        <a href="{{ $canonical }}" class="flex items-center gap-2.5" aria-label="{{ \App\Support\Brand::name() }}">
            <x-logo class="h-9 w-9" />
            <span class="text-lg font-extrabold tracking-tight text-ink-900 dark:text-white">{{ \App\Support\Brand::short() }}</span>
        </a>
        <nav class="ml-8 hidden items-center gap-1 lg:flex" aria-label="{{ $c['aria']['menu'] }}">
            @foreach (['features', 'roles', 'how', 'faq'] as $k)
                <a href="#{{ $k }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-ink-600 transition hover:bg-ink-50 hover:text-brand-600 dark:text-ink-300 dark:hover:bg-ink-900">{{ $c['nav'][$k] }}</a>
            @endforeach
        </nav>
        <div class="ml-auto flex items-center gap-2">
            <div class="flex overflow-hidden rounded-xl text-xs font-bold ring-1 ring-ink-200 dark:ring-ink-700" role="group" aria-label="{{ $c['footer']['lang'] }}">
                <a href="{{ $alternates['uz'] }}" hreflang="uz" lang="uz" class="px-3 py-2 {{ $locale === 'uz' ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-ink-50 dark:text-ink-300 dark:hover:bg-ink-900' }}">UZ</a>
                <a href="{{ $alternates['ru'] }}" hreflang="ru" lang="ru" class="px-3 py-2 {{ $locale === 'ru' ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-ink-50 dark:text-ink-300 dark:hover:bg-ink-900' }}">RU</a>
            </div>
            <button type="button" class="btn-ghost h-10 w-10 !p-0" @click="$store.theme.toggle()" aria-label="{{ $c['aria']['theme'] }}">
                <x-icon name="moon" class="h-5 w-5 dark:hidden" /><x-icon name="sun" class="hidden h-5 w-5 dark:block" />
            </button>
            <a href="{{ route('login') }}" class="btn-primary hidden sm:inline-flex">{{ $c['nav']['login'] }}</a>
            <button type="button" class="btn-ghost h-10 w-10 !p-0 lg:hidden" @click="menu = !menu" :aria-expanded="menu" aria-label="{{ $c['aria']['menu'] }}"><x-icon name="menu" class="h-5 w-5" /></button>
        </div>
    </div>
    <div x-show="menu" x-cloak x-transition.opacity class="border-t border-ink-100 bg-white px-4 py-3 dark:border-ink-800 dark:bg-ink-950 lg:hidden">
        @foreach (['features', 'roles', 'how', 'faq'] as $k)
            <a href="#{{ $k }}" @click="menu = false" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-ink-700 hover:bg-ink-50 dark:text-ink-200 dark:hover:bg-ink-900">{{ $c['nav'][$k] }}</a>
        @endforeach
        <a href="{{ route('login') }}" class="btn-primary mt-2 w-full sm:hidden">{{ $c['nav']['login'] }}</a>
    </div>
</header>

<main>
    {{-- Bosh blok --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true" style="background: radial-gradient(720px 420px at 85% -5%, rgb(var(--brand-500) / .16), transparent 70%), radial-gradient(560px 360px at -5% 105%, rgb(var(--accent-400) / .16), transparent 70%)"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.05fr_1fr] lg:py-24">
            <div class="login-rise">
                <span class="badge badge-brand mb-5 inline-flex items-center gap-1.5 !px-3 !py-1.5 text-xs"><x-icon name="sparkles" class="h-4 w-4" /> {{ $c['hero']['badge'] }}</span>
                <h1 class="text-4xl font-extrabold leading-[1.08] tracking-tight text-ink-900 dark:text-white sm:text-5xl lg:text-[3.4rem]">{{ $c['hero']['title'] }}</h1>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-ink-600 dark:text-ink-300">{{ $c['hero']['lead'] }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="btn-primary !px-6 !py-3 text-base shadow-glow">{{ $c['hero']['cta'] }} <x-icon name="chevron" class="h-4 w-4 -rotate-90" /></a>
                    <a href="#features" class="btn-secondary !px-6 !py-3 text-base">{{ $c['hero']['cta2'] }}</a>
                </div>
                <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-ink-500 dark:text-ink-400">
                    @foreach ($c['hero']['points'] as $p)
                        <li class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400"><x-icon name="check" class="h-3.5 w-3.5" /></span>{{ $p }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Tizim ko'rinishi (faqat bezak, haqiqiy ma'lumot emas) --}}
            <div class="login-rise login-rise-2 relative mx-auto w-full max-w-lg" aria-hidden="true">
                <div class="rounded-[1.75rem] bg-white p-4 shadow-pop ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800">
                    <div class="mb-4 flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span></div>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ([['users', 'brand'], ['banknote', 'accent'], ['calendar', 'brand']] as [$ic, $tone])
                            <div class="rounded-2xl bg-ink-50 p-3 dark:bg-ink-800/60">
                                <span class="grid h-9 w-9 place-items-center rounded-xl {{ $tone === 'accent' ? 'bg-accent-100 text-accent-600 dark:bg-accent-500/15 dark:text-accent-300' : 'bg-brand-100 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300' }}"><x-icon :name="$ic" class="h-5 w-5" /></span>
                                <div class="mt-3 h-2.5 w-14 rounded-full bg-ink-200 dark:bg-ink-700"></div>
                                <div class="mt-2 h-4 w-20 rounded-full {{ $tone === 'accent' ? 'bg-accent-300 dark:bg-accent-500/60' : 'bg-brand-300 dark:bg-brand-500/60' }}"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 rounded-2xl bg-ink-50 p-4 dark:bg-ink-800/60">
                        <div class="mb-3 flex items-center justify-between"><div class="h-3 w-24 rounded-full bg-ink-200 dark:bg-ink-700"></div><div class="h-3 w-12 rounded-full bg-brand-200 dark:bg-brand-500/40"></div></div>
                        <div class="flex h-28 items-end gap-2.5">
                            @foreach ([40, 62, 48, 78, 58, 92, 70, 84] as $h)
                                <span class="bar-grow flex-1 rounded-t-lg bg-gradient-to-t from-brand-600 to-brand-400" style="height: {{ $h }}%; animation-delay: {{ 0.2 + $loop->index * 0.07 }}s"></span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach ([78, 56, 66] as $w)
                            <div class="flex items-center gap-3 rounded-xl bg-ink-50 px-3 py-2.5 dark:bg-ink-800/60">
                                <span class="h-8 w-8 shrink-0 rounded-full bg-gradient-to-br from-brand-400 to-accent-400"></span>
                                <div class="min-w-0 flex-1"><div class="h-2.5 rounded-full bg-ink-200 dark:bg-ink-700" style="width: {{ $w }}%"></div><div class="mt-1.5 h-2 w-1/3 rounded-full bg-ink-100 dark:bg-ink-800"></div></div>
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="float-a absolute -left-4 top-24 hidden items-center gap-2.5 rounded-2xl bg-white p-3 shadow-pop ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800 sm:flex">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300"><x-icon name="send" class="h-5 w-5" /></span>
                    <div><div class="h-2.5 w-20 rounded-full bg-ink-200 dark:bg-ink-700"></div><div class="mt-1.5 h-2 w-12 rounded-full bg-ink-100 dark:bg-ink-800"></div></div>
                </div>
                <div class="float-b absolute -right-3 bottom-10 hidden items-center gap-2.5 rounded-2xl bg-white p-3 shadow-pop ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800 sm:flex">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-accent-100 text-accent-600 dark:bg-accent-500/15 dark:text-accent-300"><x-icon name="award" class="h-5 w-5" /></span>
                    <div><div class="h-2.5 w-16 rounded-full bg-ink-200 dark:bg-ink-700"></div><div class="mt-1.5 h-2 w-10 rounded-full bg-ink-100 dark:bg-ink-800"></div></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Imkoniyatlar --}}
    <section id="features" class="scroll-mt-20 bg-ink-50/70 py-16 dark:bg-ink-900/40 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $c['features_title'] }}</h2>
                <p class="mt-4 text-lg text-ink-600 dark:text-ink-300">{{ $c['features_lead'] }}</p>
            </div>
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($c['features'] as $f)
                    <article class="card card-hover card-body">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300"><x-icon :name="$f['icon']" class="h-6 w-6" /></span>
                        <h3 class="mt-4 text-lg font-bold text-ink-900 dark:text-white">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-600 dark:text-ink-300">{{ $f['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Rollar --}}
    <section id="roles" class="scroll-mt-20 py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $c['roles_title'] }}</h2>
                <p class="mt-4 text-lg text-ink-600 dark:text-ink-300">{{ $c['roles_lead'] }}</p>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($c['roles'] as $r)
                    <div class="flex items-start gap-4 rounded-2xl border border-ink-100 p-5 transition hover:border-brand-300 hover:shadow-card dark:border-ink-800 dark:hover:border-brand-500/50">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-accent-100 text-accent-600 dark:bg-accent-500/15 dark:text-accent-300"><x-icon :name="$r['icon']" class="h-5 w-5" /></span>
                        <div><h3 class="font-bold text-ink-900 dark:text-white">{{ $r['title'] }}</h3><p class="mt-1 text-sm leading-relaxed text-ink-600 dark:text-ink-300">{{ $r['text'] }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Qanday ishlaydi --}}
    <section id="how" class="scroll-mt-20 login-hero relative overflow-hidden py-16 text-white sm:py-24">
        <div class="login-orb absolute -right-24 -top-24 h-80 w-80 rounded-full bg-white/10" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <h2 class="mx-auto max-w-2xl text-center text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $c['how_title'] }}</h2>
            <ol class="mt-12 grid gap-5 md:grid-cols-3">
                @foreach ($c['how'] as $s)
                    <li class="glass rounded-3xl p-6">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-lg font-black text-brand-600">{{ $loop->iteration }}</span>
                        <h3 class="mt-4 text-xl font-bold">{{ $s['title'] }}</h3>
                        <p class="mt-2 leading-relaxed text-white/80">{{ $s['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Savol-javoblar (FAQPage JSON-LD bilan bir xil matn) --}}
    <section id="faq" class="scroll-mt-20 py-16 sm:py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <h2 class="text-center text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $c['faq_title'] }}</h2>
            <div class="mt-10 space-y-3">
                @foreach ($c['faq'] as $f)
                    <details class="group rounded-2xl border border-ink-100 bg-white px-5 py-4 open:shadow-card dark:border-ink-800 dark:bg-ink-900" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-bold text-ink-900 dark:text-white [&::-webkit-details-marker]:hidden">
                            <h3 class="text-base font-bold">{{ $f['q'] }}</h3>
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-600 transition group-open:rotate-180 dark:bg-brand-500/15 dark:text-brand-300"><x-icon name="chevron" class="h-4 w-4" /></span>
                        </summary>
                        <p class="mt-3 leading-relaxed text-ink-600 dark:text-ink-300">{{ $f['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Yakuniy chaqiriq --}}
    <section class="px-4 pb-16 sm:px-6 sm:pb-24">
        <div class="relative mx-auto max-w-5xl overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-600 to-brand-800 px-6 py-12 text-center text-white shadow-pop sm:px-12 sm:py-16">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-accent-400/30 blur-2xl" aria-hidden="true"></div>
            <h2 class="relative text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $c['cta']['title'] }}</h2>
            <p class="relative mx-auto mt-4 max-w-xl text-lg text-white/80">{{ $c['cta']['text'] }}</p>
            <div class="relative mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-base font-bold text-brand-700 shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">{{ $c['cta']['login'] }}</a>
                @foreach ($contact as $kind => $ct)
                    <a href="{{ $ct['url'] }}" @if (in_array($kind, ['telegram', 'instagram'])) target="_blank" rel="noopener" @endif class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-5 py-3 text-base font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/25">
                        <x-icon :name="['telegram' => 'send', 'instagram' => 'megaphone', 'phone' => 'phone', 'email' => 'message'][$kind]" class="h-5 w-5" /> {{ $ct['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</main>

<footer class="border-t border-ink-100 bg-ink-50/70 dark:border-ink-800 dark:bg-ink-900/40">
    <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 py-8 sm:flex-row sm:px-6">
        <div class="flex items-center gap-2.5"><x-logo class="h-8 w-8" /><span class="font-extrabold text-ink-900 dark:text-white">{{ \App\Support\Brand::name() }}</span></div>
        <nav class="flex flex-wrap justify-center gap-x-5 gap-y-1 text-sm text-ink-500" aria-label="{{ $c['footer']['docs'] }}">
            @foreach (\App\Services\LegalService::TYPES as $t => $label)
                <a href="{{ route('legal.show', $t) }}" class="hover:text-brand-600 hover:underline">{{ $label }}</a>
            @endforeach
        </nav>
        <p class="text-xs text-ink-400">&copy; {{ date('Y') }} {{ \App\Support\Brand::name() }}. {{ $c['footer']['rights'] }}</p>
    </div>
</footer>
@include('partials.cookie-banner')
</body>
</html>
