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
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">Akkauntni tanlang</h1>
        <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-400">Bu raqamga bir nechta akkaunt biriktirilgan. Qaysi birining parolini tiklaysiz?</p>
        <x-alert />
        <div class="mt-6 space-y-3">
            @foreach ($accounts as $a)
                <form method="POST" action="{{ route('password.pick') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $a->id }}">
                    <button class="w-full rounded-xl border border-ink-200 p-4 text-left transition hover:border-brand-500 hover:bg-brand-50 dark:border-ink-700 dark:hover:bg-ink-800">
                        <div class="font-semibold text-ink-900 dark:text-white">{{ $a->name }}</div>
                        <div class="mt-0.5 text-xs text-ink-500">{{ $a->role->label() }}{{ $a->branch ? ' · '.$a->branch->name : '' }}</div>
                    </button>
                </form>
            @endforeach
        </div>
        <a href="{{ route('login') }}" class="mt-6 block text-center text-sm text-ink-500 hover:text-brand-600">Kirish sahifasiga qaytish</a>
        @include('partials.legal-links')
    </div>
</div>
@include('partials.cookie-banner')
</body>
</html>
