<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Kirish · {{ \App\Support\Brand::name() }}</title>
    @include('partials.pwa-head')
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div class="grid min-h-full lg:grid-cols-[1.05fr_1fr]">
    {{-- Chap tomon: harakatlanuvchi gradient va suzuvchi shisha kartalar (faqat bezak, haqiqiy ma'lumot emas) --}}
    <div class="login-hero relative hidden overflow-hidden lg:flex lg:flex-col lg:justify-between lg:p-12">
        <div class="login-orb absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
        <div class="login-orb login-orb-2 absolute -bottom-32 -left-16 h-[28rem] w-[28rem] rounded-full bg-black/15"></div>

        <div class="relative flex items-center gap-3 text-white">
            <span class="inline-flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-white text-xl font-black text-brand-600 shadow-lg">@if ($logo = \App\Support\Brand::logoUrl())<img src="{{ $logo }}" alt="" class="h-full w-full object-contain">@else{{ \App\Support\Brand::initial() }}@endif</span>
            <span class="text-2xl font-extrabold tracking-tight">{{ \App\Support\Brand::short() }}</span>
        </div>

        <div class="relative my-10">
            <div class="relative mx-auto h-72 max-w-md">
                <div class="glass float-a absolute left-0 top-2 w-56 rounded-3xl p-4 text-white">
                    <div class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/80"><x-icon name="chart" class="h-4 w-4" /> Statistika</div>
                    <div class="flex h-20 items-end gap-2">
                        @foreach ([38, 62, 48, 80, 58, 92, 70] as $h)
                            <span class="bar-grow flex-1 rounded-t-md bg-white/80" style="height: {{ $h }}%; animation-delay: {{ 0.15 + $loop->index * 0.08 }}s"></span>
                        @endforeach
                    </div>
                </div>
                <div class="glass float-b absolute right-0 top-0 flex w-40 flex-col items-center rounded-3xl p-4 text-white">
                    <svg viewBox="0 0 44 44" class="h-20 w-20 -rotate-90"><circle cx="22" cy="22" r="17" fill="none" stroke="rgb(255 255 255 / .25)" stroke-width="5"/><circle cx="22" cy="22" r="17" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-dasharray="107" stroke-dashoffset="26"/></svg>
                    <div class="mt-2 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-white/85"><x-icon name="calendar" class="h-4 w-4" /> Davomad</div>
                </div>
                <div class="glass float-c absolute bottom-0 left-10 flex w-64 items-center gap-3 rounded-3xl p-4 text-white">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white/20"><x-icon name="banknote" class="h-6 w-6" /></span>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-bold">To'lovlar</div>
                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-white/25"><span class="block h-full w-3/4 rounded-full bg-white"></span></div>
                    </div>
                    <span class="grid h-7 w-7 place-items-center rounded-full bg-emerald-400 text-white shadow"><x-icon name="check" class="h-4 w-4" /></span>
                </div>
                <div class="glass float-a absolute -right-2 bottom-6 grid h-14 w-14 place-items-center rounded-2xl text-white"><x-icon name="sparkles" class="h-7 w-7" /></div>
            </div>
        </div>

        <div class="login-rise login-rise-2 relative max-w-md text-white">
            <h2 class="text-4xl font-extrabold leading-tight tracking-tight">{{ \App\Support\Brand::tagline() }}</h2>
            <p class="mt-4 text-lg text-white/80">Filiallar, o'quvchilar, davomad, to'lovlar va hisobotlar &mdash; barchasi bir joyda.</p>
        </div>
        <p class="relative mt-8 text-sm text-white/60">&copy; {{ date('Y') }} {{ \App\Support\Brand::name() }}</p>
    </div>

    {{-- O'ng tomon: forma --}}
    <div x-data class="relative flex items-center justify-center bg-white p-6 dark:bg-ink-950 sm:p-12">
        <div class="pointer-events-none absolute inset-0 opacity-70" style="background: radial-gradient(520px 300px at 85% 0%, rgb(var(--brand-500) / .10), transparent 70%), radial-gradient(420px 260px at 0% 100%, rgb(var(--accent-400) / .09), transparent 70%)"></div>
        <button type="button" class="btn-ghost absolute right-4 top-4 h-10 w-10 !p-0" @click="$store.theme.toggle()" aria-label="Rejimni almashtirish">
            <x-icon name="moon" colored class="no-ic-anim absolute left-2.5 top-2.5 h-5 w-5 transition duration-500" ::class="$store.theme.dark ? 'rotate-90 scale-0 opacity-0' : ''" />
            <x-icon name="sun" colored class="no-ic-anim absolute left-2.5 top-2.5 h-5 w-5 transition duration-500" ::class="$store.theme.dark ? '' : '-rotate-90 scale-0 opacity-0'" />
        </button>

        <div class="login-rise relative w-full max-w-sm">
            <x-brand-mark class="mb-8 lg:hidden" />

            <span class="brand-chip mb-5 grid h-12 w-12 place-items-center rounded-2xl"><x-icon name="lock" class="h-6 w-6" /></span>
            <h1 class="text-3xl font-extrabold tracking-tight text-ink-900 dark:text-white">Tizimga kirish</h1>
            <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-400">Login (yoki telefon raqami) va parolingizni kiriting.</p>

            <form method="POST" action="{{ route('login') }}" class="login-rise login-rise-3 mt-8 space-y-5" x-data="{ show: false }">
                @csrf
                <div>
                    <label for="login" class="label">Login, email yoki telefon</label>
                    <div class="relative">
                        <x-icon name="user" colored class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2" />
                        <input id="login" name="login" type="text" value="{{ old('login') }}" autocomplete="username" required autofocus
                               class="input pl-11 {{ $errors->has('login') ? 'input-error' : '' }}">
                    </div>
                    @error('login')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="label">Parol</label>
                    <div class="relative">
                        <x-icon name="key" colored class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2" />
                        <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="current-password" required
                               class="input pl-11 pr-11 {{ $errors->has('password') ? 'input-error' : '' }}">
                        <button type="button" @click="show = !show" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-ink-400 transition hover:bg-ink-100 hover:text-brand-600 dark:hover:bg-ink-800" :aria-label="show ? 'Parolni yashirish' : 'Parolni ko\'rsatish'">
                            <x-icon name="eye" class="h-5 w-5" />
                        </button>
                    </div>
                    @error('password')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-ink-600 dark:text-ink-300">
                    <input type="checkbox" name="remember" value="1" class="checkbox"> Meni eslab qol
                </label>

                <button type="submit" class="btn-primary w-full py-3 text-base">Kirish <x-icon name="send" class="h-4 w-4" /></button>
                <a href="{{ route('password.forgot') }}" class="block text-center text-sm font-medium text-ink-500 transition hover:text-brand-600">Parolni unutdingizmi?</a>
            </form>
            @include('partials.legal-links')
        </div>
    </div>
</div>
@include('partials.cookie-banner')
</body>
</html>
