{{-- Hikoya sahnasi: to'liq SVG illyustratsiya (viewBox 500x400). $n: 1..5; $uid: id to'qnashmasligi uchun ('d' | 'm') --}}
@php $g = "gb{$uid}{$n}"; @endphp
<svg viewBox="0 0 500 400" class="h-full w-full" role="img" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="{{ $g }}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="rgb(var(--brand-500))"/><stop offset="1" stop-color="rgb(var(--brand-700))"/></linearGradient>
        <linearGradient id="{{ $g }}a" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="rgb(var(--brand-500))" stop-opacity=".35"/><stop offset="1" stop-color="rgb(var(--brand-500))" stop-opacity="0"/></linearGradient>
        <filter id="{{ $g }}s" x="-20%" y="-20%" width="140%" height="150%"><feDropShadow dx="0" dy="8" stdDeviation="9" flood-color="#2a1f7a" flood-opacity=".16"/></filter>
    </defs>
    {{-- fon bezaklari --}}
    <circle cx="430" cy="60" r="70" class="il-brand-l"/><circle cx="50" cy="350" r="56" class="il-acc-l"/>
    <g class="a-float" style="animation-delay:-1s"><path d="M455 330l5 12 12 5-12 5-5 12-5-12-12-5 12-5z" class="il-acc"/></g>
    <g class="a-float" style="animation-delay:-2.2s"><path d="M40 70l3.5 8 8 3.5-8 3.5-3.5 8-3.5-8-8-3.5 8-3.5z" class="il-brand-m"/></g>

@switch($n)
@case(1)
    {{-- Ariza manbalari -> voronka -> yangi ariza --}}
    @php $src = [[70, 70, 'acc', 'ig'], [70, 160, 'sky', 'tg'], [70, 250, 'green', 'ph'], [70, 340, 'brand', 'web']]; @endphp
    @foreach ($src as [$sx, $sy, $col, $ic])
        <path d="M{{ $sx + 28 }} {{ $sy }} C 170 {{ $sy }}, 170 200, 224 200" class="il-flow a-draw" style="--len:220; --i:{{ $loop->index }}"/>
        <circle r="5" class="il-{{ $col }}"><animateMotion dur="2.6s" begin="{{ $loop->index * 0.5 }}s" repeatCount="indefinite" path="M{{ $sx + 28 }} {{ $sy }} C 170 {{ $sy }}, 170 200, 224 200"/></circle>
        <g transform="translate({{ $sx }} {{ $sy }})"><g class="a-pop" style="--i:{{ $loop->index }}">
            <circle r="27" class="il-{{ $col }}" filter="url(#{{ $g }}s)"/>
            @if ($ic === 'ig')<rect x="-10" y="-10" width="20" height="20" rx="6" class="il-glyph-s"/><circle r="4.5" class="il-glyph-s"/><circle cx="6" cy="-6" r="1.4" class="il-white"/>
            @elseif ($ic === 'tg')<path d="M-11 1 L11 -10 L5 11 L0 4 L-6 7 L-3 0 Z" class="il-white"/>
            @elseif ($ic === 'ph')<path d="M-8 -9 q-3 2 -2 6 q3 9 12 12 q4 1 6 -2 l-4 -5 l-4 2 q-4 -2 -6 -6 l2 -4 z" class="il-white"/>
            @else<circle r="10" class="il-glyph-s"/><ellipse rx="4.5" ry="10" class="il-glyph-s"/><path d="M-10 0h20" class="il-glyph-s"/>@endif
        </g></g>
    @endforeach
    <g transform="translate(250 200)"><g class="a-pop" style="--i:4">
        <circle r="46" class="a-pulse il-brand-l"/><circle r="40" fill="url(#{{ $g }})" filter="url(#{{ $g }}s)"/>
        <path d="M-15 -13 H15 L4.5 2 V14 L-4.5 10 V2 Z" class="il-white"/>
    </g></g>
    <path d="M296 200 H338" class="il-flow a-draw" style="--len:50; --i:5"/>
    <g class="a-r" style="--i:5" filter="url(#{{ $g }}s)">
        <rect x="338" y="140" width="136" height="120" rx="18" class="il-card"/>
        <circle cx="370" cy="176" r="15" class="il-brand-m"/><circle cx="370" cy="172" r="5.5" class="il-white"/><path d="M360 186 q10 -8 20 0" class="il-white"/>
        <rect x="394" y="167" width="64" height="8" rx="4" class="il-line"/><rect x="394" y="181" width="40" height="7" rx="3.5" class="il-sub"/>
        <rect x="352" y="208" width="108" height="30" rx="15" class="il-brand"/><path d="M386 223 l5 5 l10 -11" class="il-check"/>
    </g>
    <g transform="translate(468 138)"><g class="a-pop" style="--i:6"><circle r="13" class="il-acc"/><circle r="13" class="a-pulse il-acc-l"/><path d="M-5 4 h10 l-1.5 -2 v-4 a3.5 3.5 0 0 0 -7 0 v4 z" class="il-white"/></g></g>
    @break

