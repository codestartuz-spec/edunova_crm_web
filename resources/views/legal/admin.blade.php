@extends('layouts.app')
@section('title', 'Huquqiy hujjatlar')

@section('content')
    <x-page-header title="Huquqiy hujjatlar" subtitle="Maxfiylik siyosati, foydalanish shartlari va cookie siyosati. Foydalanuvchilar ularni qabul qilishi shart.">
        <x-slot:actions>
        <a href="{{ route('legal.journal') }}" class="btn-secondary btn-sm">Rozilik jurnali</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid gap-3 sm:grid-cols-3">
        @foreach ($types as $t => $label)
            <a href="{{ route('legal.admin', ['type' => $t]) }}" class="card card-body block transition hover:border-brand-300 {{ $type === $t ? 'ring-2 ring-brand-500' : '' }}">
                <div class="flex items-center justify-between gap-2">
                    <div class="text-sm font-semibold text-ink-900 dark:text-white">{{ $label }}</div>
                    <span class="badge-gray">v{{ $docs[$t]->version }}</span>
                </div>
                <div class="mt-2 flex items-center gap-3 text-xs text-ink-500">
                    <span class="text-emerald-600"><b>{{ $stats[$t]['accepted'] }}</b> qabul qilgan</span>
                    <span class="{{ $stats[$t]['pending'] ? 'text-amber-600' : '' }}"><b>{{ $stats[$t]['pending'] }}</b> kutilmoqda</span>
                </div>
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $users ? round($stats[$t]['accepted'] * 100 / $users) : 0 }}%"></div>
                </div>
            </a>
        @endforeach
    </div>

    <form method="POST" action="{{ route('legal.admin.update', $type) }}" class="card card-body space-y-4" x-data="{ bump: false }">
        @csrf @method('PUT')
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-semibold text-ink-900 dark:text-white">{{ $types[$type] }}</h2>
                <p class="text-xs text-ink-500">Joriy versiya: v{{ $doc->version }} · e'lon qilingan: {{ ($doc->published_at ?? $doc->created_at)->timezone('Asia/Tashkent')->format('d.m.Y H:i') }}</p>
            </div>
            <a href="{{ route('legal.show', $type) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">Foydalanuvchi ko'radigan ko'rinish ↗</a>
        </div>

        <x-input name="title" label="Sarlavha" :value="old('title', $doc->title)" required />

        <div>
            <label class="label" for="body">Matn <span class="text-brand-600">*</span></label>
            <textarea id="body" name="body" rows="22" required class="input font-mono text-[13px] leading-relaxed @error('body') input-error @enderror">{{ old('body', $doc->body) }}</textarea>
            @error('body')<p class="mt-1 text-xs text-brand-600">{{ $message }}</p>@enderror
            <p class="mt-1.5 text-xs text-ink-400">Format: <code>## Sarlavha</code> — bo'lim sarlavhasi; <code>- </code> bilan boshlangan qatorlar — ro'yxat; bo'sh qator bilan ajratilgan matn — alohida xatboshi. Matn yuridik mutaxassis tomonidan ko'rib chiqilishi tavsiya etiladi.</p>
        </div>

        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-100 p-3.5 text-sm dark:border-ink-800" :class="bump ? 'border-amber-300 bg-amber-50/60 dark:bg-amber-950/20' : ''">
            <input type="checkbox" name="new_version" value="1" x-model="bump" class="checkbox mt-0.5">
            <span>
                <b class="text-ink-900 dark:text-white">Yangi versiya sifatida e'lon qilish</b>
                <span class="mt-0.5 block text-xs text-ink-500">Belgilansa, <b>barcha foydalanuvchilar</b> keyingi kirishda ushbu hujjatni qayta qabul qilishi shart (v{{ $doc->version }} → v{{ $doc->version + 1 }}). Belgilanmasa, faqat matn tuzatiladi (imlo xatolari uchun).</span>
            </span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button class="btn-primary" x-text="bump ? 'Yangi versiyani e\'lon qilish' : 'Saqlash'">Saqlash</button>
        </div>
    </form>

    <form method="POST" action="{{ route('legal.admin.reset', $type) }}" class="mt-3 text-right" data-confirm="Matn standart (tayyor) variantga qaytariladi. Sizning o'zgartirishlaringiz o'chadi." data-confirm-title="Standart matnga qaytarilsinmi?" data-confirm-ok="Qaytarish">
        @csrf
        <button class="text-xs font-semibold text-ink-500 hover:text-brand-600">Standart matnga qaytarish</button>
    </form>
@endsection
