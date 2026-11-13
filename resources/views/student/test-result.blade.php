@extends('layouts.app')
@section('title', 'Test natijasi')

@php
    $score = $review['score'];
    $good = $score >= 60;
    $unanswered = collect($review['items'])->whereNull('chosen')->count();
    $wrong = $review['total'] - $review['correct'] - ($review['has_answers'] ? $unanswered : 0);
    $circ = 2 * pi() * 52;
    $msg = $score >= 85 ? "A'lo natija!" : ($score >= 60 ? 'Yaxshi natija!' : "Yana bir bor urinib ko'ring");
    $letters = ['A', 'B', 'C', 'D'];
@endphp

@section('content')
    <x-page-header :title="($course?->name ?? 'Kurs').' — natija'" :subtitle="$attempt->finished_at?->format('d.m.Y H:i')">
        <x-slot:actions>
            @if ($course)
                <a href="{{ route('student.courses.show', $course) }}#test" class="btn-secondary">Kursga qaytish</a>
                <form method="POST" action="{{ route('student.tests.start', $course) }}">@csrf<button class="btn-primary">Qayta urinish</button></form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="card card-body mb-6 flex flex-col items-center gap-6 sm:flex-row">
        <div class="relative h-36 w-36 flex-none">
            <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90">
                <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" class="stroke-ink-100 dark:stroke-ink-800" />
                <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round"
                        class="{{ $good ? 'stroke-emerald-500' : 'stroke-brand-600' }}"
                        stroke-dasharray="{{ round($circ * $score / 100, 1) }} {{ round($circ, 1) }}" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-extrabold {{ $good ? 'text-emerald-600' : 'text-brand-600' }}">{{ $score }}%</span>
            </div>
        </div>
        <div class="text-center sm:text-left">
            <div class="text-xl font-bold text-ink-900 dark:text-white">{{ $msg }}</div>
            <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                <span class="badge-green">To'g'ri: {{ $review['correct'] }}</span>
                @if ($review['has_answers'])
                    <span class="badge-red">Noto'g'ri: {{ $wrong }}</span>
                    @if ($unanswered)<span class="badge-gray">Javobsiz: {{ $unanswered }}</span>@endif
                @endif
                <span class="badge-gray">Jami: {{ $review['total'] }}</span>
            </div>
        </div>
    </div>

    @if (! $review['has_answers'])
        <p class="text-sm text-ink-500">Bu urinishning savollar bo'yicha tahlili saqlanmagan.</p>
    @else
        <h2 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">Javoblar tahlili</h2>
        <div class="max-w-3xl space-y-4">
            @foreach ($review['items'] as $n => $item)
                <div class="card card-body">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div class="text-sm font-semibold text-ink-900 dark:text-white">{{ $n + 1 }}. {{ $item['question'] }}</div>
                        <span class="flex-none {{ $item['is_correct'] ? 'badge-green' : ($item['chosen'] === null ? 'badge-gray' : 'badge-red') }}">{{ $item['is_correct'] ? "To'g'ri" : ($item['chosen'] === null ? 'Javobsiz' : "Noto'g'ri") }}</span>
                    </div>
                    <div class="space-y-2">
                        @foreach ($item['options'] as $oi => $opt)
                            @php
                                $isRight = $oi === $item['correct_index'];
                                $isChosen = $oi === $item['chosen'];
                                $cls = $isRight ? 'border-emerald-500 bg-emerald-50 text-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200'
                                    : ($isChosen ? 'border-red-400 bg-red-50 text-red-900 dark:bg-red-900/20 dark:text-red-200' : 'border-ink-200 text-ink-600 dark:border-ink-700 dark:text-ink-300');
                            @endphp
                            <div class="flex items-center gap-3 rounded-xl border px-4 py-2.5 text-sm {{ $cls }}">
                                <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-white/70 text-xs font-bold dark:bg-black/20">{{ $letters[$oi] ?? '' }}</span>
                                <span class="flex-1">{{ $opt }}</span>
                                @if ($isRight)<span class="text-xs font-semibold">To'g'ri javob</span>@elseif ($isChosen)<span class="text-xs font-semibold">Sizning javobingiz</span>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
