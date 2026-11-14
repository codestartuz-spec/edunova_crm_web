@extends('layouts.app')
@section('title', 'Zaxira nusxalar')

@section('content')
    <x-page-header title="Zaxira nusxalar" subtitle="Har kuni 01:00 da baza zaxirasi — parollangan ZIP (AES-256), Telegramga faqat Admin ID larga yuboriladi">
        <x-slot:actions>
            <form method="POST" action="{{ route('backups.run') }}">@csrf<button class="btn-primary">Hozir zaxira olish</button></form>
        </x-slot:actions>
    </x-page-header>

    @unless ($telegramReady)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">Telegram sozlanmagan yoki Admin ID yo'q — zaxira faqat serverda saqlanadi. <a class="font-semibold underline" href="{{ route('telegram.index') }}">Telegram bot sozlamalari</a></div>
    @endunless

    <div class="card card-body mb-6 max-w-xl space-y-3">
        <h2 class="text-base font-semibold">ZIP paroli</h2>
        @if ($password)<div class="rounded-lg bg-ink-100 p-3 font-mono text-sm dark:bg-ink-800">{{ $password }}</div>@endif
        <form method="POST" action="{{ route('backups.password') }}" class="flex flex-wrap items-end gap-3">@csrf
            <x-input name="password" type="password" label="Hisob parolingiz" class="w-56" autocomplete="current-password" />
            <button class="btn-secondary">Parolni ko'rsatish</button></form>
        <p class="text-xs text-ink-500">Tiklash: <code>php artisan app:restore storage/app/backups/encrypted/&lt;fayl&gt;.zip</code> (veb orqali tiklash yo'q).</p>
    </div>

    <div class="card overflow-hidden"><div class="table-wrap"><table class="table">
        <thead><tr><th>Sana</th><th>Holat</th><th>Turi</th><th class="text-right">Hajmi</th><th>SHA-256</th><th>Davomiyligi</th><th>Telegram</th><th class="text-right">Amallar</th></tr></thead>
        <tbody>
        @forelse ($backups as $b)
            <tr>
                <td class="whitespace-nowrap">{{ $b->created_at->format('d.m.Y H:i') }}</td>
                <td><span class="{{ $b->status === 'ok' ? 'badge-green' : ($b->status === 'failed' ? 'badge-red' : 'badge-gray') }}">{{ ['ok' => 'Tayyor', 'failed' => 'Xato', 'running' => 'Bajarilmoqda'][$b->status] ?? $b->status }}</span>@if ($b->error)<div class="max-w-xs text-xs text-danger-600">{{ $b->error }}</div>@endif</td>
                <td>{{ $b->trigger === 'manual' ? "Qo'lda" : 'Jadval' }}</td>
                <td class="text-right">{{ $b->size ? round($b->size / 1048576, 2).' MB' : '—' }}</td>
                <td class="font-mono text-xs">{{ $b->sha256 ? substr($b->sha256, 0, 12).'…' : '—' }}</td>
                <td>{{ $b->duration_ms ? round($b->duration_ms / 1000, 1).' s' : '—' }}</td>
                <td class="text-xs">@forelse ($b->telegram ?? [] as $chat => $res)<div>{{ $chat }}: <span class="{{ $res === 'ok' ? 'text-emerald-600' : 'text-brand-600' }}">{{ $res === 'ok' ? 'yuborildi' : $res }}</span></div>@empty — @endforelse</td>
                <td class="whitespace-nowrap text-right">
                    @if ($b->status === 'ok' && $b->file)
                        <form method="POST" action="{{ route('backups.resend', $b) }}" class="inline">@csrf<button class="text-xs font-semibold text-brand-600">Qayta yuborish</button></form>
                        <form method="POST" action="{{ route('backups.download', $b) }}" class="inline" onsubmit="this.password.value = prompt('Yuklab olish uchun hisob parolingizni kiriting') || ''; return this.password.value !== ''">@csrf<input type="hidden" name="password"><button class="ml-2 text-xs font-semibold text-brand-600">Yuklab olish</button></form>
                    @endif
                    <form method="POST" action="{{ route('backups.destroy', $b) }}" class="ml-2 inline" data-confirm="Zaxira o'chirilsinmi?">@csrf @method('DELETE')<button class="text-xs font-semibold text-ink-500">O'chirish</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="py-10 text-center text-ink-500">Hali zaxira olinmagan.</td></tr>
        @endforelse
        </tbody>
    </table></div></div>
    <div class="mt-4">{{ $backups->links() }}</div>
@endsection
