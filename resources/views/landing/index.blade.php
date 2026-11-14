@php
    $x = $c['x'];
    $demo = $x['demo'];
    $sizeLabels = $locale === 'ru'
        ? ['1-100' => 'до 100', '101-300' => '101–300', '301-1000' => '301–1000', '1000+' => 'более 1000']
        : $sizes;
    $chips = array_column($x['story']['scenes'], 'chip');
    $toastIcons = ['funnel', 'users', 'calendar', 'banknote', 'chart'];
@endphp
<!DOCTYPE html>
<html lang="{{ $c['lang'] }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.seo-head', ['title' => $c['meta']['title'], 'description' => $c['meta']['description'], 'canonical' => $canonical, 'locale' => $locale, 'alternates' => $alternates, 'jsonLd' => $jsonLd])
    @include('partials.pwa-head')
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>document.documentElement.classList.add('js');try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    <style>
        /* ---- Landing animatsiyalari (JS o'chiq bo'lsa hammasi oddiy ko'rinadi) ---- */
        .js .rv{opacity:0;transform:translateY(30px);transition:opacity .85s cubic-bezier(.2,.7,.2,1) var(--d,0s),transform .85s cubic-bezier(.2,.7,.2,1) var(--d,0s)}
        .js .rv.in{opacity:1;transform:none}
        .js .rv-s{transform:scale(.92) translateY(20px)}
        .js .rv-l{transform:translateX(-36px)} .js .rv-r{transform:translateX(36px)}
        .js .rv-l.in,.js .rv-r.in,.js .rv-s.in{transform:none}
        .grad-text{background:linear-gradient(90deg,rgb(var(--brand-600)),rgb(var(--accent-400,255 129 89)),rgb(var(--brand-400)),rgb(var(--brand-600)));background-size:240% auto;-webkit-background-clip:text;background-clip:text;color:transparent;animation:gt 7s linear infinite}
        .dark .grad-text{background-image:linear-gradient(90deg,#a99bff,#ff9d7a,#8f7cff,#a99bff)}
        @keyframes gt{to{background-position:240% center}}
        .marquee{display:flex;width:max-content;animation:mq 40s linear infinite}
        .marquee:hover{animation-play-state:paused}
        @keyframes mq{to{transform:translateX(-50%)}}
        .shine{position:relative;overflow:hidden}
        .shine::after{content:"";position:absolute;inset:0;transform:translateX(-130%) skewX(-20deg);background:linear-gradient(90deg,transparent,rgba(255,255,255,.5),transparent);animation:shine 3.4s ease-in-out infinite}
        @keyframes shine{0%,55%{transform:translateX(-130%) skewX(-20deg)}100%{transform:translateX(130%) skewX(-20deg)}}
        .pulse-dot{animation:pd 2s ease-out infinite}
        @keyframes pd{0%{box-shadow:0 0 0 0 rgb(var(--brand-500)/.55)}100%{box-shadow:0 0 0 14px rgb(var(--brand-500)/0)}}
        .orb{will-change:transform;transform:translate3d(0,calc(var(--sy,0)*var(--k,-.12)*1px),0)}
        .mouse-wheel{animation:mw 1.7s ease-in-out infinite}
        @keyframes mw{0%{opacity:0;transform:translateY(0)}30%{opacity:1}100%{opacity:0;transform:translateY(10px)}}
        .tilt{transition:transform .25s ease-out;transform-style:preserve-3d}
        .toast-in{animation:ti .5s cubic-bezier(.3,1.4,.5,1)}
        @keyframes ti{from{opacity:0;transform:translateY(14px) scale(.9)}to{opacity:1;transform:none}}
        .draw-line{transform-origin:top;transform:scaleY(0);transition:transform 1.6s cubic-bezier(.2,.7,.2,1)}
        .in .draw-line,.draw-line.in{transform:scaleY(1)}
        .strike{position:relative}
        .strike::after{content:"";position:absolute;left:0;top:52%;height:2px;width:0;background:currentColor;transition:width .9s ease .5s}
        .in .strike::after{width:100%}

        /* ---- SVG illyustratsiyalar ---- */
        .il-card{fill:#fff;stroke:#ece9f7}.dark .il-card{fill:#1b1a2c;stroke:#2c2a44}
        .il-sub{fill:#f3f1fb}.dark .il-sub{fill:#26243b}
        .il-line{fill:#e3e0f3}.dark .il-line{fill:#3a3857}
        .il-white{fill:#fff}
        .il-brand{fill:rgb(var(--brand-600))}.il-brand-m{fill:rgb(var(--brand-400))}.il-brand-l{fill:rgb(var(--brand-100))}.dark .il-brand-l{fill:rgb(var(--brand-500)/.22)}
        .il-acc{fill:#ff8159}.il-acc-l{fill:#ffe6db}.dark .il-acc-l{fill:rgb(255 129 89/.2)}
        .il-green{fill:#10b981}.il-green-l{fill:#d1fae5}.dark .il-green-l{fill:rgb(16 185 129/.2)}
        .il-sky{fill:#0ea5e9}.il-sky-l{fill:#e0f2fe}.dark .il-sky-l{fill:rgb(14 165 233/.2)}
        .il-rose{fill:#f43f5e}.il-rose-l{fill:#ffe4e6}.dark .il-rose-l{fill:rgb(244 63 94/.2)}
        .il-flow{fill:none;stroke:rgb(var(--brand-300));stroke-width:2.5;stroke-dasharray:6 7;stroke-linecap:round}
        .il-glyph-s{fill:none;stroke:#fff;stroke-width:2.2;stroke-linecap:round}
        .il-glyph-b{fill:none;stroke:rgb(var(--brand-600));stroke-width:4;stroke-linecap:round}
        .il-check{fill:none;stroke:#fff;stroke-width:3.4;stroke-linecap:round;stroke-linejoin:round}
        .il-check-g{fill:none;stroke:#10b981;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}.il-check-g.big{stroke-width:6}
        .il-x{fill:none;stroke:#f43f5e;stroke-width:3;stroke-linecap:round}
        .il-plus{fill:none;stroke:rgb(var(--brand-600));stroke-width:3.4;stroke-linecap:round}
        .il-sign{fill:none;stroke:rgb(var(--brand-600));stroke-width:3;stroke-linecap:round}
        .il-dash{stroke:#d9d5ee;stroke-width:2;stroke-dasharray:5 6}.dark .il-dash{stroke:#3a3857}
        .il-ring-bg{fill:none;stroke:#eeebf9;stroke-width:12}.dark .il-ring-bg{stroke:#2a2840}
        .il-ring{fill:none;stroke:#10b981;stroke-width:12;stroke-linecap:round}
        .il-ring-acc{fill:none;stroke:#ff8159;stroke-width:12;stroke-linecap:round}
        .il-ring-sky{fill:none;stroke:rgb(var(--brand-500));stroke-width:12;stroke-linecap:round}
        .il-trend{fill:none;stroke:#ff8159;stroke-width:4;stroke-linecap:round;stroke-linejoin:round}
        .il-trend-l{fill:none;stroke:#ff8159;stroke-width:4.5;stroke-linecap:round}
        .il-coin{fill:#fbbf24}.il-coin-s{fill:#d99a0b}.il-coin-in{fill:#fcd34d}
        .il-num{font:800 20px 'Plus Jakarta Sans Variable',system-ui,sans-serif;fill:rgb(var(--brand-700))}.dark .il-num{fill:#c9bfff}.il-num.il-white{fill:#fff;font-size:24px}
        .a-pop{opacity:0;transform-box:fill-box;transform-origin:center;transform:scale(.3)}
        .a-up{opacity:0;transform:translateY(22px)}.a-l{opacity:0;transform:translateX(-34px)}.a-r{opacity:0;transform:translateX(34px)}
        .a-sx{transform-box:fill-box;transform-origin:left;transform:scaleX(0)}
        .a-bar{transform-box:fill-box;transform-origin:bottom;transform:scaleY(0)}
        .a-draw{stroke-dasharray:var(--len,300);stroke-dashoffset:var(--len,300)}
        .on .a-pop,.in .a-pop{animation:aPop .6s cubic-bezier(.3,1.5,.5,1) forwards;animation-delay:calc(var(--i,0)*.1s + .1s)}
        .on .a-up,.in .a-up,.on .a-l,.in .a-l,.on .a-r,.in .a-r{animation:aMove .7s cubic-bezier(.2,.8,.2,1) forwards;animation-delay:calc(var(--i,0)*.1s + .05s)}
        .on .a-sx,.in .a-sx{animation:aSx .8s cubic-bezier(.2,.8,.2,1) forwards;animation-delay:calc(var(--i,0)*.12s + .15s)}
        .on .a-bar,.in .a-bar{animation:aBar .9s cubic-bezier(.2,.8,.2,1) forwards;animation-delay:calc(var(--i,0)*.07s + .1s)}
        .on .a-draw,.in .a-draw{animation:aDraw 1.3s ease forwards;animation-delay:calc(var(--i,0)*.1s + .2s)}
        .a-float{animation:aFloat 3.6s ease-in-out infinite}
        .a-pulse{transform-box:fill-box;transform-origin:center;animation:aPulse 2.4s ease-out infinite}
        @keyframes aPop{to{opacity:1;transform:scale(1)}}
        @keyframes aMove{to{opacity:1;transform:none}}
        @keyframes aSx{to{transform:scaleX(1)}}
        @keyframes aBar{to{transform:scaleY(1)}}
        @keyframes aDraw{to{stroke-dashoffset:var(--off,0)}}
        @keyframes aFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
        @keyframes aPulse{0%{transform:scale(.8);opacity:.9}100%{transform:scale(1.7);opacity:0}}
        /* eski sahnalar (ishlatilmaydi) */
        .sc-pop{opacity:0;transform:scale(.5)}
        .on .sc-pop,.in .sc-pop{animation:scPop .55s cubic-bezier(.3,1.5,.5,1) forwards;animation-delay:calc(var(--i,0)*.12s + .1s)}
        @keyframes scPop{to{opacity:1;transform:scale(1)}}
        .sc-slide{opacity:0;transform:translateX(26px)}
        .on .sc-slide,.in .sc-slide{animation:scSlide .6s ease forwards;animation-delay:calc(var(--i,0)*.2s)}
        @keyframes scSlide{to{opacity:1;transform:none}}
        .sc-fill{width:0}
        .on .sc-fill,.in .sc-fill{animation:scFill 1.2s .3s ease forwards}
        @keyframes scFill{to{width:var(--w)}}
        .sc-bar{height:0}
        .on .sc-bar,.in .sc-bar{animation:scBar .9s cubic-bezier(.2,.8,.2,1) forwards;animation-delay:calc(var(--i,0)*.07s + .1s)}
        @keyframes scBar{to{height:var(--h)}}
        /* muvaffaqiyat belgisi */
        .ok-ring{stroke-dasharray:166;stroke-dashoffset:166;animation:okd .7s ease forwards}
        .ok-tick{stroke-dasharray:48;stroke-dashoffset:48;animation:okd .45s .55s ease forwards}
        @keyframes okd{to{stroke-dashoffset:0}}
        .confetti i{position:absolute;left:50%;top:38%;width:8px;height:8px;border-radius:2px;opacity:0;animation:cf 1.2s ease-out forwards;animation-delay:calc(var(--i)*.03s + .5s)}
        @keyframes cf{0%{opacity:1;transform:translate(0,0) rotate(0)}100%{opacity:0;transform:translate(var(--x),var(--y)) rotate(320deg)}}
        @media (prefers-reduced-motion: reduce){
            .js .rv{opacity:1!important;transform:none!important;transition:none}
            .grad-text,.marquee,.shine::after,.pulse-dot,.mouse-wheel,.toast-in{animation:none!important}
            .sc-pop,.sc-slide{opacity:1!important;transform:none!important;animation:none!important}
            .sc-fill{width:var(--w)!important;animation:none!important}.sc-bar{height:var(--h)!important;animation:none!important}
            .draw-line{transform:none}.strike::after{width:100%;transition:none}
            .orb{transform:none!important}
            .a-pop,.a-up,.a-l,.a-r,.a-sx,.a-bar{opacity:1!important;transform:none!important;animation:none!important}
            .a-draw{stroke-dashoffset:var(--off,0)!important;animation:none!important}
            .a-float,.a-pulse{animation:none!important}
        }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('landing', (cfg) => ({
                menu: false, p: 0, showCta: false, scene: 0, wordI: 0, toastI: 0, words: cfg.words, chips: cfg.chips,
                init() {
                    const root = document.documentElement;
                    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    // scroll: progress, parallax, suzuvchi tugma
                    let tick = false;
                    const onScroll = () => {
                        if (tick) return; tick = true;
                        requestAnimationFrame(() => {
                            tick = false;
                            const max = root.scrollHeight - innerHeight;
                            this.p = max > 0 ? Math.min(1, scrollY / max) : 0;
                            root.style.setProperty('--p', this.p.toFixed(4));
                            root.style.setProperty('--sy', Math.round(scrollY));
                            const d = document.getElementById('demo');
                            const top = d ? d.getBoundingClientRect().top : 9999;
                            this.showCta = scrollY > innerHeight * 0.6 && top > innerHeight * 0.55;
                            const mid = innerHeight / 2; let best = this.scene, bd = Infinity;
                            document.querySelectorAll('[data-scene]').forEach((el) => { const r = el.getBoundingClientRect(); const d = Math.abs(r.top + r.height / 2 - mid); if (d < bd) { bd = d; best = Number(el.dataset.scene); } });
                            if (best !== this.scene) this.scene = best;
                        });
                    };
                    addEventListener('scroll', onScroll, { passive: true }); onScroll();
                    // ko'rinishga kirganda paydo bo'lish
                    const io = new IntersectionObserver((es) => es.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: 0.14, rootMargin: '0px 0px -6% 0px' });
                    document.querySelectorAll('.rv, [data-in]').forEach((el) => io.observe(el));
                    // hikoya: ekran o'rtasiga eng yaqin qadam = faol sahna (scroll paytida hisoblanadi)
                    // so'zlar va bildirishnoma almashinuvi
                    if (!reduce) {
                        setInterval(() => { this.wordI = (this.wordI + 1) % this.words.length; }, 2300);
                        setInterval(() => { this.toastI = (this.toastI + 1) % this.chips.length; }, 3000);
                    }
                    // sichqoncha bilan engil qiyshayish
                    if (!reduce && matchMedia('(hover: hover)').matches) {
                        document.querySelectorAll('[data-tilt]').forEach((el) => {
                            el.addEventListener('mousemove', (ev) => { const r = el.getBoundingClientRect(); const px = (ev.clientX - r.left) / r.width - .5; const py = (ev.clientY - r.top) / r.height - .5; el.style.transform = `perspective(900px) rotateY(${px * 7}deg) rotateX(${-py * 7}deg)`; });
                            el.addEventListener('mouseleave', () => { el.style.transform = ''; });
                        });
                    }
                },
            }));
            Alpine.data('demoForm', (cfg) => ({
                f: { name: '', phone: '+998 ', center: '', size: '', message: '', website: '' }, err: {}, busy: false, ok: cfg.ok, fail: false,
                fmt() {
                    let d = this.f.phone.replace(/\D/g, ''); if (d.startsWith('998')) d = d.slice(3); d = d.slice(0, 9);
                    let o = '+998'; if (d.length) o += ' ' + d.slice(0, 2); if (d.length > 2) o += ' ' + d.slice(2, 5); if (d.length > 5) o += ' ' + d.slice(5, 7); if (d.length > 7) o += ' ' + d.slice(7, 9);
                    this.f.phone = d.length ? o : '+998 ';
                },
                async submit() {
                    if (this.busy) return; this.busy = true; this.err = {}; this.fail = false;
                    try {
                        const r = await fetch(cfg.url, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.csrf }, body: JSON.stringify({ ...this.f, locale: cfg.locale, t: cfg.t }) });
                        if (r.status === 422) { const j = await r.json(); for (const k in j.errors || {}) this.err[k] = j.errors[k][0]; }
                        else if (!r.ok) { this.fail = true; }
                        else { this.ok = true; }
                    } catch (e) { this.fail = true; }
                    this.busy = false;
                },
            }));
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full overflow-x-hidden bg-white text-ink-800 antialiased dark:bg-ink-950 dark:text-ink-100" x-data="landing(@js(['words' => $x['hero_words'], 'chips' => $chips]))" @keydown.escape.window="menu = false">

{{-- Sahifa bo'ylab o'tish chizig'i --}}
<div class="pointer-events-none fixed inset-x-0 top-0 z-[60] h-1"><div class="h-full origin-left bg-gradient-to-r from-brand-500 via-accent-400 to-brand-600" :style="`transform: scaleX(${p})`"></div></div>

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
            <a href="{{ route('login') }}" class="btn-secondary hidden sm:inline-flex">{{ $c['nav']['login'] }}</a>
            <a href="#demo" class="btn-primary shine hidden sm:inline-flex">{{ $x['float_cta'] }}</a>
            <button type="button" class="btn-ghost h-10 w-10 !p-0 lg:hidden" @click="menu = !menu" :aria-expanded="menu" aria-label="{{ $c['aria']['menu'] }}"><x-icon name="menu" class="h-5 w-5" /></button>
        </div>
    </div>
    <div x-show="menu" x-cloak x-transition.opacity class="border-t border-ink-100 bg-white px-4 py-3 dark:border-ink-800 dark:bg-ink-950 lg:hidden">
        @foreach (['features', 'roles', 'how', 'faq'] as $k)
            <a href="#{{ $k }}" @click="menu = false" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-ink-700 hover:bg-ink-50 dark:text-ink-200 dark:hover:bg-ink-900">{{ $c['nav'][$k] }}</a>
        @endforeach
        <div class="mt-2 grid grid-cols-2 gap-2 sm:hidden">
            <a href="{{ route('login') }}" class="btn-secondary">{{ $c['nav']['login'] }}</a>
            <a href="#demo" @click="menu = false" class="btn-primary">{{ $x['float_cta'] }}</a>
        </div>
    </div>
</header>

<main>
    {{-- ============ Bosh blok ============ --}}
    <section class="relative overflow-hidden">
        <div class="orb pointer-events-none absolute -right-32 -top-32 h-[34rem] w-[34rem] rounded-full bg-brand-400/25 blur-3xl" style="--k:-.10" aria-hidden="true"></div>
        <div class="orb pointer-events-none absolute -bottom-40 -left-32 h-[30rem] w-[30rem] rounded-full bg-accent-300/30 blur-3xl" style="--k:-.18" aria-hidden="true"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.05fr_1fr] lg:py-24">
            <div class="login-rise">
                <span class="badge badge-brand mb-5 inline-flex items-center gap-2 !px-3 !py-1.5 text-xs"><span class="pulse-dot h-2 w-2 rounded-full bg-brand-500"></span> {{ $c['hero']['badge'] }}</span>
                <h1 class="text-4xl font-extrabold leading-[1.08] tracking-tight text-ink-900 dark:text-white sm:text-5xl lg:text-[3.4rem]">{{ $c['hero']['title'] }}</h1>
                <p class="mt-4 flex flex-wrap items-baseline gap-x-2 text-xl font-bold text-ink-500 dark:text-ink-400 sm:text-2xl" aria-hidden="true">
                    <span>{{ $x['hero_words_label'] }}</span>
                    <span class="inline-grid">
                        @foreach ($x['hero_words'] as $i => $w)
                            <span class="grad-text col-start-1 row-start-1 transition-all duration-500" :class="wordI === {{ $i }} ? 'translate-y-0 opacity-100' : 'translate-y-3 opacity-0'">{{ $w }}</span>
                        @endforeach
                    </span>
                </p>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-ink-600 dark:text-ink-300">{{ $c['hero']['lead'] }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#demo" class="btn-primary shine !px-6 !py-3 text-base shadow-glow">{{ $x['float_cta'] }} <x-icon name="chevron" class="h-4 w-4 -rotate-90" /></a>
                    <a href="{{ route('login') }}" class="btn-secondary !px-6 !py-3 text-base">{{ $c['hero']['cta'] }}</a>
                </div>
                <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-ink-500 dark:text-ink-400">
                    @foreach ($c['hero']['points'] as $p)
                        <li class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400"><x-icon name="check" class="h-3.5 w-3.5" /></span>{{ $p }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Jonli tizim ko'rinishi (faqat bezak) --}}
            <div class="login-rise login-rise-2 relative mx-auto w-full max-w-lg" aria-hidden="true">
                <div data-tilt class="tilt rounded-[1.75rem] bg-white p-4 shadow-pop ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800">
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
                {{-- Almashib turuvchi bildirishnoma --}}
                <div class="absolute -top-4 right-2 z-10 sm:-right-4">
                    @foreach ($chips as $i => $chip)
                        <div x-show="toastI === {{ $i }}" x-cloak class="toast-in flex items-center gap-2.5 rounded-2xl bg-white p-3 pr-4 shadow-pop ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300"><x-icon :name="$toastIcons[$i]" class="h-5 w-5" /></span>
                            <span class="text-sm font-bold text-ink-800 dark:text-ink-100">{{ $chip }}</span>
                        </div>
                    @endforeach
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
        <a href="#story" class="relative mx-auto mb-6 flex w-fit flex-col items-center gap-2 text-sm font-semibold text-ink-500 transition hover:text-brand-600 dark:text-ink-400">
            <span class="grid h-10 w-6 place-items-start justify-center rounded-full border-2 border-ink-300 pt-1.5 dark:border-ink-600"><span class="mouse-wheel h-2 w-1 rounded-full bg-brand-500"></span></span>
            {{ $x['scroll_hint'] }}
        </a>
    </section>

    {{-- Yurib turuvchi kalit so'zlar lentasi --}}
    <div class="overflow-hidden border-y border-ink-100 bg-ink-50/70 py-4 dark:border-ink-800 dark:bg-ink-900/40" aria-hidden="true">
        <div class="marquee">
            @for ($r = 0; $r < 2; $r++)
                @foreach ($x['marquee'] as $m)
                    <span class="mx-5 flex items-center gap-3 whitespace-nowrap text-lg font-extrabold tracking-tight text-ink-400 dark:text-ink-500"><span class="h-2 w-2 rounded-full bg-accent-400"></span>{{ $m }}</span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- ============ Oldin va keyin ============ --}}
    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <h2 class="rv mx-auto max-w-2xl text-center text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $x['pain']['title'] }}</h2>
            <div class="mt-12 grid gap-5 md:grid-cols-2">
                <div class="rv rv-l rounded-3xl border border-ink-200 bg-ink-50/60 p-6 dark:border-ink-800 dark:bg-ink-900/40">
                    <div class="mb-4 flex items-center gap-2 text-sm font-extrabold uppercase tracking-wider text-ink-400"><x-icon name="x" class="h-5 w-5 text-rose-500" /> {{ $x['pain']['before_title'] }}</div>
                    <ul class="space-y-3.5">
                        @foreach ($x['pain']['before'] as $t)
                            <li data-in class="strike w-fit text-ink-500 dark:text-ink-400" style="transition-delay: {{ $loop->index * .15 }}s">{{ $t }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="rv rv-r relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 p-6 text-white shadow-glow" style="--d:.12s">
                    <div class="orb absolute -right-12 -top-12 h-40 w-40 rounded-full bg-accent-400/30 blur-2xl" style="--k:-.06" aria-hidden="true"></div>
                    <div class="relative mb-4 flex items-center gap-2 text-sm font-extrabold uppercase tracking-wider text-white/80"><x-icon name="sparkles" class="h-5 w-5 text-accent-300" /> {{ $x['pain']['after_title'] }}</div>
                    <ul class="relative space-y-3.5">
                        @foreach ($x['pain']['after'] as $t)
                            <li class="flex items-start gap-3 font-semibold"><span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-white text-brand-600"><x-icon name="check" class="h-3.5 w-3.5" /></span>{{ $t }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Hikoya: bir kun CRM ichida (scroll bilan almashadi) ============ --}}
    <section id="story" class="scroll-mt-16 bg-ink-50/70 py-16 dark:bg-ink-900/40 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="rv mx-auto max-w-2xl text-center">
                <span class="badge badge-brand mb-4 !px-3 !py-1.5 text-xs">{{ str_replace('{n}', \App\Support\Brand::short(), $x['story']['eyebrow']) }}</span>
                <h2 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $x['story']['title'] }}</h2>
                <p class="mt-4 text-lg text-ink-600 dark:text-ink-300">{{ $x['story']['lead'] }}</p>
            </div>

            <div class="mt-12 grid gap-10 lg:grid-cols-2 lg:gap-16">
                {{-- Chap: yopishib turadigan sahna (faqat katta ekranda) --}}
                <div class="relative hidden lg:block" aria-hidden="true">
                    <div class="sticky" style="top: max(5.5rem, calc(50vh - 21rem))">
                        <div class="relative rounded-[2rem] bg-white p-5 shadow-pop ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span></div>
                                <div class="flex gap-1.5">@foreach ($x['story']['scenes'] as $i => $s)<span class="h-1.5 rounded-full transition-all duration-500" :class="scene === {{ $i }} ? 'w-8 bg-brand-600' : 'w-3 bg-ink-200 dark:bg-ink-700'"></span>@endforeach</div>
                            </div>
                            <div class="relative aspect-[5/4] overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-white dark:from-brand-950/50 dark:to-ink-900">
                                @foreach ($x['story']['scenes'] as $i => $s)
                                    <div class="absolute inset-0 transition-all duration-500" :class="scene === {{ $i }} ? 'on translate-y-0 scale-100 opacity-100' : 'pointer-events-none translate-y-4 scale-95 opacity-0'">
                                        @include('landing._scene', ['n' => $i + 1, 'chip' => $s['chip'], 'uid' => 'd'])
                                    </div>
                                @endforeach
                            </div>
                            {{-- Sahna sarlavhasi (almashib turadi) --}}
                            <div class="relative mt-4 h-12">
                                @foreach ($x['story']['scenes'] as $i => $s)
                                    <div class="absolute inset-0 flex items-center gap-3 transition-all duration-500" :class="scene === {{ $i }} ? 'translate-y-0 opacity-100' : 'translate-y-3 opacity-0'">
                                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-600 text-white shadow-glow"><x-icon :name="$toastIcons[$i]" class="h-5 w-5" /></span>
                                        <div class="min-w-0"><div class="truncate text-sm font-extrabold text-ink-900 dark:text-white">{{ $s['title'] }}</div><div class="text-xs font-semibold text-brand-600 dark:text-brand-300">{{ $s['chip'] }}</div></div>
                                        <span class="ml-auto text-3xl font-black text-ink-100 dark:text-ink-800">0{{ $i + 1 }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="float-a absolute -right-5 -top-5 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-accent-300 to-accent-500 text-white shadow-pop"><x-icon name="sparkles" class="h-7 w-7" /></div>
                    </div>
                </div>

                {{-- O'ng: qadamlar --}}
                <ol class="relative">
                    <span class="absolute bottom-10 left-[1.4rem] top-10 hidden w-0.5 bg-ink-200 dark:bg-ink-800 sm:block" aria-hidden="true"><span class="block w-full origin-top bg-gradient-to-b from-brand-500 to-accent-400 transition-transform duration-500" style="height:100%" :style="`transform: scaleY(${(scene + 1) / {{ count($x['story']['scenes']) }}})`"></span></span>
                    @foreach ($x['story']['scenes'] as $i => $s)
                        <li data-scene="{{ $i }}" class="flex min-h-[26rem] items-center py-6 lg:min-h-[34rem]">
                            <div class="rv relative w-full sm:pl-16">
                                <span class="absolute left-0 top-0 hidden h-12 w-12 place-items-center rounded-2xl text-lg font-black transition-all duration-500 sm:grid" :class="scene >= {{ $i }} ? 'bg-brand-600 text-white shadow-glow' : 'bg-white text-ink-400 ring-1 ring-ink-200 dark:bg-ink-900 dark:ring-ink-700'">{{ $s['tag'] }}</span>
                                <h3 class="text-2xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-3xl"><span class="mr-2 text-brand-600 sm:hidden">{{ $s['tag'] }}.</span>{{ $s['title'] }}</h3>
                                <p class="mt-3 text-lg leading-relaxed text-ink-600 dark:text-ink-300">{{ $s['text'] }}</p>
                                <div data-in class="mt-6 rounded-[1.5rem] bg-white p-4 shadow-card ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800 lg:hidden" aria-hidden="true">
                                    <div class="aspect-[5/4] overflow-hidden rounded-xl bg-gradient-to-br from-brand-50 to-white dark:from-brand-950/50 dark:to-ink-900">@include('landing._scene', ['n' => $i + 1, 'chip' => $s['chip'], 'uid' => 'm'])</div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- ============ Imkoniyatlar ============ --}}
    <section id="features" class="scroll-mt-16 py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="rv mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $c['features_title'] }}</h2>
                <p class="mt-4 text-lg text-ink-600 dark:text-ink-300">{{ $c['features_lead'] }}</p>
            </div>
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($c['features'] as $f)
                    <article class="rv rv-s card card-hover card-body group" style="--d: {{ ($loop->index % 3) * .1 }}s">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-600 transition duration-300 group-hover:-rotate-6 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white dark:bg-brand-500/15 dark:text-brand-300"><x-icon :name="$f['icon']" class="h-6 w-6" /></span>
                        <h3 class="mt-4 text-lg font-bold text-ink-900 dark:text-white">{{ $f['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-600 dark:text-ink-300">{{ $f['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Rollar ============ --}}
    <section id="roles" class="scroll-mt-16 bg-ink-50/70 py-16 dark:bg-ink-900/40 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="rv mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $c['roles_title'] }}</h2>
                <p class="mt-4 text-lg text-ink-600 dark:text-ink-300">{{ $c['roles_lead'] }}</p>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($c['roles'] as $r)
                    <div class="rv flex items-start gap-4 rounded-2xl border border-ink-100 bg-white p-5 transition duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-card dark:border-ink-800 dark:bg-ink-900 dark:hover:border-brand-500/50" style="--d: {{ ($loop->index % 3) * .1 }}s">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-accent-100 text-accent-600 dark:bg-accent-500/15 dark:text-accent-300"><x-icon :name="$r['icon']" class="h-5 w-5" /></span>
                        <div><h3 class="font-bold text-ink-900 dark:text-white">{{ $r['title'] }}</h3><p class="mt-1 text-sm leading-relaxed text-ink-600 dark:text-ink-300">{{ $r['text'] }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Qanday ishlaydi ============ --}}
    <section id="how" class="login-hero relative scroll-mt-16 overflow-hidden py-16 text-white sm:py-24">
        <div class="login-orb absolute -right-24 -top-24 h-80 w-80 rounded-full bg-white/10" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <h2 class="rv mx-auto max-w-2xl text-center text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $c['how_title'] }}</h2>
            <ol class="mt-12 grid gap-5 md:grid-cols-3">
                @foreach ($c['how'] as $s)
                    <li class="rv rv-s glass rounded-3xl p-6" style="--d: {{ $loop->index * .15 }}s">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-lg font-black text-brand-600">{{ $loop->iteration }}</span>
                        <h3 class="mt-4 text-xl font-bold">{{ $s['title'] }}</h3>
                        <p class="mt-2 leading-relaxed text-white/80">{{ $s['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ============ Savol-javoblar ============ --}}
    <section id="faq" class="scroll-mt-16 py-16 sm:py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <h2 class="rv text-center text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-4xl">{{ $c['faq_title'] }}</h2>
            <div class="mt-10 space-y-3">
                @foreach ($c['faq'] as $f)
                    <details class="rv group rounded-2xl border border-ink-100 bg-white px-5 py-4 open:shadow-card dark:border-ink-800 dark:bg-ink-900" style="--d: {{ $loop->index * .06 }}s" @if ($loop->first) open @endif>
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

    {{-- ============ Demo so'rash ============ --}}
    <section id="demo" class="relative scroll-mt-16 overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-brand-950 py-16 text-white sm:py-24">
        <div class="orb pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-accent-400/25 blur-3xl" style="--k:-.08" aria-hidden="true"></div>
        <div class="orb pointer-events-none absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-brand-400/25 blur-3xl" style="--k:-.14" aria-hidden="true"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-[1fr_1.05fr]">
            <div class="rv rv-l">
                <h2 class="text-3xl font-extrabold leading-tight tracking-tight sm:text-5xl">{{ $demo['title'] }}</h2>
                <p class="mt-5 max-w-lg text-lg leading-relaxed text-white/80">{{ $demo['text'] }}</p>
                <ul class="mt-8 space-y-3.5">
                    @foreach ($demo['points'] as $pt)
                        <li class="flex items-center gap-3 font-semibold"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-accent-400 text-white shadow-lg"><x-icon name="check" class="h-4 w-4" /></span>{{ $pt }}</li>
                    @endforeach
                </ul>
                @if ($contact)
                    <div class="mt-8 flex flex-wrap gap-2.5">
                        @foreach ($contact as $kind => $ct)
                            <a href="{{ $ct['url'] }}" @if (in_array($kind, ['telegram', 'instagram'])) target="_blank" rel="noopener" @endif class="inline-flex items-center gap-2 rounded-xl bg-white/12 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                                <x-icon :name="['telegram' => 'send', 'instagram' => 'megaphone', 'phone' => 'phone', 'email' => 'message'][$kind]" class="h-4 w-4" /> {{ $ct['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rv rv-r" style="--d:.12s">
                <div x-data="demoForm(@js(['url' => route('demo.store'), 'csrf' => csrf_token(), 't' => $demoToken, 'locale' => $locale, 'ok' => (bool) session('demo_ok')]))" class="relative overflow-hidden rounded-[2rem] bg-white p-6 text-ink-800 shadow-pop dark:bg-ink-900 dark:text-ink-100 sm:p-8">
                    {{-- Muvaffaqiyat holati --}}
                    <div x-show="ok" x-cloak class="relative flex min-h-[22rem] flex-col items-center justify-center text-center">
                        <div class="confetti pointer-events-none absolute inset-0" aria-hidden="true">
                            @foreach ([['-120px','-90px','#ff8159'],['110px','-100px','#6d4cf0'],['-90px','70px','#34d399'],['130px','60px','#fbbf24'],['-40px','-130px','#6d4cf0'],['50px','-120px','#ff8159'],['-140px','-10px','#fbbf24'],['150px','-10px','#34d399']] as [$cx, $cy, $col])
                                <i style="--x: {{ $cx }}; --y: {{ $cy }}; --i: {{ $loop->index }}; background: {{ $col }}"></i>
                            @endforeach
                        </div>
                        <svg viewBox="0 0 56 56" class="h-24 w-24" aria-hidden="true"><circle class="ok-ring" cx="28" cy="28" r="26" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" transform="rotate(-90 28 28)"/><path class="ok-tick" d="M16 29l8 8 16-17" fill="none" stroke="#10b981" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <h3 class="mt-5 text-2xl font-extrabold text-ink-900 dark:text-white">{{ $demo['ok']['title'] }}</h3>
                        <p class="mt-2 text-ink-600 dark:text-ink-300">{{ $demo['ok']['text'] }}</p>
                    </div>

                    <form x-show="!ok" method="POST" action="{{ route('demo.store') }}" @submit.prevent="submit()" class="space-y-4" novalidate>
                        @csrf
                        <input type="hidden" name="t" value="{{ $demoToken }}">
                        <input type="hidden" name="locale" value="{{ $locale }}">
                        <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off" x-model="f.website"></label></div>
                        <div>
                            <label class="label" for="d_name">{{ $demo['form']['name'] }} <span class="text-danger-600">*</span></label>
                            <input id="d_name" name="name" x-model="f.name" required maxlength="80" autocomplete="name" class="input" :class="err.name && 'input-error'">
                            <p class="error-text" x-show="err.name" x-text="err.name" x-cloak></p>
                        </div>
                        <div>
                            <label class="label" for="d_phone">{{ $demo['form']['phone'] }} <span class="text-danger-600">*</span></label>
                            <input id="d_phone" name="phone" x-model="f.phone" @input="fmt()" required inputmode="tel" autocomplete="tel" maxlength="17" class="input" :class="err.phone && 'input-error'">
                            <p class="error-text" x-show="err.phone" x-text="err.phone" x-cloak></p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label" for="d_center">{{ $demo['form']['center'] }}</label>
                                <input id="d_center" name="center" x-model="f.center" maxlength="100" class="input">
                            </div>
                            <div>
                                <label class="label" for="d_size">{{ $demo['form']['size'] }}</label>
                                <select id="d_size" name="size" x-model="f.size" class="input">
                                    <option value="">{{ $demo['form']['size_ph'] }}</option>
                                    @foreach ($sizeLabels as $k => $lbl)<option value="{{ $k }}">{{ $lbl }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="label" for="d_msg">{{ $demo['form']['message'] }}</label>
                            <textarea id="d_msg" name="message" x-model="f.message" rows="3" maxlength="500" class="input"></textarea>
                        </div>
                        <p class="rounded-xl bg-danger-50 px-3 py-2 text-sm font-semibold text-danger-700 dark:bg-danger-500/10 dark:text-danger-300" x-show="fail" x-cloak>{{ $demo['form']['error'] }}</p>
                        <button type="submit" class="btn-primary shine w-full !py-3.5 text-base shadow-glow" :disabled="busy">
                            <span x-show="!busy">{{ $demo['form']['submit'] }}</span>
                            <span x-show="busy" x-cloak>{{ $demo['form']['sending'] }}</span>
                        </button>
                        <p class="text-center text-xs text-ink-400">{{ $demo['form']['privacy'] }}
                            <a href="{{ route('legal.show', 'privacy') }}" class="font-semibold text-brand-600 hover:underline">{{ \App\Services\LegalService::TYPES['privacy'] }}</a></p>
                    </form>
                </div>
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
            <a href="{{ route('login') }}" class="hover:text-brand-600 hover:underline">{{ $c['nav']['login'] }}</a>
        </nav>
        <p class="text-xs text-ink-400">&copy; {{ date('Y') }} {{ \App\Support\Brand::name() }}. {{ $c['footer']['rights'] }}</p>
    </div>
</footer>

{{-- Suzuvchi demo tugmasi: sahifa o'tilgani sari halqa to'ladi va oxirigacha chorlaydi --}}
<a href="#demo" x-show="showCta" x-cloak x-transition:enter="transition duration-500" x-transition:enter-start="translate-y-6 opacity-0" x-transition:leave="transition duration-300" x-transition:leave-end="translate-y-6 opacity-0"
   class="shine fixed bottom-5 right-4 z-50 flex items-center gap-3 rounded-full bg-ink-900 py-2 pl-2 pr-5 text-sm font-bold text-white shadow-pop ring-1 ring-white/10 transition hover:-translate-y-0.5 dark:bg-white dark:text-ink-900 sm:right-6">
    <span class="relative grid h-11 w-11 place-items-center">
        <svg viewBox="0 0 44 44" class="absolute inset-0 -rotate-90"><circle cx="22" cy="22" r="19" fill="none" stroke="currentColor" stroke-opacity=".2" stroke-width="3"/><circle cx="22" cy="22" r="19" fill="none" stroke="rgb(255 129 89)" stroke-width="3" stroke-linecap="round" stroke-dasharray="119.4" :stroke-dashoffset="119.4 * (1 - p)"/></svg>
        <x-icon name="play" class="relative h-4 w-4" />
    </span>
    {{ $x['float_cta'] }}
</a>
@include('partials.cookie-banner')
</body>
</html>
