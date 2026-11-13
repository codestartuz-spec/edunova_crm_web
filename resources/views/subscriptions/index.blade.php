@extends('layouts.app')
@section('title', 'Obuna')

@section('content')
    <x-page-header icon="wallet" title="Obuna" subtitle="Filiallaringiz obunasi: holati, tugash sanasi va qolgan muddat" />

    @unless ($configured)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">Obuna narxi va karta raqami hali belgilanmagan. Administrator bilan bog'laning.</div>
    @endunless

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Filial</th><th>Holati</th><th>Tugash sanasi</th><th>Qolgan muddat</th><th class="text-right">Amal</th></tr></thead>
                <tbody>
                @forelse ($branches as $branch)
                    @php $left = $branch->subscriptionDaysLeft(); @endphp
                    <tr>
                        <td class="font-medium text-ink-900 dark:text-white">{{ $branch->name }}</td>
                        <td>@include('subscriptions._status', ['branch' => $branch])</td>
                        <td>{{ $branch->subscription_exempt ? '—' : $branch->subscription_ends_on?->format('d.m.Y') }}</td>
                        <td>
                            @if ($branch->subscription_exempt) Cheksiz
                            @elseif ($left === null) —
                            @elseif ($left <= 0) <span class="text-brand-600">Muddati tugagan</span>
                            @else {{ $left }} kun
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($branch->subscription_exempt)
                                <span class="text-xs text-ink-400">Kerak emas</span>
                            @elseif ($pending->has($branch->id))
                                <span class="badge-amber">Chek tekshiruvda</span>
                            @elseif ($branch->subscriptionExpired())
                                <a href="{{ route('subscriptions.buy', $branch) }}" class="btn-primary btn-sm">Obuna sotib olish</a>
                            @else
                                <a href="{{ route('subscriptions.buy', $branch) }}" class="btn-secondary btn-sm">Obunani uzaytirish</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-ink-500">Filiallar yo'q</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-ink-100 px-5 py-3 font-semibold text-ink-900 dark:border-ink-800 dark:text-white">Yuborilgan cheklar</div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Sana</th><th>Filial</th><th>Tarif</th><th>Summa</th><th>Holati</th></tr></thead>
                <tbody>
                @forelse ($history as $r)
                    <tr>
                        <td>{{ $r->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $r->branch?->name }}</td>
                        <td>{{ $r->planLabel() }}</td>
                        <td>{{ \App\Support\Format::money($r->amount) }}</td>
                        <td>
                            @if ($r->status === 'approved')<span class="badge-green">Tasdiqlangan</span> <span class="text-xs text-ink-500">{{ $r->new_ends_on?->format('d.m.Y') }} gacha</span>
                            @elseif ($r->status === 'rejected')<span class="badge-red">Rad etilgan</span> <span class="text-xs text-ink-500">{{ $r->reject_reason }}</span>
                            @else<span class="badge-amber">Tekshiruvda</span>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-ink-500">Hozircha chek yuborilmagan</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
