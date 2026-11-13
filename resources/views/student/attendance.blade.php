@extends('layouts.app')
@section('title', 'Davomad')

@section('content')
    <x-page-header title="Davomad" :subtitle="$overall !== null ? 'Umumiy davomat: '.$overall.'%' : 'Davomat ma\'lumoti hali yo\'q'" />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($rows as $r)
            <div class="card card-body">
                <div class="text-sm font-medium text-ink-900 dark:text-white">{{ $r['group'] }}</div>
                <div class="mt-2 text-2xl font-bold text-ink-900 dark:text-white">{{ $r['rate'] !== null ? $r['rate'].'%' : '—' }}</div>
                <div class="mt-1 text-xs text-ink-500">{{ $r['present'] }} / {{ $r['total'] }} dars</div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800"><div class="h-full rounded-full bg-emerald-500" style="width: {{ (int) ($r['rate'] ?? 0) }}%"></div></div>
            </div>
        @empty
            <p class="text-sm text-ink-500">Davomat ma'lumoti hali yo'q.</p>
        @endforelse
    </div>

    <div class="card overflow-hidden"><div class="table-wrap">
        <table class="table">
            <thead><tr><th>Sana</th><th>Guruh</th><th>Holat</th></tr></thead>
            <tbody>
            @forelse ($recent as $a)
                <tr>
                    <td>{{ $a->date->format('d.m.Y') }}</td>
                    <td>{{ $a->group?->name ?? '—' }}</td>
                    <td><span class="{{ $a->is_present ? 'badge-green' : 'badge-red' }}">{{ $a->is_present ? 'Keldi' : 'Kelmadi' }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3" class="py-8 text-center text-ink-500">So'nggi yozuvlar yo'q.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
@endsection
