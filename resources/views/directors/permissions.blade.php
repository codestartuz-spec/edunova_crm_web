@extends('layouts.app')
@section('title', 'Direktorlar ruxsatlari')

@section('content')
    <x-page-header title="Direktorlar uchun umumiy ruxsatlar" subtitle="Bu ruxsatlar BARCHA direktorlarga birdek beriladi">
        <x-slot:actions>
            <a href="{{ route('directors.index') }}" class="btn-secondary">Orqaga</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-5 space-y-2 rounded-xl bg-ink-100 px-4 py-3 text-sm text-ink-600 dark:bg-ink-800 dark:text-ink-300">
        <p>Saqlaganingizda ruxsatlar hozirgi {{ $directors->count() }} ta direktorga ({{ $directors->pluck('name')->implode(', ') ?: '—' }}) va keyin qo'shiladigan barcha direktorlarga qo'llanadi.</p>
        <p>Direktorlar xodimlarga faqat O'ZLARIDA bor ruxsatlarni bera oladi. Bu yerdan olib tashlangan ruxsatni direktor xodimga bera olmaydi.</p>
        @unless ($configured)
            <p class="font-medium text-brand-600">Umumiy ruxsatlar hali saqlanmagan. Hozir belgilanganlar hozirgi direktorlarning hammasida mavjud bo'lgan ruxsatlar; saqlaganingizda hamma direktor shunga tenglashadi.</p>
        @endunless
    </div>

    <form method="POST" action="{{ route('directors.permissions.update') }}" x-data="{ all(v) { this.$root.querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = v) }, onlyView() { this.$root.querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = /\.(view|stats|history)$/.test(c.value)) } }">
        @csrf @method('PUT')

        <div class="mb-4 flex gap-2">
            <button type="button" class="btn-secondary btn-sm" @click="all(true)">Barchasini belgilash</button>
            <button type="button" class="btn-secondary btn-sm" @click="all(false)">Belgini olib tashlash</button>
            <button type="button" class="btn-secondary btn-sm" @click="onlyView()">Faqat ko'rish</button>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($groups as $group)
                <div class="card card-body">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400">{{ $group['label'] }}</h3>
                    <div class="space-y-2.5">
                        @foreach ($group['items'] as $key => $item)
                            <label class="flex cursor-pointer items-start gap-3 text-sm">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}" class="checkbox mt-0.5" @checked(in_array($key, $current, true))>
                                <span>{{ $item['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6"><button class="btn-primary">Saqlash va barcha direktorlarga qo'llash</button></div>
    </form>
@endsection
