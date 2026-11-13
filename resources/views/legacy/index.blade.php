@extends('layouts.app')
@section('title', "Eski bazani ko'chirish")
@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-xl font-semibold">Eski bazani ko'chirish</h1>
        <p class="text-sm text-gray-500 mt-1">Eski tizimning (.sql yoki .sql.gz) zaxira faylini yuklang. Jadvallar tuzilmasi o'zgarmaydi — faqat ma'lumotlar ko'chiriladi. Telegram bot, SMS shlyuzi, huquqiy hujjatlar va sertifikat sozlamalari saqlanadi. SMS tarixi va navbati ko'chirilmaydi (eskilari tozalanadi). Rasm/video/audio fayllar ko'chirilmaydi.</p>
    </div>

    @if(session('success'))<div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('legacy.upload') }}" enctype="multipart/form-data" class="rounded-xl border bg-white p-5 space-y-3">
        @csrf
        <label class="block text-sm font-medium">Baza fayli</label>
        <input type="file" name="dump" accept=".sql,.gz" required class="block w-full text-sm">
        <p class="text-xs text-gray-500">Katta fayl bo'lsa, PHP <code>upload_max_filesize</code> va <code>post_max_size</code> sozlamalarini oshiring (yoki <code>php artisan app:import-legacy fayl.sql</code> ishlating).</p>
        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">Yuklash va tekshirish</button>
    </form>

    @if($report)
    <div class="rounded-xl border bg-white p-5 space-y-3">
        <h2 class="font-semibold">Oxirgi ko'chirish hisoboti <span class="text-xs text-gray-500 font-normal">({{ $report['finished_at'] }}, {{ $report['seconds'] }} s)</span></h2>
        <ul class="text-sm space-y-1">
            @foreach($report['checks'] as $c)
                <li class="{{ $c['ok'] ? 'text-green-700' : 'text-red-700' }}">{{ $c['ok'] ? '✔' : '✘' }} {{ $c['label'] }}: {{ $c['detail'] }}</li>
            @endforeach
        </ul>
        @if(!empty($report['directors']))
            <p class="text-sm">Direktorga aylantirilgan sAdminlar (umumiy direktor ruxsatlari berildi): {{ collect($report['directors'])->pluck('name')->implode(', ') }}</p>
        @endif
        <p class="text-sm text-gray-600">Ko'chirilmagan jadvallar: {{ implode(', ', $report['skipped_tables']) ?: '—' }}. Eski bazadagi ruxsatlar (hodim va direktorlar) ko'chirilmadi: {{ $report['dropped_permissions'] }} ta. Hodimlar ruxsatsiz, direktorlar umumiy shablon bilan.</p>
    </div>
    @endif
</div>
@endsection
