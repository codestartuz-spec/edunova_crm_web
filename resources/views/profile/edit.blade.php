@extends('layouts.app')
@section('title', 'Profil')

@section('content')
    <x-page-header title="Profil" :subtitle="$user->role->label().($user->branch ? ' · '.$user->branch->name : '')" />

    <div class="grid max-w-5xl gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('profile.update') }}" class="card card-body space-y-5">
            @csrf @method('PUT')
            <h2 class="text-base font-semibold text-ink-900 dark:text-white">Shaxsiy ma'lumotlar</h2>
            <x-input name="name" label="F.I.O" :value="$user->name" required />
            <x-input name="phone" phone label="Telefon" :value="$user->phone" :required="! $user->isGlobal()" />
            <x-input name="email" type="email" label="Email" :value="$user->email" />
            <div><button class="btn-primary">Saqlash</button></div>
        </form>

        <form method="POST" action="{{ route('profile.password') }}" class="card card-body space-y-5">
            @csrf @method('PUT')
            <h2 class="text-base font-semibold text-ink-900 dark:text-white">Parolni o'zgartirish</h2>
            <x-input name="current_password" type="password" label="Joriy parol" required autocomplete="current-password" />
            <x-input name="password" type="password" label="Yangi parol" required autocomplete="new-password" hint="Kamida 8 ta belgi." />
            <x-input name="password_confirmation" type="password" label="Yangi parolni takrorlang" required autocomplete="new-password" />
            <div><button class="btn-primary">Parolni yangilash</button></div>
        </form>
    </div>

    {{-- v28: animatsiya darajasi (faqat shu qurilma/brauzer uchun saqlanadi) --}}
    <div class="card card-body mt-6 max-w-5xl space-y-4" x-data="{ fx: (window.eduFx ? window.eduFx.get() : 'full'), pick(v) { this.fx = v; if (window.eduFx) window.eduFx.set(v); } }">
        <div>
            <h2 class="text-base font-semibold text-ink-900 dark:text-white">Animatsiya va 3D effektlar</h2>
            <p class="hint">Orqa fondagi harakatlar va kartalarning 3D qiyshayishi. Tanlov faqat shu qurilma va brauzer uchun saqlanadi. Telefon yoki kompyuter sekin ishlasa, «Yengil» yoki «O'chiq» ni tanlang.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3" role="radiogroup" aria-label="Animatsiya darajasi">
            @foreach ([
                ['full', 'To\'liq', 'Doimiy harakatdagi fon, parallaks va 3D qiyshayish.', 'sparkles'],
                ['lite', 'Yengil', 'Fon sekin harakatlanadi, parallaks va og\'ir effektlarsiz. Zaif qurilmalar uchun.', 'pulse'],
                ['off', 'O\'chiq', 'Hamma animatsiya o\'chadi, 3D faqat chuqurlik va soyada qoladi.', 'moon'],
            ] as [$val, $title, $desc, $icon])
                <button type="button" role="radio" :aria-checked="fx === '{{ $val }}'" @click="pick('{{ $val }}')"
                        class="rounded-2xl border p-4 text-left transition duration-200 hover:-translate-y-0.5"
                        :class="fx === '{{ $val }}' ? 'border-brand-500 bg-brand-50 ring-2 ring-brand-500/25 dark:bg-brand-950/40' : 'border-ink-200 dark:border-ink-700'">
                    <span class="flex items-center gap-2 font-semibold text-ink-900 dark:text-white"><x-icon name="{{ $icon }}" colored class="h-5 w-5" /> {{ $title }}
                        <span x-show="fx === '{{ $val }}'" x-cloak class="ml-auto"><x-icon name="check" class="h-4 w-4 text-brand-600" /></span></span>
                    <span class="mt-1.5 block text-xs text-ink-500 dark:text-ink-400">{{ $desc }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="card card-body mt-6 max-w-5xl space-y-4">
        <h2 class="text-base font-semibold text-ink-900 dark:text-white">Faol qurilmalar</h2>
        @if (! $devicesAvailable)
            <p class="text-sm text-ink-500">Qurilmalar ro'yxati hozircha yoqilmagan (SESSION_DRIVER=database kerak).</p>
        @else
            <ul class="divide-y divide-ink-100 dark:divide-white/10">
                @foreach ($devices as $d)
                    <li class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-ink-900 dark:text-white">
                                {{ $d['device'] }} · {{ $d['browser'] }} / {{ $d['os'] }}
                                @if ($d['current'])<span class="ml-2 rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700">Joriy qurilma</span>@endif
                            </div>
                            <div class="text-xs text-ink-500">IP: {{ $d['ip'] ?? '—' }} · Oxirgi faollik: {{ $d['last_active']->diffForHumans() }}</div>
                        </div>
                        @unless ($d['current'])
                            <form method="POST" action="{{ route('profile.sessions.revoke', $d['id']) }}">
                                @csrf @method('DELETE')
                                <button class="btn-secondary text-sm">Chiqarish</button>
                            </form>
                        @endunless
                    </li>
                @endforeach
            </ul>
            @if (count($devices) > 1)
                <form method="POST" action="{{ route('profile.sessions.others') }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="w-full sm:w-64"><x-input name="password" type="password" label="Parolni tasdiqlang" required autocomplete="current-password" /></div>
                    <button class="btn-primary">Boshqa barcha qurilmalardan chiqish</button>
                </form>
            @endif
        @endif
    </div>
@endsection
