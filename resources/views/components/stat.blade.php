@props(['label', 'value', 'icon' => 'chart', 'tone' => 'brand', 'unit' => 'int', 'sub' => null, 'valueClass' => ''])
<div {{ $attributes->class(['card card-hover card-body stat-card flex items-center gap-4', 'stat-tone-'.$tone => $tone !== 'brand']) }}>
    <span class="stat-icon"><x-icon :name="$icon" class="h-6 w-6" /></span>
    <div class="min-w-0">
        <div class="truncate text-2xl font-bold tabular-nums text-ink-900 dark:text-white {{ $valueClass }}" @if (is_numeric($value) && $unit !== 'raw') data-countup="{{ (int) $value }}" data-unit="{{ $unit }}" @endif>{{ is_numeric($value) && $unit === 'money' ? \App\Support\Format::money($value) : (is_numeric($value) ? number_format($value, 0, '', ' ').($unit === 'percent' ? '%' : '') : $value) }}</div>
        <div class="text-sm text-ink-500 dark:text-ink-400">{{ $label }}@if ($sub) <span class="text-ink-400">&middot; {!! $sub !!}</span>@endif</div>
    </div>
</div>
