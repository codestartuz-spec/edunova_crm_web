<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kirish · {{ \App\Support\Brand::name() }}</title>
    @include('partials.pwa-head')
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div class="grid min-h-full lg:grid-cols-2">
    <div class="relative hidden overflow-hidden login-hero lg:flex lg:flex-col lg:justify-between lg:p-12">
        <div class="login-orb absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
        <div class="login-orb login-orb-2 absolute -bottom-32 -left-16 h-[28rem] w-[28rem] rounded-full bg-black/15"></div>
        <div class="relative flex items-center gap-3 text-white">
            <span class="inline-flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-white text-xl font-black text-brand-600">@if ($logo = \App\Support\Brand::logoUrl())<img src="{{ $logo }}" alt="" class="h-full w-full object-contain">@else{{ \App\Support\Brand::initial() }}@endif</span>
            <span class="text-2xl font-extrabold tracking-tight">{{ \App\Support\Brand::short() }}</span>
        </div>
        <div class="login-rise login-rise-2 relative max-w-md text-white">
            <h2 class="text-4xl font-extrabold leading-tight tracking-tight">{{ \App\Support\Brand::tagline() }}</h2>
            <p class="mt-4 text-lg text-brand-100">Filiallar, o'quvchilar, davomad, to'lovlar va hisobotlar — barchasi bir joyda.</p>
        </div>
        <p class="relative text-sm text-brand-200">© {{ date('Y') }} {{ \App\Support\Brand::name() }}</p>
    </div>

    <div class="flex items-center justify-center bg-white p-6 dark:bg-ink-950 sm:p-12">
        <div class="login-rise w-full max-w-sm">
            <x-brand-mark class="mb-8 lg:hidden" />

            <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">Tizimga kirish</h1>
            <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-400">Login (yoki telefon raqami) va parolingizni kiriting.</p>

            <form method="POST" action="{{ route('login') }}" class="login-rise login-rise-3 mt-8 space-y-5">
                @csrf
                <x-input name="login" label="Login, email yoki telefon" autocomplete="username" required autofocus />
                <x-input name="password" type="password" label="Parol" autocomplete="current-password" required />

                <label class="flex items-center gap-2 text-sm text-ink-600 dark:text-ink-300">
                    <input type="checkbox" name="remember" value="1" class="checkbox"> Meni eslab qol
                </label>

                <button type="submit" class="btn-primary w-full py-3">Kirish</button>
                <a href="{{ route('password.forgot') }}" class="block text-center text-sm text-ink-500 hover:text-brand-600">Parolni unutdingizmi?</a>
            </form>
            @include('partials.legal-links')
        </div>
    </div>
</div>
@include('partials.cookie-banner')
</body>
</html>
