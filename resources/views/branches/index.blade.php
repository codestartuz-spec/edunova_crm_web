@extends('layouts.app')
@section('title', 'Filiallar')

@section('content')
    @php
        $svg = fn (string $p, string $cls = 'h-4 w-4') => '<svg class="'.$cls.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$p.'</svg>';
        $ic = [
            'phone' => '<path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2A16 16 0 0 1 4.5 5.5a2 2 0 0 1 2-2z"/>',
            'pin' => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
            'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8"/><path d="M18 14.3c2.2.6 3.5 2.4 3.5 5.7"/>',
            'chip' => '<rect x="7" y="2.5" width="10" height="19" rx="2"/><path d="M11 18.5h2"/>',
            'wallet' => '<path d="M4 7a2 2 0 0 1 2-2h11v4"/><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 14h2"/>',
            'arrow' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
            'archive' => '<rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V9"/><path d="M10 13h4"/>',
            'plus' => '<path d="M12 5v14M5 12h14"/>',
        ];
    @endphp

    <style>
        @keyframes br-in { from { opacity: 0; transform: translateY(14px) scale(.98); } to { opacity: 1; transform: none; } }
        @keyframes br-fill { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        @keyframes br-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
        .br-card { --bc: #dc2626; animation: br-in .55s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 70ms); transition: transform .25s cubic-bezier(.2,.8,.2,1), box-shadow .25s; }
        .br-card:hover { transform: translateY(-5px); box-shadow: 0 18px 40px -18px color-mix(in srgb, var(--bc) 55%, transparent), 0 6px 14px -8px rgba(15,23,42,.18); }
        .br-card:active { transform: translateY(-2px) scale(.995); }
        .br-bar { position: absolute; inset: 0 0 auto 0; height: 4px; background: linear-gradient(90deg, var(--bc), color-mix(in srgb, var(--bc) 40%, white)); transform-origin: left; animation: br-fill .8s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 70ms + 150ms); }
        .br-glow { position: absolute; right: -60px; top: -60px; height: 170px; width: 170px; border-radius: 9999px; background: radial-gradient(closest-side, color-mix(in srgb, var(--bc) 22%, transparent), transparent); opacity: 0; transition: opacity .3s; pointer-events: none; }
        .br-card:hover .br-glow { opacity: 1; }
        .br-tile { background: color-mix(in srgb, var(--bc) 14%, transparent); color: var(--bc); transition: transform .3s cubic-bezier(.2,.8,.2,1); }
        .br-card:hover .br-tile { transform: rotate(-6deg) scale(1.08); }
        .br-go { opacity: 0; transform: translateX(-6px); transition: opacity .25s, transform .25s; color: var(--bc); }
        .br-card:hover .br-go { opacity: 1; transform: none; }
        .br-meter > i { display: block; height: 100%; border-radius: 9999px; background: var(--bc); transform-origin: left; animation: br-fill 1s cubic-bezier(.2,.8,.2,1) both; animation-delay: .3s; }
        .br-closed { filter: grayscale(.6); opacity: .8; }
        @media (prefers-reduced-motion: reduce) { .br-card, .br-bar, .br-meter > i { animation: none !important; } .br-card:hover { transform: none; } }
    </style>

    <x-page-header title="Filiallar" subtitle="Filialni ochish uchun kartochkasini bosing">
        <x-slot:actions>
            <a href="{{ route('branches.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Yangi filial</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($branches as $branch)
            @php
                $limit = (int) $branch->sms_modem_daily_limit;
                $used = (int) ($today[$branch->id] ?? 0);
                $pct = $limit > 0 ? min(100, (int) round($used / $limit * 100)) : 0;
                $last = $lastPayments[$branch->id] ?? null;
            @endphp
            <a href="{{ route('branches.show', $branch) }}" class="br-card group card relative block overflow-hidden p-5 {{ $branch->isActive() ? '' : 'br-closed' }}"
               style="--bc: {{ $branch->effectiveBrandColor() }}; --i: {{ $loop->index }}">
                <span class="br-bar"></span>
                <span class="br-glow"></span>

                <div class="relative flex items-start gap-3">
                    <span class="br-tile flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-xl font-bold">{{ mb_strtoupper(mb_substr($branch->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-lg font-semibold text-ink-900 dark:text-white">{{ $branch->name }}</h3>
                        <p class="truncate text-xs text-ink-400">{{ $branch->code }}</p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-1">
                        <span class="{{ $branch->isActive() ? 'badge-green' : 'badge-gray' }}">
                            @unless ($branch->isActive()){!! $svg($ic['archive'], 'h-3.5 w-3.5') !!}@endunless
                            {{ $branch->status->label() }}
                        </span>
                        @if ($branch->subscriptionExpired())<span class="badge-red">Obuna tugagan</span>@endif
                    </div>
                </div>

                <dl class="relative mt-4 space-y-1.5 text-sm text-ink-600 dark:text-ink-300">
                    @if ($branch->phone)<div class="flex items-center gap-2">{!! $svg($ic['phone'], 'h-4 w-4 text-ink-400') !!}{{ $branch->phone }}</div>@endif
                    @if ($branch->address)<div class="flex items-center gap-2">{!! $svg($ic['pin'], 'h-4 w-4 shrink-0 text-ink-400') !!}<span class="truncate">{{ $branch->address }}</span></div>@endif
                    <div class="flex items-center gap-2">{!! $svg($ic['users'], 'h-4 w-4 text-ink-400') !!}{{ $branch->staff_count }} hodim · {{ $branch->students_count }} o'quvchi</div>
                    @unless ($branch->isActive())
                        <div class="text-xs text-ink-400">Yopilgan: {{ $branch->closed_at?->format('d.m.Y') }}@if ($branch->closed_reason) — {{ $branch->closed_reason }}@endif</div>
                    @endunless
                </dl>

                <div class="relative mt-4 space-y-3 border-t border-ink-100 pt-4 dark:border-ink-800">
                    <div>
                        <div class="flex items-center justify-between text-xs text-ink-500">
                            <span class="inline-flex items-center gap-1.5">{!! $svg($ic['chip'], 'h-3.5 w-3.5') !!} Modem SMS (bugun)</span>
                            <span class="font-medium text-ink-700 dark:text-ink-200">@if ($limit > 0){{ $used }} / {{ $limit }}@else Eskiz orqali @endif</span>
                        </div>
                        @if ($limit > 0)
                            <div class="br-meter mt-1.5 h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-700"><i style="width: {{ $pct }}%"></i></div>
                        @endif
                    </div>
                    @php $subLeft = $branch->subscriptionDaysLeft(); @endphp
                    <div class="flex items-center justify-between text-xs text-ink-500">
                        <span class="inline-flex items-center gap-1.5">{!! $svg($ic['wallet'], 'h-3.5 w-3.5') !!} Obuna</span>
                        <span class="font-medium {{ $branch->subscriptionExpired() ? 'text-brand-600' : 'text-ink-700 dark:text-ink-200' }}">
                            @if ($branch->subscription_exempt) Obunasiz (doim faol)
                            @elseif ($branch->subscriptionExpired()) Tugagan · faqat ko'rish ({{ $branch->subscriptionLastDay()?->format('d.m.Y') }} dan keyin)
                            @elseif ($subLeft !== null) {{ $branch->subscriptionLastDay()?->format('d.m.Y') }} kunigacha · {{ $subLeft }} kun
                            @else — @endif
                        </span>
                    </div>
                    @if ($isSadmin)
                        <div class="flex items-center justify-between text-xs text-ink-500">
                            <span class="inline-flex items-center gap-1.5">{!! $svg($ic['wallet'], 'h-3.5 w-3.5') !!} Oxirgi to'lov</span>
                            <span class="font-medium text-ink-700 dark:text-ink-200">@if ($last){{ $last->created_at->timezone('Asia/Tashkent')->format('d.m.Y') }} · {{ \App\Support\Format::money($last->amount) }}@else Hali yo'q @endif</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-end text-sm font-medium"><span class="br-go inline-flex items-center gap-1">Ochish {!! $svg($ic['arrow'], 'h-4 w-4') !!}</span></div>
                </div>
            </a>
        @empty
            <div class="card card-body col-span-full py-12 text-center text-ink-500">Hali filial yo'q. Birinchi filialni oching.</div>
        @endforelse
    </div>
@endsection
