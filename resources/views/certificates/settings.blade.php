@extends('layouts.app')
@section('title', 'Sertifikat sozlamalari')

@section('content')
    <x-page-header title="Sertifikat sozlamalari" :subtitle="$branchScoped ? 'Tanlangan filial uchun (bo\'sh qoldirilsa umumiy sozlama ishlatiladi)' : 'Barcha filiallar uchun umumiy sozlama'">
        <x-slot:actions><a href="{{ route('certificates.index') }}" class="btn-secondary btn-sm">← Sertifikatlar</a></x-slot:actions>
    </x-page-header>

    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,26rem)_minmax(0,1fr)]">
        <form id="certForm" method="POST" action="{{ route('certificates.settings.save') }}" enctype="multipart/form-data" class="card card-body space-y-5">
            @csrf
            <x-input name="title" label="Sarlavha" :value="old('title', $v['title'])" required />
            <div>
                <label class="label" for="body">Matn (ism va kurs nomidan keyin chiqadi)</label>
                <textarea id="body" name="body" rows="3" maxlength="300" class="input" required>{{ old('body', $v['body']) }}</textarea>
                <p class="mt-1 text-right text-xs text-ink-400"><span id="bodyCount">0</span>/300</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                <x-input name="signer_name" label="Imzolovchi F.I.O" :value="old('signer_name', $v['signer_name'])" />
                <x-input name="signer_title" label="Lavozimi" :value="old('signer_title', $v['signer_title'])" />
            </div>
            <div class="space-y-4">
                <div>
                    <label class="label" for="signature">Imzo rasmi (PNG, shaffof fon yaxshi)</label>
                    <input id="signature" type="file" name="signature" accept="image/png,image/jpeg" class="input cursor-pointer file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/15 dark:file:text-brand-300">
                    @if ($signature)<p class="mt-1 text-xs text-ink-400">Hozirgi imzo ko'rinishda; yangisini tanlasangiz almashadi.</p>@endif
                </div>
                <div>
                    <label class="label" for="logo">Logo (bo'sh bo'lsa tizim logotipi)</label>
                    <input id="logo" type="file" name="logo" accept="image/png,image/jpeg" class="input cursor-pointer file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/15 dark:file:text-brand-300">
                    @if ($logo)<p class="mt-1 text-xs text-ink-400">Hozirgi logo ko'rinishda; yangisini tanlasangiz almashadi.</p>@endif
                </div>
            </div>
            @error('title')<p class="error-text">{{ $message }}</p>@enderror
            @error('body')<p class="error-text">{{ $message }}</p>@enderror
            @error('signature')<p class="error-text">{{ $message }}</p>@enderror
            @error('logo')<p class="error-text">{{ $message }}</p>@enderror
            <div class="flex items-center gap-3">
                <button class="btn-primary">Saqlash</button>
                <span id="certDirty" class="hidden text-xs font-medium text-accent-600">● Saqlanmagan o'zgarishlar bor</span>
            </div>
        </form>

        <div class="xl:sticky xl:top-24">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="flex items-center gap-2 text-sm font-semibold text-ink-700 dark:text-ink-200">
                    <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-70"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span></span>
                    Jonli ko'rinish
                </h2>
                <span class="text-xs text-ink-400">Namunaviy ma'lumotlar bilan</span>
            </div>
            <div id="certStage" class="card overflow-hidden !p-3 sm:!p-4" style="background:repeating-conic-gradient(rgb(128 128 150 / .06) 0% 25%, transparent 0% 50%) 50% / 20px 20px">
                <div id="certFit" class="relative w-full" style="aspect-ratio:842/595">
                    @include('certificates._preview')
                </div>
            </div>
            <p class="mt-2 text-xs text-ink-400">PDF'dagi haqiqiy sertifikat shu ko'rinishda chiqadi (shrift PDF'da biroz farq qilishi mumkin). Ism, kurs, sana va QR sertifikat berilganda to'ldiriladi.</p>
        </div>
    </div>
@endsection
