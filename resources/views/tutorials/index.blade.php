@extends('layouts.app')
@section('title', 'Video darslar')

@php
    // Kategoriyaga qarab kartochka rangi (har kategoriya o'z rangida, "o'zi" doim bir xil)
    $tones = [
        'from-brand-500 to-brand-800',
        'from-sky-500 to-indigo-700',
        'from-emerald-500 to-teal-700',
        'from-orange-400 to-rose-600',
        'from-pink-500 to-violet-700',
        'from-cyan-500 to-blue-700',
    ];
    $tone = fn (?string $c) => $tones[crc32((string) $c) % count($tones)];
@endphp

@section('content')
    <x-page-header icon="play" title="Video darslar" subtitle="Tizimdan foydalanishni video orqali o'rganing">
        @if ($canManage)
            <x-slot:actions><a href="{{ route('tutorials.admin') }}" class="btn-secondary btn-sm"><x-icon name="cog" colored class="h-4 w-4" /> Boshqarish</a></x-slot:actions>
        @endif
    </x-page-header>

    @if ($videos->isEmpty())
        <div class="card card-body">
            <x-empty icon="play" title="Hozircha video darslar yo'q" :text="$canManage ? 'Sozlamalar bo\'limidan birinchi video darsni qo\'shing.' : 'Tez orada bu yerda tizim bo\'yicha video qo\'llanmalar paydo bo\'ladi.'">
                @if ($canManage)<a href="{{ route('tutorials.admin') }}" class="btn-primary btn-sm mt-3">Video qo'shish</a>@endif
            </x-empty>
        </div>
    @else
        <div x-data="{ q: '', cat: '' }">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative sm:w-80">
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" x-model.debounce.150ms="q" placeholder="Video qidirish…" class="input pl-10">
                </div>
                @if ($categories->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="cat = ''" class="rounded-full px-3.5 py-1.5 text-sm font-semibold transition" :class="cat === '' ? 'bg-brand-600 text-white shadow-glow' : 'bg-white text-ink-600 ring-1 ring-ink-200 hover:bg-brand-50 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700'">Hammasi <span class="opacity-70">{{ $videos->count() }}</span></button>
                        @foreach ($categories as $c)
                            <button type="button" @click="cat = @js($c)" class="rounded-full px-3.5 py-1.5 text-sm font-semibold transition" :class="cat === @js($c) ? 'bg-brand-600 text-white shadow-glow' : 'bg-white text-ink-600 ring-1 ring-ink-200 hover:bg-brand-50 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-700'">{{ $c }}</button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($videos as $n => $v)
                    <a href="{{ route('tutorials.show', $v) }}"
                       x-show="(cat === '' || cat === @js($v->category)) && (q === '' || @js(mb_strtolower($v->title.' '.$v->description.' '.$v->category)).includes(q.toLowerCase()))"
                       x-transition.opacity.duration.200ms
                       class="card card-hover group block overflow-hidden">
                        <div class="relative aspect-video overflow-hidden bg-gradient-to-br {{ $tone($v->category) }}">
                            <div class="absolute inset-0 opacity-30" style="background-image:radial-gradient(circle at 20% 20%,#fff 0,transparent 40%),radial-gradient(circle at 85% 90%,#fff 0,transparent 35%)"></div>
                            <span class="absolute left-4 top-3 text-5xl font-black leading-none text-white/20">{{ str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="absolute inset-0 grid place-items-center">
                                <span class="grid h-16 w-16 place-items-center rounded-full bg-white/95 text-brand-700 shadow-pop transition duration-300 group-hover:scale-110">
                                    <svg viewBox="0 0 24 24" class="ml-1 h-7 w-7 fill-current"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l11-6.5a1 1 0 0 0 0-1.72l-11-6.5A1 1 0 0 0 8 5.5z"/></svg>
                                </span>
                            </span>
                            @if ($v->duration)<span class="absolute bottom-3 right-3 rounded-lg bg-black/60 px-2 py-0.5 text-xs font-semibold tabular-nums text-white backdrop-blur">{{ $v->duration }}</span>@endif
                        </div>
                        <div class="card-body">
                            @if ($v->category)<span class="badge-brand mb-2">{{ $v->category }}</span>@endif
                            <h3 class="text-base font-bold leading-snug text-ink-900 dark:text-white">{{ $v->title }}</h3>
                            @if ($v->description)<p class="mt-1 line-clamp-2 text-sm text-ink-500 dark:text-ink-400">{{ $v->description }}</p>@endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div x-show="false" x-cloak></div>
        </div>
    @endif
@endsection
