@props(['icon' => 'search', 'title' => "Ma'lumot topilmadi", 'text' => null])
<div class="empty-state">
    <span class="empty-icon"><x-icon :name="$icon" class="h-7 w-7" /></span>
    <div class="font-semibold text-ink-800 dark:text-ink-100">{{ $title }}</div>
    @if ($text)<p class="max-w-sm text-sm">{{ $text }}</p>@endif
    {{ $slot }}
</div>
