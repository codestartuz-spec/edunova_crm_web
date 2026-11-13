{{-- v14: Sozlamalar - chap guruhlangan menyu + o'ng tomonda tarkib. Yopilishi: partials.settings-close --}}
<div class="lg:grid lg:grid-cols-[15rem_minmax(0,1fr)] lg:gap-6">
    <aside class="mb-6 lg:mb-0">
        <nav class="card card-body space-y-4 lg:sticky lg:top-4" aria-label="Sozlamalar menyusi">
            <a href="{{ route('settings.index') }}" class="flex items-center gap-2 text-sm font-semibold text-ink-500 hover:text-brand-600"><x-icon name="cog" class="h-4 w-4" /> Barcha sozlamalar</a>
            @foreach (\App\Support\SettingsMenu::groups() as $g)
                <div>
                    <div class="mb-1 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-ink-400"><x-icon :name="$g['icon']" class="h-4 w-4" />{{ $g['title'] }}</div>
                    <div class="space-y-0.5">
                        @foreach ($g['items'] as $it)
                            <a href="{{ $it['url'] }}" class="block rounded-lg px-3 py-1.5 text-sm font-medium transition {{ $it['active'] ? 'bg-brand-50 text-brand-700 dark:bg-brand-900/30 dark:text-brand-300' : 'text-ink-600 hover:bg-ink-100 dark:text-ink-300 dark:hover:bg-ink-800' }}">{{ $it['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>
    <div class="min-w-0">
