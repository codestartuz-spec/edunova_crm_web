@extends('layouts.app')
@section('title', $n->title)

@section('content')
    <x-page-header :title="$n->title" :subtitle="$n->created_at->format('d.m.Y H:i').' · '.($n->sender?->name ?? '')">
        <x-slot:actions><a href="{{ route('notifications.index') }}" class="btn-secondary">Orqaga</a></x-slot:actions>
    </x-page-header>
    <div class="card card-body mb-6 text-sm">{{ $n->body }}@if ($n->url)<div class="mt-2 text-xs text-ink-500">Havola: {{ $n->url }}</div>@endif</div>
    <div class="card overflow-hidden"><div class="table-wrap"><table class="table">
        <thead><tr><th>Hodim</th><th>Lavozim</th><th>O'qilgan</th><th>Push</th></tr></thead>
        <tbody>
        @foreach ($recipients as $r)
            <tr><td>{{ $r->user?->name }}</td><td>{{ $r->user?->role->label() }}</td>
                <td>{!! $r->read_at ? '<span class="badge-green">'.$r->read_at->format('d.m.Y H:i').'</span>' : '<span class="badge-gray">Yo\'q</span>' !!}</td>
                <td class="text-xs">{{ ['sent' => 'Yuborildi', 'skipped' => 'Obuna yo\'q', 'failed' => 'Xato', 'pending' => 'Kutilmoqda'][$r->push_status] ?? $r->push_status }}</td></tr>
        @endforeach
        </tbody>
    </table></div></div>
@endsection