@case(2)
    {{-- Guruhga qabul --}}
    <g filter="url(#{{ $g }}s)" class="a-up"><rect x="40" y="60" width="270" height="290" rx="24" class="il-card"/></g>
    <rect x="62" y="84" width="110" height="12" rx="6" class="il-line a-sx" style="--i:1"/>
    <rect x="62" y="104" width="64" height="9" rx="4.5" class="il-sub a-sx" style="--i:2"/>
    <g class="a-pop" style="--i:3"><rect x="212" y="82" width="78" height="26" rx="13" class="il-green-l"/><path d="M234 95 l5 5 l10 -11" class="il-check-g"/></g>
    @php $av = [[88,170,'brand'],[148,170,'acc'],[208,170,'sky'],[268,170,'green'],[88,230,'acc'],[148,230,'brand'],[208,230,'green']]; @endphp
    @foreach ($av as [$ax, $ay, $ac])
        <g transform="translate({{ $ax }} {{ $ay }})"><g class="a-pop" style="--i:{{ $loop->index + 2 }}"><circle r="25" class="il-{{ $ac }}-l"/><circle cy="-6" r="8.5" class="il-{{ $ac }}"/><path d="M-14 15 q14 -17 28 0 v4 h-28z" class="il-{{ $ac }}"/></g></g>
    @endforeach
    <g transform="translate(268 230)"><g class="a-pop" style="--i:9"><circle r="25" class="il-brand-l"/><path d="M-9 0h18M0 -9v18" class="il-plus"/></g></g>
    <rect x="62" y="278" width="226" height="14" rx="7" class="il-sub"/><rect x="62" y="278" width="226" height="14" rx="7" fill="url(#{{ $g }})" class="a-sx" style="--i:10; transform-origin:left; --w:80%"/>
    <rect x="62" y="304" width="62" height="24" rx="12" class="il-brand-l a-pop" style="--i:11"/><rect x="132" y="304" width="62" height="24" rx="12" class="il-acc-l a-pop" style="--i:12"/><rect x="202" y="304" width="62" height="24" rx="12" class="il-sky-l a-pop" style="--i:13"/>
    {{-- progress halqasi --}}
    <g transform="translate(395 120)"><g class="a-pop" style="--i:6">
        <circle r="46" class="il-ring-bg"/><circle r="46" class="il-ring a-draw" style="--len:289; --i:7" transform="rotate(-90)"/>
        <path d="M-12 1 l8 9 l17 -19" class="il-check-g big"/>
    </g></g>
    {{-- shartnoma --}}
    <g class="a-r" style="--i:8" filter="url(#{{ $g }}s)">
        <rect x="340" y="206" width="124" height="148" rx="16" class="il-card"/>
        <rect x="356" y="224" width="60" height="9" rx="4.5" class="il-line"/>
        <rect x="356" y="242" width="92" height="7" rx="3.5" class="il-sub"/><rect x="356" y="256" width="80" height="7" rx="3.5" class="il-sub"/><rect x="356" y="270" width="88" height="7" rx="3.5" class="il-sub"/>
        <path d="M358 318 q10 -22 18 -6 t16 -2 t16 4" class="il-sign a-draw" style="--len:100; --i:12"/>
        <g transform="translate(436 326)"><g class="a-pop" style="--i:13"><circle r="14" class="il-green"/><path d="M-6 0 l4 4 l8 -9" class="il-check"/></g></g>
    </g>
    @break

