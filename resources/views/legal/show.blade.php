<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $doc->title }} · {{ \App\Support\Brand::name() }}</title>
    <meta name="robots" content="noindex">
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ink-50 text-ink-800 dark:bg-ink-950 dark:text-ink-100">
<div class="mx-auto max-w-3xl px-4 py-8 sm:py-12">
    <div class="mb-6 flex items-center justify-between gap-3">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-3"><x-logo /><span class="text-xl font-extrabold tracking-tight text-ink-900 dark:text-white">{{ \App\Support\Brand::short() }}</span></a>
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="text-sm text-ink-500 hover:text-brand-600">← Orqaga</a>
    </div>

    <div class="mb-4 flex flex-wrap gap-2 text-sm font-semibold">
        @foreach ($types as $t => $label)
            <a href="{{ route('legal.show', $t) }}" class="rounded-lg px-3 py-1.5 {{ $doc->type === $t ? 'bg-brand-600 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-100 hover:bg-ink-100 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-800' }}">{{ $label }}</a>
        @endforeach
    </div>

    <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ $doc->title }}</h1>
        <p class="mt-1 text-xs text-ink-400">Versiya {{ $doc->version }} · {{ ($doc->published_at ?? $doc->updated_at)->timezone('Asia/Tashkent')->format('d.m.Y') }} dan kuchda</p>
        <div class="legal-prose mt-6 text-ink-700 dark:text-ink-200">{!! $html !!}</div>
    </article>
    @include('partials.legal-links', ['class' => 'mt-6'])
</div>
@guest @include('partials.cookie-banner') @endguest
</body>
</html>
