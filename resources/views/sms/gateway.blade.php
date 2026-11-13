@extends('layouts.app')
@section('title', 'SMS shlyuzi')

@section('content')
    <x-page-header title="SMS shlyuzi" subtitle="USB modem (birinchi) → Eskiz (zaxira)">
        <x-slot:actions><a href="{{ route('sms.index') }}" class="btn-secondary">SMS bo'limiga</a></x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="card card-body"><div class="text-sm text-ink-500">Bugun modem orqali</div><div class="mt-1 text-2xl font-bold">{{ $used }}</div></div>
        <div class="card card-body"><div class="text-sm text-ink-500">Kunlik limit</div><div class="mt-1 text-sm font-medium">Har bir filial uchun «Filiallar» bo'limida, filial kartochkasining SMS qismida belgilanadi</div></div>
        <div class="card card-body"><div class="text-sm text-ink-500">Uzilish himoyasi</div><div class="mt-1"><span class="{{ $breaker ? 'badge-red' : 'badge-green' }}">{{ $breaker ? "Yoqilgan (5 daqiqa Eskiz)" : 'Faol emas' }}</span></div></div>
    </div>

    <div class="mb-6 rounded-xl border border-ink-200 bg-ink-50 p-4 text-sm text-ink-700 dark:border-ink-700 dark:bg-ink-800/50 dark:text-ink-200">
        Shlyuz hujjatidagi (<code>sms.elshod.date/admin/docs</code>) qiymatlarni shu yerga kiriting. Modem ishlamasa, filialning kunlik limiti (Toshkent sanasi bo'yicha) tugasa yoki 0 bo'lsa yoki ketma-ket 3 xato bo'lsa, xabar avtomatik Eskiz orqali yuboriladi. Eskiz sozlamalari o'zgarmaydi.
    </div>

    <form method="POST" action="{{ route('sms.gateway.update') }}" class="card card-body max-w-3xl space-y-5">
        @csrf @method('PUT')
        <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="enabled" value="1" class="checkbox" @checked(old('enabled', \App\Models\AppSetting::bool('sms.modem.enabled')))> USB modemni yoqish</label>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="base_url" label="Shlyuz manzili" :value="$v['base_url']" hint="Masalan: https://sms.elshod.date" class="sm:col-span-2" />
            <x-input name="path" label="Yuborish yo'li" :value="$v['path'] ?? '/api/send'" />
            <div><label class="label">Usul</label><select name="method" class="input"><option value="post" @selected(($v['method'] ?? 'post') === 'post')>POST</option><option value="get" @selected(($v['method'] ?? '') === 'get')>GET</option></select></div>
            <div><label class="label">So'rov tanasi</label><select name="body_format" class="input"><option value="json" @selected(($v['body_format'] ?? 'json') === 'json')>JSON</option><option value="form" @selected(($v['body_format'] ?? '') === 'form')>Form (x-www-form-urlencoded)</option></select></div>
            <div><label class="label">Telefon ko'rinishi</label><select name="phone_format" class="input"><option value="plus" @selected(($v['phone_format'] ?? 'plus') === 'plus')>+998901234567</option><option value="digits12" @selected(($v['phone_format'] ?? '') === 'digits12')>998901234567</option><option value="digits9" @selected(($v['phone_format'] ?? '') === 'digits9')>901234567</option></select></div>
            <x-input name="phone_field" label="Telefon maydoni nomi" :value="$v['phone_field'] ?? 'phone'" />
            <x-input name="message_field" label="Matn maydoni nomi" :value="$v['message_field'] ?? 'message'" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="label">Autentifikatsiya</label>
                <select name="auth_type" class="input">
                    @foreach (['none' => "Yo'q", 'bearer' => 'Bearer token', 'header' => 'Sarlavha (API kalit)', 'basic' => 'Login / parol (Basic)'] as $k => $l)
                        <option value="{{ $k }}" @selected(($v['auth_type'] ?? 'none') === $k)>{{ $l }}</option>
                    @endforeach
                </select></div>
            <x-input name="auth_header" label="Sarlavha nomi (API kalit uchun)" :value="$v['auth_header'] ?? 'X-API-Key'" />
            <x-input name="basic_user" label="Login (Basic uchun)" :value="$v['basic_user']" />
            <div><x-input name="auth_value" type="password" label="Token / kalit / parol" autocomplete="new-password" :hint="$hasSecret ? 'Saqlangan (bo\'sh qoldirsangiz o\'zgarmaydi)' : null" />
                @if ($hasSecret)<label class="mt-1 flex items-center gap-2 text-xs text-ink-500"><input type="checkbox" name="clear_secret" value="1" class="checkbox"> Saqlangan qiymatni o'chirish</label>@endif</div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-input name="extra" label="Qo'shimcha maydonlar (JSON)" :value="$v['extra']" hint='Masalan: {"sim":1}. Bo&apos;sh qoldirsa bo&apos;ladi.' class="sm:col-span-2" />
            <x-input name="success_field" label="Muvaffaqiyat maydoni (ixtiyoriy)" :value="$v['success_field']" hint="Javob JSON'ida, masalan: success" />
            <x-input name="success_value" label="Kutilgan qiymat" :value="$v['success_value'] ?? 'true'" />
            <x-input name="timeout" type="number" label="Kutish vaqti (soniya)" :value="$v['timeout'] ?? 15" min="3" max="60" />
        </div>
        <div><button class="btn-primary">Saqlash</button></div>
    </form>

    <form method="POST" action="{{ route('sms.gateway.test') }}" class="card card-body mt-6 max-w-3xl space-y-3">
        @csrf
        <h2 class="text-base font-semibold">Tekshirish</h2>
        <p class="text-sm text-ink-500">Avval sozlamalarni saqlang. Ko'rsatilgan raqamga sinov SMS yuboriladi va shlyuz javobi ko'rsatiladi (limitga hisoblanmaydi).</p>
        <div class="flex flex-wrap items-end gap-3"><x-input name="test_phone" label="Sinov raqami" phone class="w-56" /><button class="btn-secondary">Sinov SMS yuborish</button></div>
    </form>
@endsection
