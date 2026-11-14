@extends('layouts.app')
@section('title', $video->title)

@section('content')
    <x-page-header icon="play" :title="$video->title" :subtitle="$video->category ?: 'Video dars'">
        <x-slot:actions>
            @if ($canManage)<a href="{{ route('tutorials.admin') }}" class="btn-secondary btn-sm">Boshqarish</a>@endif
            <a href="{{ route('tutorials.index') }}" class="btn-secondary btn-sm">← Barcha videolar</a>
        </x-slot:actions>
    </x-page-header>

    @php $embed = $video->embed; @endphp
    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-4">
            <div class="card overflow-hidden" x-data="{ playing: false }">
                <div class="relative aspect-video w-full bg-black">
                    <template x-if="playing">
                        <div class="absolute inset-0">
                            @if ($embed['kind'] === 'iframe')
                                <iframe src="{{ $video->playSrc() }}" title="{{ $video->title }}" class="h-full w-full" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                        allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
                            @elseif ($embed['kind'] === 'video')
                                <video src="{{ $embed['src'] }}" class="h-full w-full" controls controlsList="nodownload" playsinline autoplay preload="metadata"></video>
                            @else
                                <div class="flex h-full flex-col items-center justify-center gap-3 p-6 text-center text-white">
                                    <p class="text-sm text-white/80">Bu video tizim ichida ijro etilmaydi.</p>
                                    <a href="{{ $video->url }}" target="_blank" rel="noopener noreferrer" class="btn-secondary btn-sm">Havolani alohida ochish</a>
                                </div>
                            @endif
                        </div>
                    </template>
                    <button type="button" x-show="!playing" @click="playing = true" class="group absolute inset-0 grid place-items-center bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900" aria-label="Videoni ijro etish">
                        <span class="absolute inset-0 opacity-30" style="background-image:radial-gradient(circle at 18% 22%,#fff 0,transparent 40%),radial-gradient(circle at 88% 88%,#ff8159 0,transparent 38%)"></span>
                        <span class="relative grid h-20 w-20 place-items-center rounded-full bg-white/95 text-brand-700 shadow-pop transition duration-300 group-hover:scale-110 sm:h-24 sm:w-24">
                            <svg viewBox="0 0 24 24" class="ml-1 h-9 w-9 fill-current sm:h-11 sm:w-11"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l11-6.5a1 1 0 0 0 0-1.72l-11-6.5A1 1 0 0 0 8 5.5z"/></svg>
                        </span>
                        <span class="absolute bottom-4 left-5 right-5 text-left text-sm font-semibold text-white/90 drop-shadow">{{ $video->title }}@if ($video->duration) · {{ $video->duration }}@endif</span>
                    </button>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($video->category)<span class="badge-brand">{{ $video->category }}</span>@endif
                    @if ($video->duration)<span class="badge-gray tabular-nums">{{ $video->duration }}</span>@endif
                    @unless ($video->is_active)<span class="badge-amber">Yashirin (faqat siz ko'rasiz)</span>@endunless
                </div>
                @if ($video->description)<p class="mt-3 text-sm leading-relaxed text-ink-600 dark:text-ink-300">{{ $video->description }}</p>@endif
                <div class="mt-5 grid grid-cols-2 gap-3 border-t border-ink-100 pt-4 dark:border-ink-800">
                    @if ($prev)
                        <a href="{{ route('tutorials.show', $prev) }}" class="btn-secondary justify-start text-left"><span class="min-w-0"><span class="block text-[11px] font-bold uppercase tracking-wider text-ink-400">← Oldingi</span><span class="block truncate">{{ $prev->title }}</span></span></a>
                    @else<span></span>@endif
                    @if ($next)
                        <a href="{{ route('tutorials.show', $next) }}" class="btn-primary justify-end text-right"><span class="min-w-0"><span class="block text-[11px] font-bold uppercase tracking-wider text-white/70">Keyingi →</span><span class="block truncate">{{ $next->title }}</span></span></a>
                    @endif
                </div>
            </div>
        </div>

        <aside class="card overflow-hidden xl:sticky xl:top-24">
            <div class="border-b border-ink-100 px-5 py-3.5 text-sm font-bold text-ink-800 dark:border-ink-800 dark:text-ink-100">Barcha darslar <span class="font-medium text-ink-400">· {{ $playlist->count() }}</span></div>
            <ol class="max-h-[28rem] divide-y divide-ink-100 overflow-y-auto dark:divide-ink-800">
                @foreach ($playlist as $n => $v)
                    <li>
                        <a href="{{ route('tutorials.show', $v) }}" @class(['flex items-center gap-3 px-5 py-3 text-sm transition hover:bg-brand-50/70 dark:hover:bg-ink-800/60', 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-950/40 dark:text-brand-300' => $v->id === $video->id])>
                            <span @class(['grid h-7 w-7 shrink-0 place-items-center rounded-full text-xs font-bold tabular-nums', 'bg-brand-600 text-white' => $v->id === $video->id, 'bg-ink-100 text-ink-500 dark:bg-ink-800 dark:text-ink-300' => $v->id !== $video->id])>{{ $n + 1 }}</span>
                            <span class="min-w-0 flex-1 truncate">{{ $v->title }}</span>
                            @if ($v->duration)<span class="shrink-0 text-xs tabular-nums text-ink-400">{{ $v->duration }}</span>@endif
                        </a>
                    </li>
                @endforeach
            </ol>
        </aside>
    </div>
@endsection