@case(3)
    {{-- Davomat: telefon + kalendar --}}
    <g filter="url(#{{ $g }}s)" class="a-l"><rect x="48" y="34" width="156" height="332" rx="28" class="il-card"/></g>
    <rect x="102" y="46" width="48" height="8" rx="4" class="il-line"/>
    @foreach ([[96,'brand',true],[160,'acc',true],[224,'sky',false],[288,'green',true]] as [$ry, $rc, $on])
        <g transform="translate(0 {{ $ry }})"><g class="a-up" style="--i:{{ $loop->index + 1 }}">
            <rect x="62" y="0" width="128" height="50" rx="14" class="il-sub"/>
            <circle cx="86" cy="25" r="14" class="il-{{ $rc }}"/><circle cx="86" cy="21" r="5" class="il-white"/><path d="M78 33 q8 -8 16 0" class="il-white"/>
            <rect x="108" y="17" width="36" height="7" rx="3.5" class="il-line"/>
            <rect x="152" y="15" width="30" height="20" rx="10" class="{{ $on ? 'il-green' : 'il-rose' }}"/>
            <circle cx="{{ $on ? 172 : 162 }}" cy="25" r="7.5" class="il-white"/>
        </g></g>
    @endforeach
    <g filter="url(#{{ $g }}s)" class="a-r"><rect x="232" y="46" width="226" height="190" rx="22" class="il-card"/></g>
    <rect x="252" y="64" width="84" height="11" rx="5.5" class="il-line"/>
    @for ($i = 0; $i < 21; $i++)
        @php $cx = 258 + ($i % 7) * 29; $cy = 96 + intdiv($i, 7) * 43; $miss = in_array($i, [4, 10, 15]); @endphp
        <g transform="translate({{ $cx }} {{ $cy }})"><g class="a-pop" style="--i:{{ 2 + $i * 0.45 }}">
            <rect width="24" height="36" rx="8" class="{{ $miss ? 'il-rose-l' : 'il-green-l' }}"/>
            @if ($miss)<path d="M7 13 l10 10 m0 -10 l-10 10" class="il-x"/>@else<path d="M6.5 19 l4.5 4.5 l7 -9" class="il-check-g"/>@endif
        </g></g>
    @endfor
    <g transform="translate(300 316)"><g class="a-pop" style="--i:14">
        <circle r="46" class="il-ring-bg"/><circle r="46" class="il-ring a-draw" style="--len:289; --off:23; --i:15" transform="rotate(-90)"/>
        <text y="7" text-anchor="middle" class="il-num">92%</text>
    </g></g>
    <g transform="translate(412 316)"><g class="a-pop" style="--i:16"><rect x="-44" y="-26" width="88" height="52" rx="16" class="il-acc-l"/><path d="M-14 8 l10 -12 l8 8 l12 -16" class="il-trend"/></g></g>
    @break

