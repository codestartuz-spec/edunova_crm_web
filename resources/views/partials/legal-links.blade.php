{{-- Hujjatlarga havolalar (kirish, parol tiklash, ochiq sahifalar pastida) --}}
<nav class="{{ $class ?? 'mt-8' }} flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs text-ink-400" aria-label="Huquqiy hujjatlar">
    @foreach (\App\Services\LegalService::TYPES as $t => $label)
        <a href="{{ route('legal.show', $t) }}" class="hover:text-brand-600 hover:underline">{{ $label }}</a>
    @endforeach
</nav>
