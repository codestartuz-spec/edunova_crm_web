@props(['icon' => 'search', 'title' => "Ma'lumot topilmadi", 'text' => null])
<div class="empty-state">
    <x-icon :name="$icon" bubble class="empty-icon" />
    <div class="text-base font-bold text-ink-800 dark:text-ink-100">{{ $title }}</div>
    @if ($text)<p class="max-w-sm text-sm">{{ $text }}</p>@endif
    {{ $slot }}
</div>
