{{-- 3D kub (faqat bezak). $s - o'lcham px, $t - aylanish soniyasi, $tone - brand|accent|sky|green, $d - kechikish, $sy - true bo'lsa scroll bilan aylanadi --}}
@php($s = $s ?? 56)
<div class="cube cube-{{ $tone ?? 'brand' }} {{ ($sy ?? false) ? 'cube-sy' : '' }}" style="--s:{{ $s }}px;--t:{{ $t ?? 16 }}s;--r0:{{ $r0 ?? 0 }};animation-delay:-{{ $d ?? 0 }}s" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></div>
