@extends('layouts.app')
@section('title', 'Obuna')

@section('content')
    <x-page-header icon="wallet" title="Obuna" subtitle="Filiallar obunasi va direktorlar yuborgan cheklar">
        <x-slot:actions><a href="{{ route('subscriptions.settings') }}" class="btn-secondary"><x-icon name="cog" class="h-4 w-4" /> Obuna sozlamalari</a></x-slot:actions>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-ink-100 px-5 py-3 font-semibold dark:border-ink-800">Cheklar @if ($pendingCount)<span class="badge-amber ml-2">{{ $pendingCount }} ta kutmoqda</span>@endif</div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Sana</th><th>Filial</th><th>Tarif / summa</th><th>Yuborgan</th><th>Chek</th><th>Holati</th></tr></thead>
                <tbody>
                @forelse ($requests as $r)
                    <tr>
                        <td>{{ $r->created_at->format('d.m.Y H:i') }}</td>
                        <td class="font-medium">{{ $r->branch?->name }}</td>
                        <td>{{ $r->planLabel() }} · {{ \App\Support\Format::money($r->amount) }}</td>
                        <td>{{ $r->user?->name }}@if ($r->note)<div class="text-xs text-ink-500">{{ $r->note }}</div>@endif</td>
                        <td><a href="{{ route('subscriptions.receipt', $r) }}" target="_blank" rel="noopener" class="text-brand-600 hover:underline">Rasmni ochish</a></td>
                        <td>
                            @if ($r->isPending())
                                <div class="flex flex-wrap items-center gap-2" x-data="{ rej: false }">
                                    <form method="POST" action="{{ route('subscriptions.approve', $r) }}" data-confirm="Obuna tasdiqlanadi va filial muddati uzaytiriladi." data-confirm-ok="Tasdiqlash">@csrf<x-once /><button class="btn-primary btn-sm">Tasdiqlash</button></form>
                                    <button type="button" class="btn-secondary btn-sm" @click="rej = !rej">Rad etish</button>
                                    <form x-show="rej" x-cloak method="POST" action="{{ route('subscriptions.reject', $r) }}" class="flex gap-2">@csrf
                                        <input name="reason" required maxlength="500" placeholder="Sabab" class="input py-1.5"><button class="btn-danger btn-sm">Yuborish</button>
                                    </form>
                                </div>
                            @elseif ($r->status === 'approved')
                                <span class="badge-green">Tasdiqlangan</span> <span class="text-xs text-ink-500">{{ $r->new_ends_on?->copy()->subDay()->format('d.m.Y') }} kunigacha</span>
                            @else
                                <span class="badge-red">Rad etilgan</span> <span class="text-xs text-ink-500">{{ $r->reject_reason }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-ink-500">Cheklar yo'q</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $requests->links() }}</div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-ink-100 px-5 py-3 font-semibold dark:border-ink-800">Filiallar obunasi</div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Filial</th><th>Holati</th><th>Qolgan</th><th>Qo'lda boshqarish <span class="font-normal text-ink-400">(sana — filial shu kundan bloklanadi)</span></th></tr></thead>
                <tbody>
                @foreach ($branches as $branch)
                    @php $left = $branch->subscriptionDaysLeft(); @endphp
                    <tr>
                        <td class="font-medium">{{ $branch->name }}</td>
                        <td>@include('subscriptions._status', ['branch' => $branch])</td>
                        <td>{{ $branch->subscription_exempt ? 'Cheksiz' : ($left === null ? '—' : ($left <= 0 ? 'Tugagan' : $left.' kun')) }}</td>
                        <td>
                            <form method="POST" action="{{ route('subscriptions.branch.update', $branch) }}" class="flex flex-wrap items-center gap-2">
                                @csrf @method('PUT')
                                <input type="date" name="subscription_ends_on" value="{{ $branch->subscription_ends_on?->format('Y-m-d') }}" class="input w-40 py-1.5">
                                <label class="flex items-center gap-1.5 text-xs"><input type="checkbox" name="subscription_exempt" value="1" class="checkbox" @checked($branch->subscription_exempt)> Obunasiz</label>
                                <button class="btn-secondary btn-sm">Saqlash</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
