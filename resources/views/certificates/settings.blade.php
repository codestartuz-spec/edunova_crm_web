@extends('layouts.app')
@section('title', 'Sertifikat sozlamalari')

@section('content')
    <x-page-header title="Sertifikat sozlamalari" :subtitle="$branchScoped ? 'Tanlangan filial uchun (bo\'sh qoldirilsa umumiy sozlama ishlatiladi)' : 'Barcha filiallar uchun umumiy sozlama'">
        <x-slot:actions><a href="{{ route('certificates.index') }}" class="btn-secondary btn-sm">← Sertifikatlar</a></x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('certificates.settings.save') }}" enctype="multipart/form-data" class="card card-body max-w-2xl space-y-5">
        @csrf
        <x-input name="title" label="Sarlavha" :value="old('title', $v['title'])" required />
        <div>
            <label class="label">Matn (ism va kurs nomidan keyin chiqadi)</label>
            <textarea name="body" rows="3" maxlength="300" class="input" required>{{ old('body', $v['body']) }}</textarea>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="signer_name" label="Imzolovchi F.I.O" :value="old('signer_name', $v['signer_name'])" />
            <x-input name="signer_title" label="Lavozimi" :value="old('signer_title', $v['signer_title'])" />
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Imzo rasmi (PNG, shaffof fon yaxshi)</label>
                <input type="file" name="signature" accept="image/png,image/jpeg" class="input">
                @if ($signature)<img src="{{ $signature }}" class="mt-2 h-14" alt="">@endif
            </div>
            <div>
                <label class="label">Logo (bo'sh bo'lsa tizim logotipi)</label>
                <input type="file" name="logo" accept="image/png,image/jpeg" class="input">
                @if ($logo)<img src="{{ $logo }}" class="mt-2 h-14" alt="">@endif
            </div>
        </div>
        @error('signature')<p class="error-text">{{ $message }}</p>@enderror
        @error('logo')<p class="error-text">{{ $message }}</p>@enderror
        <div><button class="btn-primary">Saqlash</button></div>
    </form>
@endsection
