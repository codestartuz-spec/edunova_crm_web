@extends('layouts.app')
@section('title', 'Telegram bot')

@section('content')
    <x-page-header title="Telegram bot" subtitle="Parolni tiklash, zaxira va /status buyruqlari">
        <x-slot:actions><a href="{{ route('backups.index') }}" class="btn-secondary">Zaxira nusxalar</a></x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card card-body space-y-4">
            <h2 class="text-base font-semibold">Bot</h2>
            @if ($configured && $bot)
                <div class="text-sm">Ulangan: <b>{{ '@'.($bot['username'] ?? '') }}</b> <span class="badge-green">faol</span></div>
            @elseif ($configured)
                <div class="text-sm text-brand-600">Token saqlangan, lekin Telegramga ulanib bo'lmadi.</div>
            @endif
            <form method="POST" action="{{ route('telegram.token') }}" class="space-y-3">
                @csrf
                <x-input name="token" type="password" label="Bot tokeni (@BotFather)" autocomplete="off" hint="Shifrlangan holda saqlanadi" />
                <button class="btn-primary">Tokenni saqlash</button>
            </form>

            @if ($configured)
                <div class="border-t border-ink-100 pt-4 dark:border-ink-800">
                    <div class="text-sm">Webhook: <b>{{ $hook['url'] ?? "o'rnatilmagan" }}</b></div>
                    @if (! empty($hook['last_error_message']))<div class="mt-1 text-xs text-brand-600">Oxirgi xato: {{ $hook['last_error_message'] }}</div>@endif
                    <div class="mt-1 text-xs text-ink-500">Kerakli manzil: {{ $webhookUrl }}</div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('telegram.webhook.set') }}">@csrf<button class="btn-secondary">Webhook o'rnatish</button></form>
                        <form method="POST" action="{{ route('telegram.webhook.delete') }}" data-confirm="Webhook o'chirilsa, bot buyruqlari ishlamaydi.">@csrf @method('DELETE')<button class="btn-secondary">Webhook o'chirish</button></form>
                        <form method="POST" action="{{ route('telegram.test') }}">@csrf<button class="btn-secondary">Sinov xabari</button></form>
                    </div>
                </div>
            @endif
        </div>

        <div class="card card-body space-y-4">
            <h2 class="text-base font-semibold">Admin ID lar</h2>
            <p class="text-xs text-ink-500">Faqat shu ID lar /status va /backup buyruqlarini ishlata oladi; zaxira va sAdmin parol kodi ham faqat ularga yuboriladi. O'z ID ngizni botdagi /id buyrug'i bilan bilib oling.</p>
            @forelse ($admins as $a)
                <div class="flex items-center justify-between text-sm"><span><b>{{ $a['id'] }}</b> {{ $a['label'] }}</span>
                    <form method="POST" action="{{ route('telegram.admins.remove', $a['id']) }}" data-confirm="Admin ID o'chirilsinmi?">@csrf @method('DELETE')<button class="text-xs font-semibold text-brand-600">O'chirish</button></form></div>
            @empty
                <p class="text-sm text-ink-500">Hali Admin ID yo'q.</p>
            @endforelse
            <form method="POST" action="{{ route('telegram.admins.add') }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <x-input name="chat_id" label="Telegram ID" class="w-44" required />
                <x-input name="label" label="Izoh" class="w-44" />
                <button class="btn-secondary">Qo'shish</button>
            </form>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-ink-100 p-4 text-base font-semibold dark:border-ink-800">Telegramga ulangan foydalanuvchilar</div>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>F.I.O</th><th>Lavozim</th><th>Telegram</th><th>Ulangan</th><th></th></tr></thead>
            <tbody>
            @forelse ($linked as $u)
                <tr><td>{{ $u->name }}</td><td>{{ $u->role->label() }}</td><td>{{ $u->telegram_username ? '@'.$u->telegram_username : $u->telegram_chat_id }}</td><td>{{ $u->telegram_linked_at?->format('d.m.Y H:i') }}</td>
                    <td class="text-right"><form method="POST" action="{{ route('telegram.unlink', $u->id) }}" data-confirm="Telegram uzilsinmi?">@csrf @method('DELETE')<button class="text-xs font-semibold text-brand-600">Uzish</button></form></td></tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-ink-500">Hali hech kim ulanmagan.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="p-3">{{ $linked->links() }}</div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-ink-100 p-4 text-base font-semibold dark:border-ink-800">Parol tiklash jurnali</div>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Vaqt</th><th>Foydalanuvchi</th><th>Kiritilgan raqam</th><th>Kanal</th><th>Hodisa</th><th>IP</th><th>Izoh</th></tr></thead>
            <tbody>
            @php $labels = ['requested' => "So'rov", 'code_sent' => 'Kod yuborildi', 'failed_send' => 'Yuborilmadi', 'code_wrong' => "Noto'g'ri kod", 'reset_done' => 'Parol tiklandi', 'blocked' => 'Cheklov']; @endphp
            @forelse ($logs as $l)
                <tr>
                    <td class="whitespace-nowrap">{{ $l->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $l->user?->name ?? '—' }}</td><td>{{ $l->phone_entered }}</td><td>{{ $l->channel ?? '—' }}</td>
                    <td><span class="{{ in_array($l->event, ['code_wrong', 'failed_send', 'blocked']) ? 'badge-red' : ($l->event === 'reset_done' ? 'badge-green' : 'badge-gray') }}">{{ $labels[$l->event] ?? $l->event }}</span></td>
                    <td class="text-xs">{{ $l->ip }}</td><td class="text-xs text-ink-500">{{ $l->note }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-8 text-center text-ink-500">Hali urinish bo'lmagan.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="p-3">{{ $logs->links() }}</div>
    </div>
@endsection
