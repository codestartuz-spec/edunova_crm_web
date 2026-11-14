@props(['label', 'value', 'icon' => 'chart', 'tone' => 'brand', 'unit' => 'int', 'sub' => null, 'valueClass' => ''])
@php
    // Eski "tone" qiymatlari ikonka ranglariga o'giriladi; "brand" bo'lsa ikonka o'z rangida chiqadi
    $iconTone = ['emerald' => 'emerald', 'amber' => 'amber', 'red' => 'rose', 'blue' => 'sky', 'ink' => 'slate'][$tone] ?? null;
@endphp
<div {{ $attributes->class(['card card-hover card-body stat-card group flex items-center gap-4', 'stat-tone-'.$tone => $tone !== 'brand']) }}>
    <x-icon :name="$icon" bubble :tone="$iconTone" class="h-12 w-12 !rounded-2xl" />
    <div class="min-w-0">
        <div class="truncate text-xl font-bold tabular-nums tracking-tight min-[1536px]:text-2xl text-ink-900 dark:text-white {{ $valueClass }}" @if (is_numeric($value) && $unit !== 'raw') data-countup="{{ (int) $value }}" data-unit="{{ $unit }}" @endif>{{ is_numeric($value) && $unit === 'money' ? \App\Support\Format::money($value) : (is_numeric($value) ? number_format($value, 0, '', ' ').($unit === 'percent' ? '%' : '') : $value) }}</div>
        <div class="text-sm font-medium text-ink-500 dark:text-ink-400">{{ $label }}@if ($sub) <span class="text-ink-400">&middot; {{ $sub }}</span>@endif</div>
    </div>
</div>
