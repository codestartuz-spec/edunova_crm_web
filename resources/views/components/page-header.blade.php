@props(['title', 'subtitle' => null, 'icon' => null])
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex min-w-0 items-center gap-3.5">
        @if ($icon)
            <span class="brand-chip grid h-12 w-12 shrink-0 place-items-center rounded-2xl"><x-icon :name="$icon" class="h-6 w-6" /></span>
        @else
            <span class="page-accent"></span>
        @endif
        <div class="min-w-0">
            <h1 class="truncate text-2xl font-extrabold tracking-tight text-ink-900 dark:text-white sm:text-[1.7rem]">{{ $title }}</h1>
            @if ($subtitle)<p class="mt-0.5 text-sm text-ink-500 dark:text-ink-400">{{ $subtitle }}</p>@endif
        </div>
    </div>
    @isset($actions)<div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>@endisset
</div>
