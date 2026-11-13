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
