@extends('layouts.app')
@section('title', "Ko'chirishni tasdiqlash")
@section('content')
<div class="max-w-3xl space-y-6">
    <h1 class="text-xl font-semibold">Fayl tekshiruvi</h1>
    @if(session('error'))<div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>@endif
    @foreach($info['errors'] as $e)<div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ $e }}</div>@endforeach
    @foreach($info['warnings'] as $w)<div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm">{{ $w }}</div>@endforeach

    <div class="rounded-xl border bg-white p-5 grid grid-cols-2 gap-x-6 gap-y-1 text-sm">
        <div>O'quvchilar: <b>{{ number_format($info['totals']['students'], 0, '', ' ') }}</b></div>
        <div>Guruhlar: <b>{{ $info['totals']['groups'] }}</b></div>
        <div>To'lovlar: <b>{{ $info['totals']['payments_count'] }}</b> ({{ number_format($info['totals']['payments_sum'], 0, '', ' ') }} so'm)</div>
        <div>Guruh a'zoliklari: <b>{{ $info['totals']['group_students'] }}</b></div>
        <div>Kassa yig'indisi: <b>{{ number_format($info['totals']['wallets_sum'], 0, '', ' ') }}</b></div>
        <div>Maosh to'lovlari: <b>{{ number_format($info['totals']['payouts_sum'], 0, '', ' ') }}</b></div>
    </div>

    <details class="rounded-xl border bg-white p-5 text-sm">
        <summary class="cursor-pointer font-medium">Jadvallar ({{ count($info['tables']) }})</summary>
        <div class="grid grid-cols-2 gap-x-6 mt-3">
            @foreach($info['tables'] as $t => $n)<div class="flex justify-between"><span>{{ $t }}</span><span class="text-gray-500">{{ $n }}</span></div>@endforeach
        </div>
        @if($info['skipped_tables'])<p class="mt-3 text-xs text-gray-500">Ko'chirilmaydi: {{ implode(', ', $info['skipped_tables']) }}</p>@endif
    </details>

    @if(!$info['errors'])
    <form method="POST" action="{{ route('legacy.run') }}" class="rounded-xl border border-red-200 bg-white p-5 space-y-4">
        @csrf
        <div class="text-sm text-red-700 font-medium">Diqqat: hozirgi tizimdagi barcha ma'lumotlar (sAdmin hisobingiz va sozlamalardan tashqari) o'chiriladi va fayldagi ma'lumotlar bilan almashtiriladi.</div>
        @if($info['sadmins'])
        <div>
            <label class="block text-sm font-medium mb-1">Fayldagi qaysi sAdmin sizning hisobingiz bo'ladi?</label>
            <select name="primary" class="border rounded-lg px-3 py-2 text-sm w-full">
                @foreach($info['sadmins'] as $s)<option value="{{ $s['id'] }}" @selected($loop->first)>{{ $s['name'] }} ({{ $s['username'] }})</option>@endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Qolgan sAdminlar direktorga aylantiriladi va ularga «Direktorlar → Umumiy ruxsatlar» shabloni qo'llanadi. Hodimlarning ruxsatlari ko'chirilmaydi (ruxsatsiz qoladi). Hozirgi login va parolingiz o'zgarmaydi.</p>
        </div>
        @endif
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="backup" value="1" checked> Avval hozirgi bazaning zaxirasini olish</label>
        <div class="grid sm:grid-cols-2 gap-3">
            <div><label class="block text-sm font-medium mb-1">Parolingiz</label><input type="password" name="password" required class="border rounded-lg px-3 py-2 text-sm w-full"></div>
            <div><label class="block text-sm font-medium mb-1">Tasdiq: TASDIQLAYMAN deb yozing</label><input name="confirm" required autocomplete="off" class="border rounded-lg px-3 py-2 text-sm w-full"></div>
        </div>
        <button class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm">Ko'chirishni boshlash</button>
    </form>
    @endif
    <a href="{{ route('legacy.index') }}" class="text-sm text-indigo-600">← Orqaga</a>
</div>
@endsection
