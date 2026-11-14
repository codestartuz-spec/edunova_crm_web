@props(['class' => 'h-10 w-10'])
{{-- v20: logotip yuklangan bo'lsa - rasm, aks holda tizim qisqa nomining bosh harfi (brend gradientida) --}}
@if ($logo = \App\Support\Brand::logoUrl())
    <img src="{{ $logo }}" alt="{{ \App\Support\Brand::name() }}" {{ $attributes->merge(['class' => "inline-block $class shrink-0 rounded-xl object-contain"]) }}>
@else
    <span {{ $attributes->merge(['class' => "brand-chip inline-flex $class shrink-0 items-center justify-center rounded-xl font-black tracking-tight"]) }}>{{ \App\Support\Brand::initial() }}</span>
@endif
