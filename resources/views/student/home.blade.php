@extends('layouts.app')
@section('title', 'Bosh sahifa')

@section('content')
    <x-page-header icon="cap" :title="'Salom, '.$student->name" subtitle="Sizning shaxsiy kabinetingiz" />

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-stat label="Balans" :value="$student->balance" icon="wallet" :tone="$student->balance < 0 ? 'red' : 'emerald'" unit="money" :sub="$student->balance < 0 ? 'qarzdorlik' : null" />
        <x-stat label="Faol guruhlar" :value="$groups->count()" icon="users" />
        <x-stat label="Umumiy davomat" :value="$overallRate !== null ? $overallRate : '—'" icon="calendar" tone="blue" :unit="$overallRate !== null ? 'percent' : 'raw'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card card-hover card-body">
            <h2 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">Guruhlarim</h2>
            @forelse ($groups as $g)
                <div class="todo-row -mx-2 flex items-center justify-between rounded-lg border-b border-ink-100 px-2 py-2 text-sm last:border-0 dark:border-ink-800">
                    <div><div class="font-medium text-ink-900 dark:text-white">{{ $g->name }}</div><div class="text-xs text-ink-500">{{ $g->course?->name }} · {{ $g->teacher?->name }}</div></div>
                    <div class="text-xs text-ink-500">{{ $g->starts_on?->format('d.m.Y') }} — {{ $g->ends_on?->format('d.m.Y') }}</div>
                </div>
            @empty
                <x-empty icon="users" title="Hozircha faol guruh yo'q" />
            @endforelse
        </div>

        <div class="card card-hover card-body">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-base font-semibold text-ink-900 dark:text-white">So'nggi harakatlar</h2>
                <a href="{{ route('student.payments') }}" class="text-xs font-semibold text-brand-600">Hammasi</a>
            </div>
            @forelse ($transactions as $t)
                <div class="todo-row -mx-2 flex items-center justify-between rounded-lg border-b border-ink-100 px-2 py-2 text-sm last:border-0 dark:border-ink-800">
                    <div><div class="font-medium text-ink-900 dark:text-white">{{ $t->typeLabel() }}</div><div class="text-xs text-ink-500">{{ $t->group?->name }} · {{ $t->created_at->format('d.m.Y H:i') }}</div></div>
                    <div class="font-semibold {{ $t->amount < 0 ? 'text-danger-600' : 'text-emerald-600' }}">{{ $t->amount > 0 ? '+' : '' }}{{ \App\Support\Format::money($t->amount) }}</div>
                </div>
            @empty
                <x-empty icon="wallet" title="Hozircha harakat yo'q" />
            @endforelse
        </div>
    </div>

    @if (! empty($certificates) && $certificates->isNotEmpty())
        <div class="card card-body mt-6">
            <h2 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">Sertifikatlarim</h2>
            @foreach ($certificates as $c)
                <div class="flex items-center justify-between gap-3 border-t border-ink-100 py-2 text-sm first:border-0 dark:border-ink-800">
                    <div><b>{{ $c->course_name }}</b> <span class="text-xs text-ink-500">· {{ $c->issued_on->format('d.m.Y') }} · {{ $c->number }}</span></div>
                    <a href="{{ route('student.certificates.mine', $c->id) }}" target="_blank" class="btn-secondary btn-sm">PDF</a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
