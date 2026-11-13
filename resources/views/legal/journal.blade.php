@extends('layouts.app')
@section('title', 'Rozilik jurnali')

@section('content')
    <x-page-header title="Rozilik jurnali" subtitle="Kim, qachon va qaysi versiyadagi hujjatni qabul qilgani">
        <x-slot:actions>
        <a href="{{ route('legal.admin') }}" class="btn-secondary btn-sm">← Hujjatlar</a>
        <a href="{{ route('legal.journal.export', $filters) }}" class="btn-secondary btn-sm">CSV yuklab olish</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="card card-body mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1.4fr_auto_auto_auto] lg:items-end">
        <div>
            <label class="label">Hujjat</label>
            <select name="type" class="input">
                <option value="">Barchasi</option>
                @foreach ($types as $t => $label)<option value="{{ $t }}" @selected(($filters['type'] ?? '') === $t)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div><label class="label">Foydalanuvchi yoki IP</label><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="Ism, IP manzil"></div>
        <div><label class="label">Dan</label><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="input"></div>
        <div><label class="label">Gacha</label><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="input"></div>
        <button class="btn-secondary justify-center">Ko'rsatish</button>
    </form>

    <div class="card overflow-hidden">
        {{-- Telefonda kartochka ko'rinishida, kompyuterda jadval --}}
        <div class="divide-y divide-ink-100 dark:divide-ink-800 md:hidden">
            @forelse ($rows as $r)
                <div class="space-y-1 p-4 text-sm">
                    <div class="flex items-center justify-between gap-2"><b class="text-ink-900 dark:text-white">{{ $r->user_name }}</b><span class="badge-gray">v{{ $r->version }}</span></div>
                    <div class="text-ink-600 dark:text-ink-300">{{ $types[$r->document_type] ?? $r->document_type }}</div>
                    <div class="text-xs text-ink-400">{{ $r->accepted_at->timezone('Asia/Tashkent')->format('d.m.Y H:i') }} · {{ $r->ip }}</div>
                </div>
            @empty
                <p class="p-6 text-center text-sm text-ink-400">Hali yozuv yo'q.</p>
            @endforelse
        </div>
        <div class="table-wrap hidden md:block">
            <table class="table">
                <thead><tr><th>Sana</th><th>Foydalanuvchi</th><th>Hujjat</th><th>Versiya</th><th>IP</th><th>Qurilma</th></tr></thead>
                <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td class="whitespace-nowrap">{{ $r->accepted_at->timezone('Asia/Tashkent')->format('d.m.Y H:i') }}</td>
                        <td class="font-medium text-ink-900 dark:text-white">{{ $r->user_name }} <span class="text-xs font-normal text-ink-400">{{ $r->user_role }}</span></td>
                        <td>{{ $types[$r->document_type] ?? $r->document_type }}</td>
                        <td><span class="badge-gray">v{{ $r->version }}</span></td>
                        <td class="whitespace-nowrap text-xs text-ink-500">{{ $r->ip }}</td>
                        <td class="max-w-xs truncate text-xs text-ink-400" title="{{ $r->user_agent }}">{{ $r->user_agent }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-8 text-center text-ink-400">Hali yozuv yo'q.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $rows->links() }}</div>
@endsection
