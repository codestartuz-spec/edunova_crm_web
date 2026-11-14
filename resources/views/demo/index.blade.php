@extends('layouts.app')
@section('title', "Demo so'rovlar")

@section('content')
    <x-page-header icon="megaphone" title="Demo so'rovlar" subtitle="Sotuv sahifasidan kelgan so'rovlar. Yangisi kelganda Telegram orqali sAdminlarga xabar boradi">
        <x-slot:actions>
            <a href="{{ \App\Support\Seo::landingUrl('uz') }}" target="_blank" rel="noopener" class="btn-secondary btn-sm"><x-icon name="eye" colored class="h-4 w-4" /> Sotuv sahifasi</a>
            <a href="{{ route('settings.index') }}" class="btn-secondary btn-sm">← Sozlamalar</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-5 flex flex-wrap gap-2 text-sm font-semibold">
        <a href="{{ route('demo.index') }}" class="rounded-lg px-3 py-1.5 {{ ! $status ? 'bg-brand-600 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-100 hover:bg-ink-100 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-800' }}">Hammasi <span class="opacity-70">{{ $counts->sum() }}</span></a>
        @foreach (\App\Models\DemoRequest::STATUSES as $k => $label)
            <a href="{{ route('demo.index', ['status' => $k]) }}" class="rounded-lg px-3 py-1.5 {{ $status === $k ? 'bg-brand-600 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-100 hover:bg-ink-100 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-800' }}">{{ $label }} <span class="opacity-70">{{ $counts[$k] ?? 0 }}</span></a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($requests as $r)
            <div class="card card-body {{ $r->status === 'new' ? 'ring-2 ring-brand-300 dark:ring-brand-500/50' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-base font-bold text-ink-900 dark:text-white">{{ $r->name }}</span>
                            <span class="badge {{ ['new' => 'badge-brand', 'contacted' => 'badge-amber', 'done' => 'badge-green', 'spam' => 'badge-gray'][$r->status] }}">{{ \App\Models\DemoRequest::STATUSES[$r->status] }}</span>
                            <span class="badge badge-gray">{{ strtoupper($r->locale) }}</span>
                            @unless ($r->telegram_sent)<span class="badge badge-amber" title="Telegram xabari yetib bormagan">Telegram ✗</span>@endunless
                        </div>
                        <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-ink-600 dark:text-ink-300">
                            <a href="tel:+{{ $r->phone }}" class="inline-flex items-center gap-1.5 font-semibold text-brand-600 hover:underline"><x-icon name="phone" colored class="h-4 w-4" /> +{{ $r->phone }}</a>
                            @if ($r->center)<span class="inline-flex items-center gap-1.5"><x-icon name="building" colored class="h-4 w-4" /> {{ $r->center }}</span>@endif
                            @if ($r->size)<span class="inline-flex items-center gap-1.5"><x-icon name="users" colored class="h-4 w-4" /> {{ \App\Models\DemoRequest::SIZES[$r->size] ?? $r->size }}</span>@endif
                            <span class="text-ink-400">{{ $r->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        @if ($r->message)<p class="mt-2 rounded-xl bg-ink-50 px-3 py-2 text-sm text-ink-700 dark:bg-ink-800/60 dark:text-ink-200">{{ $r->message }}</p>@endif
                    </div>
                    <form method="POST" action="{{ route('demo.destroy', $r) }}" data-confirm="So'rov o'chirilsinmi?" data-confirm-tone="danger">@csrf @method('DELETE')<button class="btn-ghost btn-sm text-danger-600" aria-label="O'chirish"><x-icon name="trash" class="h-4 w-4" /></button></form>
                </div>
                <form method="POST" action="{{ route('demo.update', $r) }}" class="mt-3 grid gap-2 sm:grid-cols-[10rem_1fr_auto]">
                    @csrf @method('PUT')
                    <select name="status" class="input">@foreach (\App\Models\DemoRequest::STATUSES as $k => $label)<option value="{{ $k }}" @selected($r->status === $k)>{{ $label }}</option>@endforeach</select>
                    <input name="note" value="{{ $r->note }}" maxlength="1000" class="input" placeholder="Izoh (masalan: ertaga soat 15:00 da qo'ng'iroq)">
                    <button class="btn-primary btn-sm">Saqlash</button>
                </form>
            </div>
        @empty
            <x-empty icon="megaphone" title="Hozircha so'rov yo'q" text="Sotuv sahifasidagi «Demo so'rash» formasi orqali kelgan so'rovlar shu yerda ko'rinadi." />
        @endforelse
    </div>
    <div class="mt-4">{{ $requests->links() }}</div>
@endsection
