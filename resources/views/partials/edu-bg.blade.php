{{--
    v28: doimiy harakatdagi animatsion SVG fon (o'quv markaz mavzusida). Sahifa turiga qarab bitta sahna chiqadi:
    supplies - o'quv buyumlari | math - formula, raqam, harf, tanga | path - bilim yo'li | orbs - nur, suhbat va sayohat belgilari.
    Ranglar filial rangidan (--brand-*, --accent-*) olinadi. Parallaks: JS `--px/--py/--sy` o'zgaruvchilarini yangilaydi.
--}}
@php
    $scene = in_array($scene ?? '', \App\Support\Edu3D::SCENES, true) ? $scene : 'supplies';
    $col = ['--brand-400', '--brand-600', '--accent-400', '--brand-300', '--accent-500', '--brand-500'];
    // [belgi, x, y, o'lcham, burilish, rang, qatlam(0 orqa..2 old), davr(s), kechikish(s)]
    $supplies = [
        ['book', 120, 150, 58, -12, 0, 0, 11, 0], ['cap', 540, 80, 52, 10, 2, 0, 13, -3], ['pencil', 950, 70, 56, 35, 3, 0, 10, -5], ['bulb', 1330, 120, 54, -8, 2, 0, 12, -2],
        ['globe', 1500, 430, 60, 0, 1, 0, 14, -6], ['atom', 70, 540, 54, 0, 5, 0, 12, -4], ['ruler', 320, 640, 58, -30, 3, 0, 10, -1], ['notebook', 780, 520, 52, 8, 0, 0, 13, -7],
        ['star', 1030, 310, 32, 0, 2, 0, 6, -2], ['star', 240, 340, 28, 0, 4, 0, 7, -4], ['star', 1290, 650, 30, 0, 2, 0, 8, -1], ['chat', 1180, 520, 50, 6, 1, 0, 12, -8],
        ['globe', 220, 770, 86, -6, 5, 1, 15, -3], ['book', 1120, 830, 90, 12, 1, 1, 13, -6], ['bulb', 650, 840, 80, 6, 2, 1, 12, -2], ['cap', 1420, 750, 88, -10, 0, 1, 14, -9],
        ['pencil', 430, 440, 86, -40, 3, 1, 11, -5], ['notebook', 900, 210, 82, -6, 5, 1, 13, -4], ['atom', 1390, 260, 82, 0, 0, 1, 16, -7], ['plane', 600, 300, 62, 0, 2, 1, 12, -1],
        ['cap', 60, 400, 124, -14, 1, 2, 17, -5], ['notebook', 1480, 570, 120, 10, 0, 2, 16, -9], ['pencil', 770, 930, 122, 28, 2, 2, 15, -3], ['book', 1000, 450, 110, -6, 5, 2, 18, -11],
    ];
    $glyphs = [
        ['∑', 110, 160, 56, 0, 0, 12, 0], ['π', 520, 110, 60, 2, 0, 13, -3], ['√', 930, 90, 58, 3, 0, 11, -5], ['∞', 1320, 150, 62, 5, 0, 12, -2], ['%', 1500, 440, 54, 1, 0, 14, -6],
        ['Δ', 60, 560, 56, 0, 0, 12, -4], ['≈', 330, 660, 60, 3, 0, 10, -1], ['±', 770, 540, 52, 5, 0, 13, -7], ['÷', 1180, 600, 56, 2, 0, 12, -8], ['×', 1030, 300, 50, 4, 0, 9, -2],
        ['x²', 230, 790, 90, 1, 1, 14, -3], ['7×8', 1120, 840, 84, 5, 1, 13, -6], 
        ['∫', 890, 220, 100, 5, 1, 13, -4], ['2+2=4', 1370, 280, 70, 1, 1, 16, -7], ['a²+b²=c²', 560, 300, 54, 2, 1, 12, -1],
        ['π', 70, 410, 170, 0, 2, 17, -5], ['∑', 770, 930, 160, 5, 2, 15, -3], ['7', 1010, 470, 140, 3, 2, 18, -11],
    ];
    $stars = [[210, 150], [470, 250], [820, 120], [1100, 200], [1380, 90], [1540, 300], [90, 330], [640, 420], [980, 380], [1250, 420], [330, 520], [1460, 520]];
    $bars = [70, 110, 160, 130, 210, 270, 330, 420];
    // zarralar: [x, y, r, davr, kechikish, rang]
    $dust = [];
    for ($i = 0; $i < 26; $i++) { $dust[] = [(($i * 131 + 57) % 1560) + 20, (($i * 97 + 41) % 980) + 10, 2 + ($i % 4), 38 + ($i * 7) % 34, -(($i * 5) % 60), $col[$i % 6]]; }
    $cx = fn ($i) => $col[$i % count($col)];
@endphp

<div class="edu-bg" data-scene="{{ $scene }}" aria-hidden="true">
    <div class="edu-aurora edu-au-1"></div>
    <div class="edu-aurora edu-au-2"></div>
    <div class="edu-aurora edu-au-3"></div>

    <svg width="0" height="0" style="position:absolute" focusable="false">
        <defs>
            <symbol id="eb-book" viewBox="0 0 64 64"><path class="eb-f" d="M32 16C26 11 14 11 8 14v36c6-3 18-3 24 2zM32 16c6-5 18-5 24-2v36c-6-3-18-3-24 2z"/><path class="eb-l" d="M32 16C26 11 14 11 8 14v36c6-3 18-3 24 2 6-5 18-5 24-2V14c-6-3-18-3-24 2zM32 16v36M15 22c4-1 9-1 13 1M15 30c4-1 9-1 13 1M36 23c4-2 9-2 13-1M36 31c4-2 9-2 13-1"/></symbol>
            <symbol id="eb-cap" viewBox="0 0 64 64"><path class="eb-f" d="M32 12 60 26 32 40 4 26z"/><path class="eb-l" d="M32 12 60 26 32 40 4 26zM16 33v12c0 4 32 4 32 0V33M56 28v16"/><circle class="eb-l" cx="56" cy="47" r="3"/></symbol>
            <symbol id="eb-pencil" viewBox="0 0 64 64"><path class="eb-f" d="m12 52 4-12 28-28 8 8-28 28z"/><path class="eb-l" d="m12 52 4-12 28-28 8 8-28 28zM16 40l8 8M40 16l8 8"/></symbol>
            <symbol id="eb-bulb" viewBox="0 0 64 64"><path class="eb-f" d="M32 8c-11 0-17 8-17 16 0 7 4 10 7 15v5h20v-5c3-5 7-8 7-15 0-8-6-16-17-16z"/><path class="eb-l" d="M32 8c-11 0-17 8-17 16 0 7 4 10 7 15v5h20v-5c3-5 7-8 7-15 0-8-6-16-17-16zM25 50h14M27 56h10M32 20v10M28 26l4 4 4-4"/></symbol>
            <symbol id="eb-globe" viewBox="0 0 64 64"><circle class="eb-f" cx="32" cy="32" r="22"/><circle class="eb-l" cx="32" cy="32" r="22"/><ellipse class="eb-l" cx="32" cy="32" rx="9" ry="22"/><path class="eb-l" d="M10 32h44M14 20h36M14 44h36"/></symbol>
            <symbol id="eb-ruler" viewBox="0 0 64 64"><path class="eb-f" d="m8 40 32-32 16 16-32 32z"/><path class="eb-l" d="m8 40 32-32 16 16-32 32zM16 32l6 6M24 24l6 6M32 16l6 6M20 44l4 4"/></symbol>
            <symbol id="eb-atom" viewBox="0 0 64 64"><circle class="eb-f" cx="32" cy="32" r="5"/><ellipse class="eb-l" cx="32" cy="32" rx="26" ry="10"/><ellipse class="eb-l" cx="32" cy="32" rx="26" ry="10" transform="rotate(60 32 32)"/><ellipse class="eb-l" cx="32" cy="32" rx="26" ry="10" transform="rotate(120 32 32)"/><circle class="eb-l" cx="32" cy="32" r="3"/></symbol>
            <symbol id="eb-notebook" viewBox="0 0 64 64"><rect class="eb-f" x="14" y="8" width="38" height="48" rx="5"/><rect class="eb-l" x="14" y="8" width="38" height="48" rx="5"/><path class="eb-l" d="M22 20h22M22 28h22M22 36h14M8 16h10M8 28h10M8 40h10"/></symbol>
            <symbol id="eb-star" viewBox="0 0 64 64"><path class="eb-f" d="M32 6c2 16 10 24 26 26-16 2-24 10-26 26-2-16-10-24-26-26 16-2 24-10 26-26z"/><path class="eb-l" d="M32 6c2 16 10 24 26 26-16 2-24 10-26 26-2-16-10-24-26-26 16-2 24-10 26-26z"/></symbol>
            <symbol id="eb-chat" viewBox="0 0 64 64"><path class="eb-f" d="M10 14h44v26H30L18 52V40h-8z"/><path class="eb-l" d="M10 14h44v26H30L18 52V40h-8zM20 24h24M20 31h14"/></symbol>
            <symbol id="eb-plane" viewBox="0 0 64 64"><path class="eb-f" d="m6 30 52-22-14 48-14-18z"/><path class="eb-l" d="m6 30 52-22-14 48-14-18zM30 38 58 8"/></symbol>
        </defs>
    </svg>

    {{-- ===== Sahna ===== --}}
    @if ($scene === 'supplies')
        @foreach ([0, 1, 2] as $L)
            <svg class="edu-layer edu-l{{ $L }}" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
                @foreach ($supplies as [$sym, $x, $y, $sz, $rot, $ci, $lay, $t, $d])
                    @if ($lay === $L)
                        <g transform="translate({{ $x }} {{ $y }})"><g class="edu-it" style="--c: var({{ $cx($ci) }}); --t: {{ $t }}s; --d: {{ $d }}s; --r0: {{ $rot }}deg; --r1: {{ $rot + (($x % 2) ? 9 : -9) }}deg; --dx: {{ ($y % 2) ? 14 : -14 }}px">
                            <use href="#eb-{{ $sym }}" x="{{ -$sz / 2 }}" y="{{ -$sz / 2 }}" width="{{ $sz }}" height="{{ $sz }}"/>
                        </g></g>
                    @endif
                @endforeach
            </svg>
        @endforeach
    @elseif ($scene === 'math')
        <svg class="edu-layer edu-l0" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
            <defs><pattern id="eb-grid" width="64" height="64" patternUnits="userSpaceOnUse"><path d="M64 0H0V64" fill="none" stroke="rgb(var(--brand-500) / .09)" stroke-width="1"/></pattern></defs>
            <rect width="1600" height="1000" fill="url(#eb-grid)" class="edu-grid"/>
            <path class="edu-wave" d="M-40 640 C 120 520, 260 760, 420 640 S 720 520, 880 640 S 1180 760, 1340 640 S 1560 540, 1660 620" fill="none" stroke="rgb(var(--brand-500) / .35)" stroke-width="3" stroke-linecap="round"/>
            <path class="edu-wave edu-wave2" d="M-40 700 C 140 600, 280 800, 440 700 S 740 600, 900 700 S 1200 800, 1360 700 S 1560 620, 1660 690" fill="none" stroke="rgb(var(--accent-400) / .32)" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        @foreach ([0, 1, 2] as $L)
            <svg class="edu-layer edu-l{{ $L }}" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
                @foreach ($glyphs as $gi => [$txt, $x, $y, $sz, $rot, $lay, $t, $d])
                    @if ($lay === $L)
                        <g transform="translate({{ $x }} {{ $y }})"><g class="edu-it edu-glyph" style="--c: var({{ $cx($gi) }}); --t: {{ $t }}s; --d: {{ $d }}s; --r0: {{ $rot }}deg; --r1: {{ -$rot - 4 }}deg; --dx: {{ ($y % 2) ? 12 : -12 }}px">
                            <text x="0" y="0" font-size="{{ $sz }}" text-anchor="middle" dominant-baseline="central">{{ $txt }}</text>
                        </g></g>
                    @endif
                @endforeach
                @if ($L === 1)
                    @foreach ([[260, 300, 38], [1250, 760, 44], [700, 640, 34]] as $ci => [$x, $y, $r])
                        <g transform="translate({{ $x }} {{ $y }})"><g class="edu-it" style="--c: var(--accent-400); --t: {{ 9 + $ci * 2 }}s; --d: -{{ $ci * 3 }}s; --r0: -8deg; --r1: 12deg">
                            <circle r="{{ $r }}" fill="currentColor" fill-opacity=".16" stroke="currentColor" stroke-width="3"/><circle r="{{ $r - 8 }}" fill="none" stroke="currentColor" stroke-width="1.6" stroke-opacity=".7"/>
                            <text y="2" font-size="{{ $r * .5 }}" font-weight="800" text-anchor="middle" dominant-baseline="central" fill="currentColor" stroke="none">UZS</text>
                        </g></g>
                    @endforeach
                @endif
            </svg>
        @endforeach
    @elseif ($scene === 'path')
        <svg class="edu-layer edu-l0" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
            @foreach ($stars as $si => [$x, $y])
                <g transform="translate({{ $x }} {{ $y }})"><g class="edu-tw" style="--c: var({{ $cx($si) }}); --t: {{ 4 + ($si % 4) }}s; --d: -{{ $si * 0.7 }}s"><use href="#eb-star" x="-14" y="-14" width="28" height="28"/></g></g>
            @endforeach
            <path id="edu-road" d="M-40 900 C 200 790, 340 940, 580 810 S 900 650, 1110 570 S 1400 390, 1660 230" fill="none" stroke="rgb(var(--brand-500) / .10)" stroke-width="54" stroke-linecap="round"/>
            <path class="edu-dash" d="M-40 900 C 200 790, 340 940, 580 810 S 900 650, 1110 570 S 1400 390, 1660 230" fill="none" stroke="rgb(var(--brand-600) / .42)" stroke-width="3.5" stroke-linecap="round" stroke-dasharray="4 22"/>
        </svg>
        <svg class="edu-layer edu-l1" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
            <g transform="translate(1030 560)" class="edu-bars">
                @foreach ($bars as $bi => $h)
                    <rect class="edu-bar" x="{{ $bi * 58 }}" y="{{ -$h }}" width="40" height="{{ $h }}" rx="8" style="--d: -{{ $bi * 0.45 }}s; --c: var({{ $cx($bi) }})"/>
                @endforeach
                <path class="edu-trend" d="M20 -90 L78 -140 L136 -190 L194 -170 L252 -250 L310 -310 L368 -370 L426 -470" fill="none" stroke="rgb(var(--accent-400) / .85)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <g class="edu-it" style="--c: var(--accent-400); --t: 7s; --d: -2s; --r0: -6deg; --r1: 6deg" transform="translate(1520 200)"><path d="M0 60V-20" stroke="currentColor" stroke-width="4" stroke-linecap="round"/><path d="M0 -20 L50 -4 L0 14z" fill="currentColor" fill-opacity=".7" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/></g>
            <g style="color: rgb(var(--brand-600) / .8)">
                <circle r="22" fill="rgb(var(--accent-400) / .25)"><animateMotion dur="16s" repeatCount="indefinite"><mpath href="#edu-road"/></animateMotion></circle>
                <g><use href="#eb-cap" x="-26" y="-34" width="52" height="52"/><animateMotion dur="16s" repeatCount="indefinite"><mpath href="#edu-road"/></animateMotion></g>
            </g>
        </svg>
        <svg class="edu-layer edu-l2" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
            @foreach ([['book', 150, 760, 120, 0], ['bulb', 640, 250, 100, 2], ['globe', 1360, 760, 110, 5], ['cap', 330, 160, 96, 1]] as $bi => [$sym, $x, $y, $sz, $ci])
                <g transform="translate({{ $x }} {{ $y }})"><g class="edu-it" style="--c: var({{ $cx($ci) }}); --t: {{ 13 + $bi * 2 }}s; --d: -{{ $bi * 3 }}s; --r0: {{ $bi * 6 - 9 }}deg; --r1: {{ 9 - $bi * 5 }}deg"><use href="#eb-{{ $sym }}" x="{{ -$sz / 2 }}" y="{{ -$sz / 2 }}" width="{{ $sz }}" height="{{ $sz }}"/></g></g>
            @endforeach
        </svg>
    @else {{-- orbs --}}
        <svg class="edu-layer edu-l0" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
            <defs>
                <radialGradient id="eb-orb1"><stop offset="0" stop-color="rgb(var(--brand-400))" stop-opacity=".55"/><stop offset="1" stop-color="rgb(var(--brand-400))" stop-opacity="0"/></radialGradient>
                <radialGradient id="eb-orb2"><stop offset="0" stop-color="rgb(var(--accent-400))" stop-opacity=".45"/><stop offset="1" stop-color="rgb(var(--accent-400))" stop-opacity="0"/></radialGradient>
            </defs>
            @foreach ([[260, 220, 240, 1], [1320, 260, 280, 2], [760, 760, 300, 1], [1500, 800, 200, 2], [90, 820, 200, 2]] as $oi => [$x, $y, $r, $g])
                <g transform="translate({{ $x }} {{ $y }})"><g class="edu-it" style="--t: {{ 14 + $oi * 3 }}s; --d: -{{ $oi * 4 }}s; --r0: 0deg; --r1: 0deg; --dx: {{ $oi % 2 ? 40 : -40 }}px; --dy: -34px"><circle r="{{ $r }}" fill="url(#eb-orb{{ $g }})"/></g></g>
            @endforeach
            <g transform="translate(800 480)">
                <ellipse rx="520" ry="210" fill="none" stroke="rgb(var(--brand-500) / .22)" stroke-width="2" stroke-dasharray="3 14" stroke-linecap="round" class="edu-spin" transform="rotate(-18)"/>
                <ellipse rx="380" ry="150" fill="none" stroke="rgb(var(--accent-400) / .25)" stroke-width="2" stroke-dasharray="2 12" stroke-linecap="round" class="edu-spin edu-spin2" transform="rotate(14)"/>
            </g>
            @foreach ([[430, 330, 60], [1180, 640, 70], [900, 250, 40]] as $ri => [$x, $y, $r])
                <g transform="translate({{ $x }} {{ $y }})"><circle class="edu-ring" r="{{ $r }}" fill="none" stroke="rgb(var(--brand-500) / .4)" stroke-width="2.5" style="--d: -{{ $ri * 1.6 }}s"/></g>
            @endforeach
        </svg>
        @foreach ([1, 2] as $L)
            <svg class="edu-layer edu-l{{ $L }}" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
                @foreach ([['chat', 300, 520, 96, 0], ['plane', 1250, 300, 100, 2], ['star', 760, 150, 70, 2], ['chat', 1420, 640, 84, 5], ['plane', 520, 830, 88, 5], ['bulb', 1000, 700, 90, 0], ['star', 160, 250, 56, 4], ['cap', 900, 450, 92, 1], ['book', 140, 760, 100, 3], ['star', 1480, 140, 52, 2]] as $oi => [$sym, $x, $y, $sz, $ci])
                    @if ($oi % 2 === $L - 1)
                        <g transform="translate({{ $x }} {{ $y }})"><g class="edu-it {{ $sym === 'star' ? 'edu-tw' : '' }}" style="--c: var({{ $cx($ci) }}); --t: {{ 10 + $oi }}s; --d: -{{ $oi * 1.7 }}s; --r0: {{ $oi * 5 - 14 }}deg; --r1: {{ 12 - $oi * 4 }}deg; --dy: -26px"><use href="#eb-{{ $sym }}" x="{{ -$sz / 2 }}" y="{{ -$sz / 2 }}" width="{{ $sz }}" height="{{ $sz }}"/></g></g>
                    @endif
                @endforeach
            </svg>
        @endforeach
    @endif

    {{-- Barcha sahnalarda: sekin ko'tariluvchi bilim "zarralari" --}}
    <svg class="edu-layer edu-ld" viewBox="0 0 1600 1000" preserveAspectRatio="xMidYMid slice" focusable="false">
        @foreach ($dust as [$x, $y, $r, $t, $d, $c])
            <circle class="edu-dust" cx="{{ $x }}" cy="{{ $y }}" r="{{ $r }}" style="--c: var({{ $c }}); --t: {{ $t }}s; --d: {{ $d }}s"/>
        @endforeach
    </svg>
</div>