@case(4)
    {{-- To'lov: chek, tangalar, Telegram xabari --}}
    <g filter="url(#{{ $g }}s)" class="a-l"><path d="M56 40 h170 a14 14 0 0 1 14 14 V350 l-14 -9 l-14 9 l-14 -9 l-14 9 l-14 -9 l-14 9 l-14 -9 l-14 9 l-14 -9 l-14 9 l-14 -9 l-14 9 V54 a14 14 0 0 1 14 -14z" class="il-card"/></g>
    <circle cx="141" cy="84" r="24" class="il-acc"/><path d="M129 84h24M141 72v24" class="il-glyph-s" style="stroke-width:0"/><text x="141" y="92" text-anchor="middle" class="il-num il-white">$</text>
    @foreach ([[128, 96], [154, 72], [180, 84], [206, 60]] as [$ly, $lw])
        <rect x="80" y="{{ $ly }}" width="{{ $lw }}" height="9" rx="4.5" class="il-line a-sx" style="--i:{{ $loop->index + 2 }}"/>
        <rect x="{{ 80 + $lw + 8 }}" y="{{ $ly }}" width="{{ 136 - $lw - 8 }}" height="9" rx="4.5" class="il-sub a-sx" style="--i:{{ $loop->index + 2 }}"/>
    @endforeach
    <line x1="80" y1="244" x2="202" y2="244" class="il-dash"/>
    <rect x="80" y="258" width="62" height="14" rx="7" class="il-line"/><rect x="146" y="256" width="56" height="18" rx="9" class="il-brand a-sx" style="--i:7"/>
    <g transform="translate(141 308)"><g class="a-pop" style="--i:8"><circle r="21" class="il-green"/><circle r="21" class="a-pulse il-green-l"/><path d="M-9 1 l6 6 l12 -13" class="il-check"/></g></g>
    {{-- tangalar ustuni --}}
    @foreach ([0, 1, 2, 3] as $k)
        <g transform="translate(370 {{ 318 - $k * 20 }})"><g class="a-pop" style="--i:{{ 5 + $k }}"><ellipse rx="42" ry="14" class="il-coin-s"/><rect x="-42" y="0" width="84" height="12" class="il-coin-s"/><ellipse rx="42" ry="14" cy="-1" class="il-coin"/><ellipse rx="26" ry="8" cy="-1" class="il-coin-in"/></g></g>
    @endforeach
    <g transform="translate(436 214)"><g class="a-float"><g class="a-pop" style="--i:10"><rect x="-34" y="-20" width="68" height="40" rx="9" class="il-green"/><circle r="11" fill="#a7f3d0"/><circle cx="-24" cy="-11" r="3" class="il-white"/><circle cx="24" cy="11" r="3" class="il-white"/></g></g></g>
    <g class="a-r" style="--i:9" filter="url(#{{ $g }}s)">
        <path d="M300 70 h128 a16 16 0 0 1 16 16 v34 a16 16 0 0 1 -16 16 h-100 l-22 18 v-18 h-6 a16 16 0 0 1 -16 -16 v-34 a16 16 0 0 1 16 -16z" class="il-sky-l"/>
        <circle cx="326" cy="103" r="14" class="il-sky"/><path d="M319 104 L334 97 L330 111 L326 106 L322 108 L324 103 Z" class="il-white"/>
        <rect x="350" y="94" width="76" height="8" rx="4" class="il-sky"/><rect x="350" y="110" width="52" height="7" rx="3.5" class="il-white"/>
    </g>
    @break

@default
    {{-- Hisobot: boshqaruv paneli --}}
    <g filter="url(#{{ $g }}s)" class="a-up"><rect x="36" y="48" width="428" height="318" rx="26" class="il-card"/></g>
    @foreach ([[56,'brand'],[184,'acc'],[312,'green']] as [$kx, $kc])
        <g transform="translate({{ $kx }} 68)"><g class="a-pop" style="--i:{{ $loop->index + 1 }}"><rect width="116" height="62" rx="14" class="il-{{ $kc }}-l"/><circle cx="26" cy="31" r="14" class="il-{{ $kc }}"/><rect x="48" y="20" width="52" height="8" rx="4" class="il-{{ $kc }}"/><rect x="48" y="36" width="36" height="7" rx="3.5" class="il-white"/></g></g>
    @endforeach
    @foreach ([46, 64, 52, 82, 68, 100, 86, 118] as $bh)
        <rect x="{{ 62 + $loop->index * 26 }}" y="{{ 342 - $bh * 1.2 }}" width="17" height="{{ $bh * 1.2 }}" rx="5" fill="url(#{{ $g }})" class="a-bar" style="--i:{{ $loop->index + 3 }}"/>
    @endforeach
    <path d="M70 262 C 120 230, 150 270, 195 226 S 250 214, 268 188" class="il-trend-l a-draw" style="--len:260; --i:11"/>
    <g transform="translate(268 188)"><g class="a-pop" style="--i:13"><circle r="8" class="il-acc"/><circle r="8" class="a-pulse il-acc-l"/></g></g>
    <g transform="translate(386 258)"><g class="a-pop" style="--i:12">
        <circle r="48" class="il-ring-bg"/><circle r="48" class="il-ring-acc a-draw" style="--len:302; --off:105; --i:12" transform="rotate(-90)"/><circle r="48" class="il-ring-sky a-draw" style="--len:302; --off:210; --i:13" transform="rotate(60)"/>
        <circle r="26" class="il-card"/><path d="M-9 8 v-7 M0 8 v-17 M9 8 v-11" class="il-glyph-b"/>
    </g></g>
    <g class="a-r" style="--i:14" filter="url(#{{ $g }}s)">
        <path d="M338 24 h110 a14 14 0 0 1 14 14 v28 a14 14 0 0 1 -14 14 h-84 l-20 16 v-16 h-6 a14 14 0 0 1 -14 -14 v-28 a14 14 0 0 1 14 -14z" class="il-sky"/>
        <path d="M356 52 L376 43 L371 63 L366 56 L360 59 L363 52 Z" class="il-white"/>
        <rect x="388" y="44" width="52" height="7" rx="3.5" class="il-white"/><rect x="388" y="58" width="34" height="6" rx="3" fill="#fff" fill-opacity=".6"/>
    </g>
@endswitch
</svg>
