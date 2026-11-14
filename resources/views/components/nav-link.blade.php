@props(['href', 'icon', 'active' => false])
<a href="{{ $href }}" {{ $attributes }} @class(['nav-link', 'nav-link-active' => $active]) @if ($active) aria-current="page" @endif>
    <x-icon :name="$icon" bubble class="nav-ic h-8 w-8" />
    <span class="truncate">{{ $slot }}</span>
</a>
