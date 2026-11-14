<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rozilik · {{ \App\Support\Brand::name() }}</title>
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-ink-50 dark:bg-ink-950">
<div class="mx-auto flex min-h-full max-w-2xl flex-col justify-center px-4 py-6 sm:py-10"
     x-data="{ n: {{ $pending->count() }}, ok: {}, get all() { return Object.values(this.ok).filter(Boolean).length === this.n }, toggleAll(v) { @foreach ($pending as $d) this.ok['{{ $d->type }}'] = v; @endforeach } }">
    <div class="rounded-3xl bg-white p-5 shadow-xl ring-1 ring-black/5 dark:bg-ink-900 dark:ring-white/10 sm:p-8">
        <x-brand-mark class="mb-5" />

        <h1 class="text-xl font-bold tracking-tight text-ink-900 dark:text-white sm:text-2xl">
            {{ $updated ? 'Hujjatlar yangilandi' : 'Davom etishdan oldin' }}
        </h1>
        <p class="mt-1.5 text-sm leading-relaxed text-ink-500 dark:text-ink-400">
            {{ $updated
                ? "Quyidagi hujjatlarning yangi versiyasi e'lon qilindi. Tizimdan foydalanishda davom etish uchun ularni o'qib chiqib, rozilik bering."
                : "Tizimdan foydalanish uchun quyidagi hujjatlar bilan tanishib, ularni qabul qilishingiz kerak. Roziligingiz sanasi va hujjat versiyasi jurnalga yoziladi." }}
        </p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-danger-50 px-4 py-3 text-sm text-danger-700 dark:bg-danger-950/60 dark:text-danger-300" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('legal.accept') }}" class="mt-5">
            @csrf
            <div class="space-y-3">
                @foreach ($pending as $d)
                    <div class="rounded-2xl border border-ink-100 dark:border-ink-800" x-data="{ open: false }">
                        <div class="flex items-center gap-3 p-3.5">
                            <label class="flex min-w-0 flex-1 cursor-pointer items-start gap-3 text-sm text-ink-700 dark:text-ink-200">
                                <input type="checkbox" name="agree[]" value="{{ $d->type }}" x-model="ok['{{ $d->type }}']" class="checkbox mt-0.5">
                                <span class="min-w-0"><b class="text-ink-900 dark:text-white">{{ $d->title }}</b> bilan tanishdim va roziman <span class="text-xs text-ink-400">(v{{ $d->version }})</span></span>
                            </label>
                            <button type="button" @click="open = !open" class="shrink-0 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-950/60" :aria-expanded="open">
                                <span x-text="open ? 'Yopish' : 'O\'qish'"></span>
                            </button>
                        </div>
                        <div x-show="open" x-cloak x-transition.opacity class="border-t border-ink-100 dark:border-ink-800">
                            <div class="legal-prose max-h-72 overflow-y-auto p-4 text-ink-700 dark:text-ink-200">{!! \App\Services\LegalService::render($d->body) !!}</div>
                            <div class="border-t border-ink-100 px-4 py-2 text-right dark:border-ink-800"><a href="{{ route('legal.show', $d->type) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-brand-600 hover:underline">Alohida sahifada ochish ↗</a></div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($pending->count() > 1)
                <label class="mt-4 flex cursor-pointer items-center gap-3 text-sm font-semibold text-ink-700 dark:text-ink-200">
                    <input type="checkbox" class="checkbox" :checked="all" @change="toggleAll($event.target.checked)"> Hammasiga roziman
                </label>
            @endif

            <div class="mt-6 grid gap-3 sm:grid-cols-[1fr_auto]">
                <button type="submit" class="btn-primary justify-center py-3 disabled:cursor-not-allowed disabled:opacity-50" :disabled="!all">Qabul qilaman va davom etaman</button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
            @csrf
            <button class="text-sm text-ink-500 hover:text-brand-600">Rozi emasman — tizimdan chiqish</button>
        </form>
    </div>
</div>
</body>
</html>
