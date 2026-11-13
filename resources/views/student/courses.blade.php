@extends('layouts.app')
@section('title', 'Kurs materiallari')

@section('content')
    <x-page-header title="Kurs materiallari" subtitle="Faol guruhlaringiz kurslarining video, audio va testlari" />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $c)
            <a href="{{ route('student.courses.show', $c) }}" class="card card-body transition hover:shadow-md">
                <div class="text-base font-semibold text-ink-900 dark:text-white">{{ $c->name }}</div>
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-ink-500">
                    <span class="badge-gray">{{ $c->videos_count }} video</span>
                    <span class="badge-gray">{{ $c->audios_count }} audio</span>
                    <span class="badge-gray">{{ $c->questions_count }} savol</span>
                </div>
                <div class="mt-3 text-xs text-ink-500">
                    @if (isset($best[$c->id])) Eng yaxshi natija: <b class="text-ink-900 dark:text-white">{{ $best[$c->id]->best }}%</b> · {{ $best[$c->id]->attempts }} urinish @else Test hali topshirilmagan @endif
                </div>
            </a>
        @empty
            <p class="text-sm text-ink-500">Hozircha faol guruh yo'q, shuning uchun materiallar ko'rinmaydi.</p>
        @endforelse
    </div>

    @if ($books->isNotEmpty())
        <h2 class="mb-3 mt-8 text-base font-semibold text-ink-900 dark:text-white">Kitoblar</h2>
        <div class="card card-body space-y-2">
            @foreach ($books as $b)
                <div class="flex items-center justify-between text-sm"><span>{{ $b->name }}</span>@if ($b->url)<a href="{{ $b->url }}" target="_blank" rel="noopener" class="font-semibold text-brand-600">Ochish</a>@endif</div>
            @endforeach
        </div>
    @endif
@endsection
