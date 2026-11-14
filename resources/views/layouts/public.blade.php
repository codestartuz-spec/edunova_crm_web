<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Murojaat') · {{ isset($branch) ? $branch->name : \App\Support\Brand::short() }}</title>
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (isset($branch))
        <style>:root { --brand-public: {{ $branch->effectiveBrandColor() }}; }</style>
    @endif
</head>
<body class="min-h-full">
<div class="mx-auto flex min-h-full max-w-lg flex-col justify-center px-4 py-10">
    @if (isset($branch))
        {{-- v9 (3-band): umumiy brend o'rniga shu filialning nomi, rangi va ma'lumoti --}}
        <div class="login-rise mb-6 flex flex-col items-center gap-3 text-center">
            <span class="flex h-16 w-16 items-center justify-center rounded-3xl text-2xl font-extrabold text-white shadow-glow ring-4 ring-white/60 dark:ring-white/10" style="background: linear-gradient(135deg, {{ $branch->effectiveBrandColor() }}, color-mix(in srgb, {{ $branch->effectiveBrandColor() }} 62%, #000))">{{ mb_strtoupper(mb_substr($branch->name, 0, 1)) }}</span>
            <span class="text-xl font-extrabold tracking-tight text-ink-900 dark:text-white">{{ $branch->name }}</span>
            @if ($branch->public_about)
                <p class="max-w-sm text-sm text-ink-500 dark:text-ink-400">{{ $branch->public_about }}</p>
            @endif
        </div>
    @else
        <div class="login-rise mb-6 flex items-center justify-center gap-3">
            <x-logo />
            <span class="text-xl font-extrabold tracking-tight text-ink-900 dark:text-white">{{ \App\Support\Brand::short() }}</span>
        </div>
    @endif
    <div class="login-rise login-rise-2">
        @yield('content')
    </div>
    @include('partials.legal-links', ['class' => 'mt-8'])
    <p class="mt-3 text-center text-xs text-ink-400">
        @if (isset($branch))
            &copy; {{ date('Y') }} {{ $branch->name }}
        @else
            &copy; {{ date('Y') }} {{ \App\Support\Brand::name() }}
        @endif
    </p>
</div>
@include('partials.cookie-banner')
</body>
</html>
