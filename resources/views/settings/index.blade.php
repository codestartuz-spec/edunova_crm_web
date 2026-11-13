@extends('layouts.app')
@section('title', 'Sozlamalar')

@section('content')
    <x-page-header icon="cog" title="Sozlamalar" subtitle="Filial sozlamalari bo'limlar bo'yicha" />

    <div class="space-y-8">
        @foreach ($groups as $g)
            <section>
                <h2 class="mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-ink-500"><x-icon :name="$g['icon']" class="h-4 w-4" /> {{ $g['title'] }}</h2>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($g['items'] as $it)
                        <a href="{{ $it['url'] }}" class="card card-hover card-body">
                            <div class="font-semibold text-ink-900 dark:text-white">{{ $it['label'] }}</div>
                            <p class="mt-1 text-sm text-ink-500">{{ $it['desc'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
@endsection
