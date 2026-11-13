@extends('layouts.app')
@php $d = $director ?? null; @endphp
@section('title', $d ? 'Direktorni tahrirlash' : "Direktor qo'shish")

@section('content')
    <x-page-header :title="$d ? 'Direktorni tahrirlash' : 'Yangi direktor'" :subtitle="$d ? $d->name : 'Qaysi filiallarni ko\'rishini shu yerda belgilaysiz; amallar umumiy ruxsatlar sahifasida'" />

    <form method="POST" action="{{ $d ? route('directors.update', $d->id) : route('directors.store') }}" class="max-w-3xl space-y-6">
        @csrf
        @if ($d) @method('PUT') @endif
        <div class="card card-body grid gap-5 sm:grid-cols-2">
            <x-input name="name" label="F.I.O" :value="$d?->name" required class="sm:col-span-2" />
            <x-input name="username" label="Login" :value="$d?->username" required hint="Tizimga kirish uchun." />
            <x-input name="phone" phone label="Telefon" :value="$d?->phone" required />
            <x-input name="email" type="email" label="Email" :value="$d?->email" />
            <div></div>
            <x-input name="password" type="password" :label="$d ? 'Yangi parol (ixtiyoriy)' : 'Parol'" :required="! $d" autocomplete="new-password" :hint="$d ? 'Bo\'sh qoldirilsa, parol o\'zgarmaydi. O\'zgarsa, direktor barcha qurilmalardan chiqariladi.' : 'Kamida 8 ta belgi.'" />
            <x-input name="password_confirmation" type="password" label="Parolni takrorlang" autocomplete="new-password" />
        </div>
        @php
            $mode = old('branch_mode', ($d && ! ($d->all_branches ?? true)) ? 'selected' : 'all');
            $picked = collect(old('branch_ids', $selectedBranchIds ?? []))->map(fn ($v) => (int) $v)->all();
        @endphp
        <div class="card card-body" x-data="{ mode: '{{ $mode }}', picked: {{ json_encode($picked) }}, total: {{ $branchList->count() }},
            toggleAll(on) { this.picked = on ? {{ json_encode($branchList->pluck('id')->all()) }} : []; } }">
            <div class="mb-4 flex items-center gap-3">
                <span class="stat-icon !h-10 !w-10 !rounded-lg"><x-icon name="building" class="h-5 w-5" /></span>
                <div>
                    <h3 class="font-semibold text-ink-900 dark:text-white">Filial ruxsati</h3>
                    <p class="text-xs text-ink-500">Direktor qaysi filiallarni ko'ra va boshqara oladi</p>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="card-hover flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition" :class="mode === 'all' ? 'border-brand-500 bg-brand-50/60 ring-1 ring-brand-500 dark:bg-brand-950/30' : 'border-ink-200 dark:border-ink-700'">
                    <input type="radio" name="branch_mode" value="all" x-model="mode" class="mt-1">
                    <span><b class="block text-sm text-ink-900 dark:text-white">Barcha filiallar</b><span class="text-xs text-ink-500">Hozirgi va keyin ochiladigan barcha filiallar.</span></span>
                </label>
                <label class="card-hover flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition" :class="mode === 'selected' ? 'border-brand-500 bg-brand-50/60 ring-1 ring-brand-500 dark:bg-brand-950/30' : 'border-ink-200 dark:border-ink-700'">
                    <input type="radio" name="branch_mode" value="selected" x-model="mode" class="mt-1">
                    <span><b class="block text-sm text-ink-900 dark:text-white">Tanlangan filiallar</b><span class="text-xs text-ink-500">Faqat belgilangan bitta yoki bir nechta filial.</span></span>
                </label>
            </div>
            <div x-show="mode === 'selected'" x-transition x-cloak class="mt-4">
                <div class="mb-2 flex items-center justify-between text-xs text-ink-500">
                    <span>Tanlandi: <b class="text-ink-800 dark:text-ink-100" x-text="picked.length + ' / ' + total"></b></span>
                    <span class="flex gap-3"><button type="button" class="font-semibold text-brand-600" @click="toggleAll(true)">Hammasini belgilash</button><button type="button" class="font-semibold text-ink-500" @click="toggleAll(false)">Tozalash</button></span>
                </div>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($branchList as $b)
                        <label class="todo-row flex cursor-pointer items-center gap-3 rounded-lg border border-ink-100 px-3 py-2.5 text-sm dark:border-ink-800">
                            <input type="checkbox" name="branch_ids[]" value="{{ $b->id }}" x-model.number="picked" class="checkbox">
                            <span class="font-medium text-ink-900 dark:text-white">{{ $b->name }}</span>
                            @if (! $b->isActive())<span class="badge-gray ml-auto">Arxiv</span>@endif
                        </label>
                    @endforeach
                </div>
                @error('branch_ids')<p class="error-text mt-2">{{ $message }}</p>@enderror
                <p class="hint mt-3">Yangi ochiladigan filial cheklangan direktorga avtomatik ko'rinmaydi - uni shu yerdan qo'shasiz.</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="btn-primary">Saqlash</button>
            <a href="{{ route('directors.index') }}" class="btn-secondary">Bekor qilish</a>
        </div>
    </form>
@endsection
