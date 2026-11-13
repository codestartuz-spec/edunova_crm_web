@extends('layouts.app')
@section('title', 'Obuna sozlamalari')

@section('content')
    <x-page-header icon="wallet" title="Obuna sozlamalari" subtitle="Narxlar barcha filiallar uchun bir xil. Faqat sAdmin o'zgartiradi">
        <x-slot:actions><a href="{{ route('subscriptions.index') }}" class="btn-secondary">Orqaga</a></x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('subscriptions.settings.save') }}" class="card card-body max-w-2xl space-y-5">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="price_monthly">Oylik narx (so'm)</label>
                <input id="price_monthly" name="price_monthly" inputmode="numeric" required value="{{ old('price_monthly', $v['price_monthly']) }}" class="input @error('price_monthly') input-error @enderror">
                @error('price_monthly')<p class="error-text">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="price_yearly">Yillik narx (so'm)</label>
                <input id="price_yearly" name="price_yearly" inputmode="numeric" required value="{{ old('price_yearly', $v['price_yearly']) }}" class="input @error('price_yearly') input-error @enderror">
                @error('price_yearly')<p class="error-text">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="card_number">Karta raqami</label>
                <input id="card_number" name="card_number" required maxlength="23" value="{{ old('card_number', $v['card_number']) }}" placeholder="8600 0000 0000 0000" class="input @error('card_number') input-error @enderror">
                @error('card_number')<p class="error-text">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="card_holder">Karta egasi (ixtiyoriy)</label>
                <input id="card_holder" name="card_holder" maxlength="100" value="{{ old('card_holder', $v['card_holder']) }}" class="input">
            </div>
        </div>
        <div>
            <label class="label" for="trial_days">Yangi filial uchun bepul sinov (kun)</label>
            <input id="trial_days" name="trial_days" inputmode="numeric" required value="{{ old('trial_days', $v['trial_days'] ?? 30) }}" class="input w-40">
            <p class="hint">Yangi ochilgan filial shuncha kun bepul ishlaydi.</p>
        </div>
        <div>
            <label class="label" for="terms">Obuna shartlari</label>
            <textarea id="terms" name="terms" rows="8" maxlength="3000" class="input">{{ old('terms', $v['terms'] ?: $defaultTerms) }}</textarea>
            <p class="hint">Direktor sotib olish sahifasida shu matnni ko'radi.</p>
        </div>
        <div><button class="btn-primary">Saqlash</button></div>
    </form>
@endsection
