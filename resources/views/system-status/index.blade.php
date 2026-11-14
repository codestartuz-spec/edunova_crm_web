@extends('layouts.app')
@section('title', 'Tizim holati')

@section('content')
    @php
        $svg = fn (string $p, string $cls = 'h-5 w-5') => '<svg class="'.$cls.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$p.'</svg>';
        $paths = [
            'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'list' => '<path d="M8 6h12M8 12h12M8 18h12"/><path d="M4 6h.01M4 12h.01M4 18h.01"/>',
            'alertc' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>',
            'database' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
            'disk' => '<rect x="3" y="13" width="18" height="7" rx="2"/><path d="m3 13 3-8h12l3 8"/><path d="M7 16.5h.01M11 16.5h.01"/>',
            'wallet' => '<path d="M4 7a2 2 0 0 1 2-2h11v4"/><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 14h2"/>',
            'bug' => '<path d="M9 9a3 3 0 0 1 6 0v6a3 3 0 0 1-6 0V9z"/><path d="M12 12v6M5 8l3 2M19 8l-3 2M4 14h4M16 14h4M5 20l3-2M19 20l-3-2M9 6 8 4M15 6l1-2"/>',
            'code' => '<path d="m8 8-5 4 5 4M16 8l5 4-5 4M14 5l-4 14"/>',
            'folder' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/><path d="m9.5 13.5 2 2 3.5-3.5"/>',
            'eye' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
            'archive' => '<rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V9"/><path d="M10 13h4"/>',
            'phone' => '<rect x="7" y="2.5" width="10" height="19" rx="2"/><path d="M11 18.5h2"/>',
            'send' => '<path d="M21 3 10 14"/><path d="m21 3-7 18-4-7-7-4 18-7z"/>',
            'filewarn' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M12 11v4M12 18h.01"/>',
            'lock' => '<rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
            'key' => '<circle cx="8" cy="15" r="4"/><path d="m11 12 8-8M16 7l3 3"/>',
            'shield' => '<path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.3 7.5 9.5 4.4-1.2 7.5-4.9 7.5-9.5V6L12 3z"/><path d="m9 12 2 2 4-4"/>',
            'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>',
            'message' => '<path d="M4 5h16v11H9l-5 4V5z"/><path d="M9 10h6"/>',
            'activity' => '<path d="M3 12h4l3-8 4 16 3-8h4"/>',
            'server' => '<rect x="3" y="4" width="18" height="7" rx="2"/><rect x="3" y="13" width="18" height="7" rx="2"/><path d="M7 7.5h.01M7 16.5h.01"/>',
            'refresh' => '<path d="M20 12a8 8 0 1 1-2.5-5.8"/><path d="M20 4v5h-5"/>',
            'plug' => '<path d="M9 3v5M15 3v5"/><path d="M6 8h12v3a6 6 0 0 1-12 0V8z"/><path d="M12 17v4"/>',
            'cogs' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
            'ok' => '<circle cx="12" cy="12" r="9"/><path d="m8 12.5 3 3 5-6"/>',
            'warn' => '<path d="M12 3.5 2.5 20h19L12 3.5z"/><path d="M12 10v4.5M12 17.5h.01"/>',
            'fail' => '<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/>',
        ];
        $keyIcon = ['cron' => 'clock', 'queue' => 'list', 'jobs' => 'alertc', 'migrations' => 'database', 'disk' => 'disk', 'wallets' => 'wallet', 'debug' => 'bug', 'php' => 'code', 'database' => 'database', 'storage' => 'folder', 'watchdog' => 'eye', 'backup' => 'archive', 'sms_modem' => 'phone', 'eskiz' => 'send', 'telegram' => 'send', 'log_errors' => 'filewarn', 'https' => 'lock', 'app_key' => 'key', 'public_env' => 'shield', 'single_sadmin' => 'user', 'sms_stuck' => 'message'];
        $groupIcon = ['infra' => 'server', 'queue' => 'cogs', 'backup' => 'archive', 'services' => 'plug', 'logs' => 'filewarn', 'security' => 'shield', 'data' => 'database'];

        $fail = collect($checks)->where('status', 'fail')->count();
        $warn = collect($checks)->where('status', 'warn')->count();
        $total = max(1, count($checks));
        $okCount = count($checks) - $fail - $warn;
        $overall = $fail ? 'fail' : ($warn ? 'warn' : 'ok');
        $score = (int) round($okCount / $total * 100);
        $tone = ['ok' => 'ss-ok', 'warn' => 'ss-warn', 'fail' => 'ss-fail', 'none' => 'ss-none'];
        $name = ['ok' => 'Yaxshi', 'warn' => 'Diqqat', 'fail' => 'Muammo'];
        $title = ['ok' => 'Hammasi joyida', 'warn' => "Ba'zi narsalarga e'tibor bering", 'fail' => 'Zudlik bilan tuzatish kerak'][$overall];
        $fmtDur = function ($a, $b) {
            $m = (int) $a->diffInMinutes($b ?? now(), true);
            return $m >= 1440 ? intdiv($m, 1440).' kun '.intdiv($m % 1440, 60).' soat' : ($m >= 60 ? intdiv($m, 60).' soat '.($m % 60).' daq' : max(1, $m).' daq');
        };
        $tz = 'Asia/Tashkent';
    @endphp

    <style>
        .ss-ok { --c: #10b981; --bg: rgba(16,185,129,.12); --tx: #047857; }
        .ss-warn { --c: #f59e0b; --bg: rgba(245,158,11,.14); --tx: #b45309; }
        .ss-fail { --c: #f43f5e; --bg: rgba(244,63,94,.12); --tx: #be123c; }
        .ss-none { --c: #cbd5e1; --bg: rgba(148,163,184,.14); --tx: #64748b; }
        .dark .ss-ok { --tx: #6ee7b7; } .dark .ss-warn { --tx: #fcd34d; } .dark .ss-fail { --tx: #fda4af; } .dark .ss-none { --c: #475569; --tx: #94a3b8; }
        .ss-pill { background: var(--bg); color: var(--tx); }
        .ss-tile { background: var(--bg); color: var(--c); }
        @keyframes ss-up { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
        @keyframes ss-ring { from { stroke-dashoffset: var(--len); } }
        @keyframes ss-ping { 0% { transform: scale(1); opacity: .55; } 80%, 100% { transform: scale(2.1); opacity: 0; } }
        @keyframes ss-grow { from { transform: scaleY(0); } to { transform: scaleY(1); } }
        @keyframes ss-shine { 0% { transform: translateX(-120%); } 100% { transform: translateX(380%); } }
        @keyframes ss-breathe { 0%, 100% { box-shadow: 0 0 0 0 var(--bg); } 50% { box-shadow: 0 0 0 8px transparent; } }
        .ss-in { animation: ss-up .5s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 45ms); }
        .ss-ringfg { stroke-dasharray: var(--len); animation: ss-ring 1.2s cubic-bezier(.2,.8,.2,1) both; }
        .ss-pingdot { position: relative; display: inline-flex; }
        .ss-pingdot::after { content: ''; position: absolute; inset: 0; border-radius: 9999px; background: var(--c); opacity: .5; animation: ss-ping 1.8s ease-out infinite; z-index: -1; }
        .ss-bar { transform-origin: bottom; animation: ss-grow .55s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 12ms); transition: filter .15s; }
        .ss-barwrap:hover .ss-bar { filter: brightness(1.15); }
        .ss-hero { position: relative; overflow: hidden; }
        .ss-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(900px 220px at 0% 0%, var(--bg), transparent 70%); pointer-events: none; }
        .ss-hero::after { content: ''; position: absolute; top: 0; bottom: 0; width: 18%; background: linear-gradient(100deg, transparent, rgba(255,255,255,.35), transparent); animation: ss-shine 6s ease-in-out infinite; pointer-events: none; }
        .dark .ss-hero::after { background: linear-gradient(100deg, transparent, rgba(255,255,255,.05), transparent); }
        .ss-row { transition: background-color .15s; }
        .ss-row:hover { background: rgba(148,163,184,.08); }
        .ss-iconwarn { animation: ss-breathe 2.4s ease-in-out infinite; }
        #status-wrap[data-filter="issues"] .chk-ok { display: none; }
        #status-wrap[data-filter="issues"] .grp-ok { display: none; }
        .ss-tip { opacity: 0; transform: translateY(4px); pointer-events: none; transition: opacity .15s, transform .15s; }
        .ss-barwrap:hover .ss-tip, .ss-barwrap:focus .ss-tip { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) { .ss-in, .ss-ringfg, .ss-bar, .ss-hero::after, .ss-pingdot::after, .ss-iconwarn { animation: none !important; } }
    </style>

    <x-page-header title="Tizim holati" subtitle="Server, navbat, zaxira, xizmatlar, xavfsizlik va ma'lumotlar yaxlitligi" />

    <div id="status-wrap" data-filter="all" data-url="{{ route('system-status.index', ['range' => $range]) }}">
        <div id="status-root">

            {{-- HERO --}}
            @php $len = round(2 * M_PI * 52, 2); @endphp
            <div class="card ss-hero {{ $tone[$overall] }} mb-4 p-5">
                <div class="relative z-10 flex flex-wrap items-center gap-5">
                    <div class="relative h-28 w-28 shrink-0">
                        <svg viewBox="0 0 120 120" class="h-28 w-28 -rotate-90">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-opacity=".12" stroke-width="9" class="text-ink-400" />
                            <circle cx="60" cy="60" r="52" fill="none" stroke="var(--c)" stroke-width="9" stroke-linecap="round" class="ss-ringfg"
                                    style="--len: {{ $len }}; stroke-dashoffset: {{ round($len * (1 - $score / 100), 2) }};" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <div class="text-2xl font-bold leading-none text-ink-900 dark:text-white">{{ $score }}%</div>
                            <div class="mt-1 text-[11px] uppercase tracking-wide text-ink-500">sog'lom</div>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3">
                            <span class="ss-tile ss-pingdot flex h-10 w-10 items-center justify-center rounded-xl">{!! $svg($paths[$overall], 'h-6 w-6') !!}</span>
                            <div>
                                <div class="text-xl font-semibold text-ink-900 dark:text-white">{{ $title }}</div>
                                <div class="text-sm text-ink-500 dark:text-ink-300">
                                    {{ $fail }} ta muammo · {{ $warn }} ta ogohlantirish · {{ $okCount }} ta yaxshi
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-ink-500">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-100 px-2.5 py-1 dark:bg-ink-800">{!! $svg($paths['clock'], 'h-3.5 w-3.5') !!}
                                Oxirgi yozuv: {{ $lastCheck ? \Illuminate\Support\Carbon::parse($lastCheck)->timezone($tz)->format('d.m H:i') : '—' }}</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-100 px-2.5 py-1 dark:bg-ink-800">{!! $svg($paths['refresh'], 'h-3.5 w-3.5') !!}
                                <span id="ss-countdown">30</span> soniyada yangilanadi</span>
                        </div>
                    </div>

                    @if (auth()->user()->isSuperAdmin())
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('system-status.run') }}">@csrf
                                <button class="btn-secondary btn-sm inline-flex items-center gap-1.5">{!! $svg($paths['refresh'], 'h-4 w-4') !!} Hozir tekshirish</button></form>
                            <form method="POST" action="{{ route('system-status.backup') }}" data-confirm="Zaxira nusxa hozir olinsinmi? Bir necha daqiqa vaqt olishi mumkin.">@csrf
                                <button class="btn-secondary btn-sm inline-flex items-center gap-1.5">{!! $svg($paths['archive'], 'h-4 w-4') !!} Hozir zaxira olish</button></form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- UPTIME --}}
            <div class="mb-4 grid gap-3 sm:grid-cols-3">
                @foreach ($ranges as $k => $cfg)
                    @php $u = $uptime[$k]; $t = $u['up'] === null ? 'none' : ($u['up'] >= 99 ? 'ok' : ($u['up'] >= 95 ? 'warn' : 'fail')); @endphp
                    <div class="card {{ $tone[$t] }} ss-in p-4" style="--i: {{ $loop->index }}">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-medium uppercase tracking-wide text-ink-500">Ishlash vaqti · {{ $cfg['label'] }}</div>
                            <span class="ss-tile flex h-8 w-8 items-center justify-center rounded-lg">{!! $svg($paths['activity'], 'h-4 w-4') !!}</span>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-ink-900 dark:text-white">{{ $u['up'] === null ? '—' : $u['up'].'%' }}</div>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-700">
                            <div class="h-full rounded-full" style="width: {{ $u['up'] ?? 0 }}%; background: var(--c);"></div>
                        </div>
                        <div class="mt-2 text-xs text-ink-500">
                            @if ($u['total']) Mukammal: {{ $u['perfect'] }}% · {{ $u['total'] }} ta tekshiruv @else Hali ma'lumot yo'q @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FILTER --}}
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div class="text-sm font-semibold text-ink-900 dark:text-white">Tekshiruvlar</div>
                <div class="inline-flex rounded-xl bg-ink-100 p-1 text-sm dark:bg-ink-800" id="ss-filter">
                    <button type="button" data-f="all" class="rounded-lg px-3 py-1 font-medium">Hammasi</button>
                    <button type="button" data-f="issues" class="rounded-lg px-3 py-1 font-medium">Faqat e'tibor talab qiladiganlar</button>
                </div>
            </div>

            {{-- CHECKS --}}
            <div class="grid gap-4 lg:grid-cols-2">
                @php $n = 0; @endphp
                @foreach ($groupLabels as $key => $label)
                    @continue(! isset($groups[$key]))
                    @php
                        $gw = $groups[$key]->where('status', 'warn')->count();
                        $gf = $groups[$key]->where('status', 'fail')->count();
                        $gs = $gf ? 'fail' : ($gw ? 'warn' : 'ok');
                    @endphp
                    <div class="card ss-in overflow-hidden {{ $gs === 'ok' ? 'grp-ok' : '' }}" style="--i: {{ $loop->index }}">
                        <div class="flex items-center gap-3 border-b border-ink-100 px-4 py-3 dark:border-ink-700">
                            <span class="{{ $tone[$gs] }} ss-tile flex h-8 w-8 items-center justify-center rounded-lg">{!! $svg($paths[$groupIcon[$key] ?? 'server'], 'h-4 w-4') !!}</span>
                            <div class="flex-1 text-sm font-semibold text-ink-900 dark:text-white">{{ $label }}</div>
                            @if ($gf)<span class="ss-fail ss-pill rounded-full px-2 py-0.5 text-xs font-semibold">{{ $gf }} muammo</span>@endif
                            @if ($gw)<span class="ss-warn ss-pill rounded-full px-2 py-0.5 text-xs font-semibold">{{ $gw }} diqqat</span>@endif
                            @if (! $gf && ! $gw)<span class="ss-ok ss-pill rounded-full px-2 py-0.5 text-xs font-semibold">{{ $groups[$key]->count() }} ta yaxshi</span>@endif
                        </div>
                        <ul class="divide-y divide-ink-100 dark:divide-ink-700">
                            @foreach ($groups[$key] as $c)
                                @php $n++; @endphp
                                <li class="ss-row ss-in {{ $tone[$c['status']] }} {{ $c['status'] === 'ok' ? 'chk-ok' : '' }} flex items-start gap-3 px-4 py-3" style="--i: {{ $n }}">
                                    <span class="ss-tile {{ $c['status'] !== 'ok' ? 'ss-iconwarn' : '' }} mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl">{!! $svg($paths[$keyIcon[$c['key']] ?? 'activity'], 'h-[18px] w-[18px]') !!}</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-medium text-ink-900 dark:text-white">{{ $c['label'] }}</div>
                                        <div class="text-sm text-ink-600 dark:text-ink-300">{{ $c['detail'] }}</div>
                                    </div>
                                    <span class="ss-pill inline-flex items-center gap-1 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold">{!! $svg($paths[$c['status']], 'h-3.5 w-3.5') !!} {{ $name[$c['status']] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            {{-- HISTORY --}}
            <div class="card mt-4 p-4">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-sm font-semibold text-ink-900 dark:text-white">Tekshiruvlar tarixi</div>
                        <div class="text-xs text-ink-500">{{ $historyCount }} ta tekshiruv · bitta ustun ≈ {{ ['24h' => '24 daqiqa', '7d' => '2 soat 48 daqiqa', '30d' => '12 soat'][$range] }}</div>
                    </div>
                    <div class="inline-flex rounded-xl bg-ink-100 p-1 text-sm dark:bg-ink-800">
                        @foreach ($ranges as $k => $cfg)
                            <a href="{{ route('system-status.index', ['range' => $k]) }}" data-range class="rounded-lg px-3 py-1 font-medium {{ $range === $k ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-700 dark:text-white' : 'text-ink-600 dark:text-ink-300' }}">{{ $cfg['label'] }}</a>
                        @endforeach
                    </div>
                </div>

                @if ($selfHealed && $cronGapMinutes !== null && $cronGapMinutes > 20)
                    <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                        Rejalashtiruvchi (cron) {{ $cronGapMinutes >= 120 ? intdiv($cronGapMinutes, 60).' soatdan ortiq' : $cronGapMinutes.' daqiqa' }} tekshiruv yozmagan. Tarix hozir tiklandi; serverda <code>schedule:run</code> / <code>schedule:work</code> ishlab turganini tekshiring.
                    </div>
                @endif
                @if ($historyCount === 0)
                    <p class="text-sm text-ink-500">Bu davrda tarix yo'q. Cron ishga tushgach har 15 daqiqada yoziladi, yoki sAdmin tekshiruvni qo'lda ishga tushiradi.</p>
                @else
                    <div class="flex h-20 items-end gap-[3px]" role="img" aria-label="Tekshiruvlar tarixi">
                        @foreach ($buckets as $i => $b)
                            @php
                                $h = ['ok' => 40, 'warn' => 70, 'fail' => 100, 'none' => 18][$b['status']];
                                $pos = $i < 12 ? 'left-0' : ($i > 47 ? 'right-0' : 'left-1/2 -translate-x-1/2');
                            @endphp
                            <div class="ss-barwrap relative flex h-full flex-1 items-end" tabindex="0">
                                <div class="ss-bar {{ $tone[$b['status']] }} w-full rounded-[3px]" style="--i: {{ $i }}; height: {{ $h }}%; background: var(--c); {{ $b['status'] === 'none' ? 'opacity:.5' : '' }}"></div>
                                <div class="ss-tip absolute bottom-full z-20 mb-2 w-60 {{ $pos }} rounded-xl border border-ink-200 bg-white p-3 text-xs shadow-xl dark:border-ink-600 dark:bg-ink-800">
                                    <div class="font-semibold text-ink-900 dark:text-white">{{ $b['from']->copy()->timezone($tz)->format('d.m H:i') }} – {{ $b['to']->copy()->timezone($tz)->format('H:i') }}</div>
                                    @if ($b['total'] === 0)
                                        <div class="mt-1 text-ink-500">Tekshiruv yozilmagan (cron ishlamagan bo'lishi mumkin).</div>
                                    @else
                                        <div class="mt-1 text-ink-500">{{ $b['total'] }} ta tekshiruv · {{ $b['ok'] }} tasi mukammal</div>
                                        @foreach (array_slice($issues[$b['worst']->id] ?? [], 0, 4) as $is)
                                            <div class="{{ $tone[$is['status']] }} mt-1.5 flex gap-1.5"><span class="mt-1 h-2 w-2 shrink-0 rounded-full" style="background: var(--c)"></span><span class="text-ink-700 dark:text-ink-200"><b>{{ $is['label'] }}</b>: {{ \Illuminate\Support\Str::limit($is['detail'], 70) }}</span></div>
                                        @endforeach
                                        @if ($b['status'] === 'ok')<div class="mt-1.5 text-emerald-600 dark:text-emerald-400">Hammasi joyida edi.</div>@endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-ink-500">
                        <span>{{ $buckets[0]['from']->copy()->timezone($tz)->format('d.m H:i') }}</span>
                        <span class="hidden items-center gap-3 sm:flex">
                            <span class="inline-flex items-center gap-1"><i class="h-2 w-2 rounded-sm bg-emerald-500"></i>Yaxshi</span>
                            <span class="inline-flex items-center gap-1"><i class="h-2 w-2 rounded-sm bg-amber-500"></i>Diqqat</span>
                            <span class="inline-flex items-center gap-1"><i class="h-2 w-2 rounded-sm bg-rose-500"></i>Muammo</span>
                            <span class="inline-flex items-center gap-1"><i class="h-2 w-2 rounded-sm bg-slate-300 dark:bg-slate-600"></i>Ma'lumot yo'q</span>
                        </span>
                        <span>Hozir</span>
                    </div>
                @endif
            </div>

            {{-- INCIDENTS --}}
            <div class="card mt-4 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-ink-100 px-4 py-3 dark:border-ink-700">
                    <span class="ss-warn ss-tile flex h-8 w-8 items-center justify-center rounded-lg">{!! $svg($paths['warn'], 'h-4 w-4') !!}</span>
                    <div class="flex-1 text-sm font-semibold text-ink-900 dark:text-white">Hodisalar · {{ $ranges[$range]['label'] }}</div>
                    <span class="text-xs text-ink-500">{{ count($incidents) }} ta</span>
                </div>
                @forelse ($incidents as $inc)
                    @php $st = $inc['worst']->status; @endphp
                    <div class="ss-row ss-in {{ $tone[$st] }} flex flex-wrap items-start gap-3 border-b border-ink-100 px-4 py-3 last:border-0 dark:border-ink-700" style="--i: {{ $loop->index }}">
                        <span class="ss-tile mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl">{!! $svg($paths[$st], 'h-[18px] w-[18px]') !!}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="ss-pill rounded-full px-2 py-0.5 text-xs font-semibold">{{ $st === 'fail' ? 'Muammo' : 'Ogohlantirish' }}</span>
                                @if ($inc['end'])
                                    <span class="ss-ok ss-pill rounded-full px-2 py-0.5 text-xs font-semibold">Tuzaldi</span>
                                @else
                                    <span class="ss-fail ss-pill inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-semibold"><span class="ss-pingdot h-1.5 w-1.5 rounded-full" style="background: var(--c)"></span>Davom etmoqda</span>
                                @endif
                                <span class="text-xs text-ink-500">{{ $inc['count'] }} ta tekshiruvda</span>
                            </div>
                            <div class="mt-1 text-sm text-ink-700 dark:text-ink-200">
                                @foreach (array_slice($issues[$inc['worst']->id] ?? [], 0, 3) as $is)
                                    <div><b>{{ $is['label'] }}</b>: {{ \Illuminate\Support\Str::limit($is['detail'], 110) }}</div>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right text-xs text-ink-500">
                            <div>{{ $inc['start']->copy()->timezone($tz)->format('d.m H:i') }} → {{ $inc['end'] ? $inc['end']->copy()->timezone($tz)->format('d.m H:i') : 'hozir' }}</div>
                            <div class="mt-0.5 font-medium text-ink-700 dark:text-ink-200">{{ $fmtDur($inc['start'], $inc['end']) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center gap-3 px-4 py-6 text-sm text-ink-500">
                        <span class="ss-ok ss-tile flex h-9 w-9 items-center justify-center rounded-xl">{!! $svg($paths['ok'], 'h-5 w-5') !!}</span>
                        Bu davrda hodisa bo'lmagan. Tizim barqaror ishladi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const wrap = document.getElementById('status-wrap');
    if (!wrap) return;
    const KEY = 'ss.filter';
    let left = 30, busy = false;

    function paintFilter() {
        const f = wrap.dataset.filter;
        document.querySelectorAll('#ss-filter button').forEach(b => {
            const on = b.dataset.f === f;
            b.className = 'rounded-lg px-3 py-1 font-medium ' + (on ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-700 dark:text-white' : 'text-ink-600 dark:text-ink-300');
        });
    }
    try { wrap.dataset.filter = localStorage.getItem(KEY) || 'all'; } catch (e) {}
    paintFilter();

    async function refresh(url) {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const r = await fetch(url || wrap.dataset.url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (!r.ok) return;
            const doc = new DOMParser().parseFromString(await r.text(), 'text/html');
            const fresh = doc.getElementById('status-root');
            if (fresh) { document.getElementById('status-root').replaceWith(fresh); paintFilter(); }
            if (url) wrap.dataset.url = url;
        } catch (e) { /* tarmoq xatosi: keyingi urinishda */ } finally { busy = false; left = 30; }
    }

    document.addEventListener('click', e => {
        const fb = e.target.closest('#ss-filter button');
        if (fb) { wrap.dataset.filter = fb.dataset.f; try { localStorage.setItem(KEY, fb.dataset.f); } catch (x) {} paintFilter(); return; }
        const rg = e.target.closest('a[data-range]');
        if (rg && wrap.contains(rg)) { e.preventDefault(); history.replaceState(null, '', rg.href); refresh(rg.href); }
    });

    setInterval(() => {
        left--;
        const el = document.getElementById('ss-countdown');
        if (el) el.textContent = Math.max(left, 0);
        if (left <= 0) refresh();
    }, 1000);
})();
</script>
@endpush
