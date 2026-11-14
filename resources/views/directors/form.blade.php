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
        {{-- v26: Telegram va kunlik hisobot --}}
        @php
            $tgId = old('telegram_chat_id', $d?->telegram_chat_id);
            $repOn = old('report_enabled', $d ? ($d->report_enabled ?? true) : true);
            $repTime = old('report_time', $d?->report_time ?: '20:00');
        @endphp
        <div class="card card-body" x-data="tgCard({ id: {{ $d ? $d->id : 'null' }}, linked: {{ $d?->telegram_chat_id ? 'true' : 'false' }}, on: {{ $repOn ? 'true' : 'false' }}, time: '{{ $repTime }}',
            linkUrl: '{{ $d ? route('directors.telegram.link', $d->id) : '' }}', statusUrl: '{{ $d ? route('directors.telegram.status', $d->id) : '' }}', csrf: '{{ csrf_token() }}' })">
            <div class="mb-4 flex items-center gap-3">
                <span class="stat-icon !h-10 !w-10 !rounded-lg"><x-icon name="send" class="h-5 w-5" /></span>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-ink-900 dark:text-white">Telegram va kunlik hisobot</h3>
                    <p class="text-xs text-ink-500">Bot har kuni direktorga faqat o'ziga ruxsat berilgan filiallar bo'yicha statistikani yuboradi</p>
                </div>
                @if ($d)
                    <span x-show="linked" x-cloak class="badge-green">Ulangan</span>
                    <span x-show="!linked" class="badge-gray">Ulanmagan</span>
                @endif
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="telegram_chat_id" class="label">Telegram ID</label>
                    <input id="telegram_chat_id" name="telegram_chat_id" type="text" inputmode="numeric" autocomplete="off" maxlength="15" placeholder="masalan 123456789"
                           value="{{ $tgId }}" @input="linked = false" class="input {{ $errors->has('telegram_chat_id') ? 'input-error' : '' }}">
                    @error('telegram_chat_id')<p class="error-text">{{ $message }}</p>@enderror
                    <p class="hint">Direktor botga /start yozib, /id buyrug'i bilan ID'sini bilib oladi. Saqlashda bot unga tasdiq xabari yuboradi.</p>
                    @if ($d?->telegram_username)<p class="hint">Telegram: &#64;{{ $d->telegram_username }}</p>@endif
                </div>
                <div>
                    <label class="label">Hisobot yuborish vaqti</label>
                    <div class="flex items-center gap-2" :class="!on && 'opacity-50'">
                        <select class="input !w-24" :value="time.slice(0, 2)" @change="time = $event.target.value + ':' + time.slice(3)" :disabled="!on" aria-label="Soat">
                            @for ($h = 0; $h < 24; $h++)<option value="{{ sprintf('%02d', $h) }}">{{ sprintf('%02d', $h) }}</option>@endfor
                        </select>
                        <b>:</b>
                        <select class="input !w-24" :value="time.slice(3)" @change="time = time.slice(0, 2) + ':' + $event.target.value" :disabled="!on" aria-label="Daqiqa">
                            @for ($m = 0; $m < 60; $m += 5)<option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>@endfor
                        </select>
                    </div>
                    <input type="hidden" name="report_time" :value="time">
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach (['18:00', '20:00', '21:00', '22:00'] as $t)
                            <button type="button" class="badge-blue cursor-pointer" :class="time === '{{ $t }}' && 'ring-2 ring-brand-500'" @click="time = '{{ $t }}'">{{ $t }}</button>
                        @endforeach
                    </div>
                    @error('report_time')<p class="error-text">{{ $message }}</p>@enderror
                    <p class="hint">Toshkent vaqti bilan. Vaqt bugun o'tib ketgan bo'lsa, hisobot ertadan boshlanadi.</p>
                </div>
            </div>

            <label class="todo-row mt-5 flex cursor-pointer items-center gap-3 rounded-lg border border-ink-100 px-3 py-2.5 text-sm dark:border-ink-800">
                <input type="hidden" name="report_enabled" value="0">
                <input type="checkbox" name="report_enabled" value="1" x-model="on" class="checkbox">
                <span class="font-medium text-ink-900 dark:text-white">Kunlik hisobotni yuborish</span>
                <span class="hint ml-auto !mt-0">O'chirilsa, direktor hisobot olmaydi</span>
            </label>

            @if ($d)
                <div class="mt-5 rounded-xl border border-dashed border-brand-300 bg-brand-50/50 p-4 dark:border-brand-800 dark:bg-brand-950/20">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="min-w-0 flex-1">
                            <b class="block text-sm text-ink-900 dark:text-white">ID'ni yozmasdan ulash</b>
                            <span class="text-xs text-ink-500">Havolani direktorga yuboring: u bossa, bot o'zi bog'laydi (havola {{ \App\Services\Telegram\DirectorTelegram::LINK_TTL_MINUTES }} daqiqa amal qiladi, bir marta ishlaydi).</span>
                        </div>
                        <button type="button" class="btn-secondary btn-sm" @click="makeLink()" :disabled="busy"><span x-text="busy ? 'Yaratilmoqda…' : 'Havola yaratish'"></span></button>
                    </div>
                    <div x-show="url" x-cloak class="mt-3 flex flex-wrap items-center gap-2">
                        <input type="text" readonly :value="url" class="input !h-9 min-w-0 flex-1 text-xs" @focus="$event.target.select()">
                        <button type="button" class="btn-ghost btn-sm" @click="copy()"><span x-text="copied ? 'Nusxalandi ✓' : 'Nusxalash'"></span></button>
                        <a :href="url" target="_blank" rel="noopener" class="btn-ghost btn-sm">Ochish</a>
                    </div>
                    <p x-show="waiting" x-cloak class="mt-2 text-xs text-ink-500">Direktor havolani bosishini kutyapman…</p>
                    <p x-show="done" x-cloak class="mt-2 text-xs font-semibold text-emerald-600">✅ Ulandi! Sahifani yangilang yoki saqlang.</p>
                    <p x-show="err" x-cloak class="error-text mt-2" x-text="err"></p>
                </div>
                @if ($d->telegram_chat_id)
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="submit" formaction="{{ route('directors.telegram.test', $d->id) }}" formnovalidate class="btn-secondary btn-sm">Sinov hisobotini yuborish</button>
                        <button type="submit" formaction="{{ route('directors.telegram.unlink', $d->id) }}" formnovalidate class="btn-ghost btn-sm text-brand-600" onclick="return confirm('Direktorning Telegrami uzilsinmi? Hisobot kelmay qoladi.')">Telegramni uzish</button>
                    </div>
                @endif
            @else
                <p class="hint mt-4">Havola orqali ulash direktor saqlangandan keyin tahrirlash sahifasida paydo bo'ladi.</p>
            @endif
        </div>
        <div class="flex gap-2">
            <button class="btn-primary">Saqlash</button>
            <a href="{{ route('directors.index') }}" class="btn-secondary">Bekor qilish</a>
        </div>
    </form>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tgCard', (c) => ({
        ...c, url: '', busy: false, copied: false, waiting: false, done: false, err: '', timer: null,
        async makeLink() {
            this.busy = true; this.err = ''; this.done = false;
            try {
                const r = await fetch(this.linkUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' } });
                const j = await r.json();
                if (!r.ok) { this.err = j.message || 'Xatolik'; return; }
                this.url = j.url; this.waiting = true; this.poll(0);
            } catch (e) { this.err = 'Ulanib bo\'lmadi'; } finally { this.busy = false; }
        },
        poll(n) {
            clearTimeout(this.timer);
            if (n > 100) { this.waiting = false; return; }   // ~5 daqiqa
            this.timer = setTimeout(async () => {
                try {
                    const j = await (await fetch(this.statusUrl, { headers: { 'Accept': 'application/json' } })).json();
                    if (j.linked && j.chat_id) { this.linked = true; this.waiting = false; this.done = true; document.getElementById('telegram_chat_id').value = j.chat_id; return; }
                } catch (e) {}
                this.poll(n + 1);
            }, 3000);
        },
        async copy() { try { await navigator.clipboard.writeText(this.url); this.copied = true; setTimeout(() => this.copied = false, 2000); } catch (e) { this.err = 'Nusxalab bo\'lmadi - qo\'lda belgilang'; } },
    }));
});
</script>
@endpush
@endsection
