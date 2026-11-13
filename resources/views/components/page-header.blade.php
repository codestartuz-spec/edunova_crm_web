@props(['title', 'subtitle' => null, 'icon' => null])
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        @if ($icon)
            <span class="brand-chip flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl"><x-icon :name="$icon" class="h-5 w-5" /></span>
        @else
            <span class="page-accent"></span>
        @endif
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">{{ $title }}</h1>
            @if ($subtitle)<p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">{{ $subtitle }}</p>@endif
        </div>
    </div>
    @isset($actions)<div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>@endisset
</div>
