{{-- Mini grafik (sparkline). $points: raqamlar ro'yxati. Animatsiyasi motion.js da. --}}
@props(['points' => [], 'w' => 96, 'h' => 30])
@php
    $pts = array_values(array_map('floatval', $points));
    $n = count($pts);
@endphp
@if ($n >= 2)
    @php
        $min = min($pts); $max = max($pts); $span = ($max - $min) ?: 1; $pad = 3;
        $xy = [];
        foreach ($pts as $i => $v) {
            $xy[] = [round($pad + ($w - 2 * $pad) * $i / ($n - 1), 1), round($h - $pad - ($h - 2 * $pad) * ($v - $min) / $span, 1)];
        }
        $line = 'M'.implode(' L', array_map(fn ($p) => $p[0].','.$p[1], $xy));
        $area = $line.' L'.$xy[$n - 1][0].','.$h.' L'.$xy[0][0].','.$h.' Z';
    @endphp
    <svg {{ $attributes->class(['spark text-brand-500']) }} viewBox="0 0 {{ $w }} {{ $h }}" width="{{ $w }}" height="{{ $h }}" aria-hidden="true">
        <path data-area d="{{ $area }}" fill="currentColor" opacity=".12" />
        <path data-line d="{{ $line }}" pathLength="1" stroke-dasharray="1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
@endif
