<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Parolni tiklash · {{ \App\Support\Brand::name() }}</title>
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @include('partials.fx-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" data-fx-mode="{{ \App\Support\Edu3D::current()['mode'] }}">
@include('partials.edu-bg', ['scene' => \App\Support\Edu3D::current()['scene']])
<div class="flex min-h-full items-center justify-center bg-white/55 p-6 dark:bg-ink-950/55">
    <div class="w-full max-w-sm">
        <x-brand-mark class="mb-8" />
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">Yangi parol</h1>
        <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-400">Kamida 8 belgi. Eski parol hech qayerga yuborilmaydi.</p>
        <x-alert />
        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
            @csrf
            <x-input name="password" type="password" label="Yangi parol" autocomplete="new-password" required autofocus />
            <x-input name="password_confirmation" type="password" label="Parolni takrorlang" autocomplete="new-password" required />
            <button class="btn-primary w-full py-3">Parolni saqlash</button>
        </form>
        <a href="{{ route('login') }}" class="mt-6 block text-center text-sm text-ink-500 hover:text-brand-600">Kirish sahifasiga qaytish</a>
        @include('partials.legal-links')
    </div>
</div>
@include('partials.cookie-banner')
</body>
</html>
