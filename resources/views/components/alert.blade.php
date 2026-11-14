@if (session('success') || session('error'))
    @php
        $ok = (bool) session('success');
        $ms = $ok ? 6000 : 9000;
    @endphp
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, {{ $ms }})"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-6"
         role="{{ $ok ? 'status' : 'alert' }}"
         class="toast alert-anim flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium shadow-pop backdrop-blur-md {{ $ok ? 'border-emerald-200 bg-emerald-50/95 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/90 dark:text-emerald-200' : 'border-danger-200 bg-danger-50/95 text-danger-800 dark:border-danger-900 dark:bg-danger-950/90 dark:text-danger-200' }}">
        <x-icon :name="$ok ? 'check' : 'alert'" class="mt-0.5 h-5 w-5 shrink-0" colored :tone="$ok ? 'emerald' : 'rose'" />
        <div class="flex-1 break-words">{{ session('success') ?? session('error') }}</div>
        <button type="button" @click="show = false" class="opacity-60 transition hover:opacity-100" aria-label="Yopish"><x-icon name="x" class="h-4 w-4" /></button>
        <span class="toast-bar" style="animation-duration: {{ $ms }}ms"></span>
    </div>
@endif
