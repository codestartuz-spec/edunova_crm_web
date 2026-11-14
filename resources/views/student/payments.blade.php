@extends('layouts.app')
@section('title', "To'lovlar va balans")

@section('content')
    <x-page-header title="To'lovlar va balans" subtitle="Hisobingizdagi barcha harakatlar" />

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="card card-body">
            <div class="text-sm text-ink-500">Joriy balans</div>
            <div class="mt-1 text-2xl font-bold {{ $student->balance < 0 ? 'text-danger-600' : 'text-emerald-600' }}">{{ \App\Support\Format::money($student->balance) }}</div>
        </div>
        <div class="card card-body">
            <div class="text-sm text-ink-500">Qarzdorlik</div>
            <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white">{{ \App\Support\Format::money(max(0, -$student->balance)) }}</div>
        </div>
    </div>

    <div class="card overflow-hidden"><div class="table-wrap">
        <table class="table">
            <thead><tr><th>Sana</th><th>Turi</th><th>Guruh</th><th class="text-right">Summa</th><th class="text-right">Balans</th><th>Izoh</th></tr></thead>
            <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td class="whitespace-nowrap">{{ $t->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $t->typeLabel() }}</td>
                    <td>{{ $t->group?->name ?? '—' }}</td>
                    <td class="text-right font-semibold {{ $t->amount < 0 ? 'text-danger-600' : 'text-emerald-600' }}">{{ $t->amount > 0 ? '+' : '' }}{{ \App\Support\Format::money($t->amount) }}</td>
                    <td class="text-right">{{ \App\Support\Format::money($t->balance_after) }}</td>
                    <td class="text-xs text-ink-500">{{ $t->note }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-500">Hali harakat yo'q.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-4">{{ $transactions->links() }}</div>
@endsection
