@props(['class' => 'mb-8'])
{{-- Kirish/yordamchi sahifalar sarlavhasi: gorizontal logotip bo'lsa o'zi, bo'lmasa kvadrat logotip + nom --}}
<div {{ $attributes->merge(['class' => "flex items-center gap-3 $class"]) }}>
    @if ($wide = \App\Support\Brand::wideUrl())
        <img src="{{ $wide }}" alt="{{ \App\Support\Brand::name() }}" class="h-10 w-auto max-w-[12rem] object-contain">
    @else
        <x-logo />
        <span class="text-xl font-extrabold tracking-tight text-ink-900 dark:text-white">{{ \App\Support\Brand::short() }}</span>
    @endif
</div>
