@extends('layouts.app')
@section('title', 'Guruhlarim')

@section('content')
    <x-page-header title="Guruhlarim" subtitle="Siz a'zo bo'lgan va bo'lgan barcha guruhlar" />
    <div class="card overflow-hidden"><div class="table-wrap">
        <table class="table">
            <thead><tr><th>Guruh</th><th>Kurs</th><th>O'qituvchi</th><th>Boshlanishi</th><th>Tugashi</th><th>Holat</th></tr></thead>
            <tbody>
            @forelse ($groups as $g)
                <tr>
                    <td class="font-medium text-ink-900 dark:text-white">{{ $g->name }}</td>
                    <td>{{ $g->course?->name ?? '—' }}</td>
                    <td>{{ $g->teacher?->name ?? '—' }}</td>
                    <td>{{ $g->starts_on?->format('d.m.Y') }}</td>
                    <td>{{ $g->ends_on?->format('d.m.Y') }}</td>
                    <td><span class="{{ $g->is_active_member ? 'badge-green' : 'badge-gray' }}">{{ $g->is_active_member ? 'Faol' : 'Chiqqan' }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-500">Siz hali guruhga qo'shilmagansiz.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
@endsection
