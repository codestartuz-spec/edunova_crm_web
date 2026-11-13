@extends('layouts.app')
@section('title', 'Brend')

@section('content')
    <x-page-header icon="sparkles" title="Brend" subtitle="Butun tizim uchun loyiha nomi va logotip. Faqat sAdmin o'zgartira oladi" />

    @unless ($gd)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">Serverda rasm bilan ishlash (GD) kengaytmasi yoqilmagan: nomni o'zgartirish mumkin, lekin logotip yuklab bo'lmaydi.</div>
    @endunless

    <form method="POST" action="{{ route('brand.update') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3"
          x-data="brandForm({ name: @js(old('name', \App\Support\Brand::name())), short: @js(old('short', \App\Support\Brand::short())), tagline: @js(old('tagline', \App\Support\Brand::tagline())), logo: @js(\App\Support\Brand::logoUrl()), wide: @js(\App\Support\Brand::wideUrl()) })">
        @csrf
        <div class="space-y-6 lg:col-span-2">
            <div class="card card-body space-y-5">
                <h3 class="font-semibold text-ink-900 dark:text-white">Nom va shior</h3>
                <div>
                    <label class="label" for="name">Loyiha nomi</label>
                    <input id="name" name="name" x-model="name" maxlength="40" required class="input @error('name') input-error @enderror">
                    @error('name')<p class="error-text">{{ $message }}</p>@enderror
                    <p class="hint">Sarlavhalarda, chek va shartnomada, SMS va Telegram xabarlarida ko'rinadi (2–40 belgi).</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="label" for="short">Qisqa nom</label>
                        <input id="short" name="short" x-model="short" maxlength="12" required class="input @error('short') input-error @enderror">
                        @error('short')<p class="error-text">{{ $message }}</p>@enderror
                        <p class="hint">Yon panel, kirish sahifasi va ilova ikonkasi tagida (2–12 belgi).</p>
                    </div>
                    <div>
                        <label class="label" for="tagline">Shior (ixtiyoriy)</label>
                        <input id="tagline" name="tagline" x-model="tagline" maxlength="120" class="input @error('tagline') input-error @enderror">
                        @error('tagline')<p class="error-text">{{ $message }}</p>@enderror
                        <p class="hint">Kirish sahifasida katta yozuv sifatida chiqadi.</p>
                    </div>
                </div>
            </div>

            <div class="card card-body space-y-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-ink-900 dark:text-white">Logotip</h3>
                        <p class="mt-0.5 text-xs text-ink-500">Faqat <b>PNG</b>, shaffof fon tavsiya etiladi, hajmi {{ \App\Support\Brand::MAX_KB }} KB gacha. O'lcham <b>aniq</b> bo'lishi shart - boshqa o'lchamdagi rasm qabul qilinmaydi.</p>
                    </div>
                </div>

                {{-- Asosiy (kvadrat) logotip --}}
                <div class="rounded-xl border border-ink-100 p-4 dark:border-ink-800">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl border border-dashed border-ink-300 bg-ink-50 dark:border-ink-700 dark:bg-ink-800/60">
                            <template x-if="logoPreview"><img :src="logoPreview" class="h-20 w-20 rounded-xl object-contain" alt=""></template>
                            <template x-if="!logoPreview"><span class="brand-chip flex h-16 w-16 items-center justify-center rounded-2xl text-2xl font-black" x-text="(short || 'E').charAt(0).toUpperCase()"></span></template>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-ink-900 dark:text-white">Asosiy logotip <span class="badge-blue ml-1">512 × 512 px</span></div>
                            <p class="mt-0.5 text-xs text-ink-500">Yon panel, ilova ikonkasi, brauzer belgisi va chekda ishlatiladi. Undan favicon va ilova ikonkalari avtomatik yasaladi.</p>
                            <input type="file" name="logo" accept="image/png" @change="pick($event, 'logo', 512, 512)" class="mt-2 block w-full text-sm text-ink-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100" @disabled(! $gd)>
                            <p x-show="errors.logo" x-cloak x-text="errors.logo" class="error-text mt-1.5"></p>
                            @error('logo')<p class="error-text mt-1.5">{{ $message }}</p>@enderror
                            @if (\App\Support\Brand::hasLogo())
                                <label class="mt-2 flex items-center gap-2 text-xs text-ink-600 dark:text-ink-300"><input type="checkbox" name="remove_logo" value="1" class="checkbox"> Logotipni olib tashlash (bosh harfli belgiga qaytish)</label>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Gorizontal logotip --}}
                <div class="rounded-xl border border-ink-100 p-4 dark:border-ink-800">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex h-24 w-48 shrink-0 items-center justify-center rounded-2xl border border-dashed border-ink-300 bg-ink-50 p-2 dark:border-ink-700 dark:bg-ink-800/60">
                            <template x-if="widePreview"><img :src="widePreview" class="max-h-20 w-auto max-w-full object-contain" alt=""></template>
                            <template x-if="!widePreview"><span class="text-xs text-ink-400">Yuklanmagan</span></template>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-ink-900 dark:text-white">Gorizontal logotip <span class="badge-blue ml-1">640 × 160 px</span></div>
                            <p class="mt-0.5 text-xs text-ink-500">Kirish va parolni tiklash sahifalarida (oq fonda) ko'rinadi. Ixtiyoriy: yuklanmasa, kvadrat logotip va qisqa nom chiqadi.</p>
                            <input type="file" name="logo_wide" accept="image/png" @change="pick($event, 'wide', 640, 160)" class="mt-2 block w-full text-sm text-ink-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100" @disabled(! $gd)>
                            <p x-show="errors.wide" x-cloak x-text="errors.wide" class="error-text mt-1.5"></p>
                            @error('logo_wide')<p class="error-text mt-1.5">{{ $message }}</p>@enderror
                            @if (\App\Support\Brand::hasWide())
                                <label class="mt-2 flex items-center gap-2 text-xs text-ink-600 dark:text-ink-300"><input type="checkbox" name="remove_logo_wide" value="1" class="checkbox"> Gorizontal logotipni olib tashlash</label>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <button class="btn-primary" :disabled="hasError" :class="hasError ? 'opacity-50 cursor-not-allowed' : ''"><x-icon name="check" class="h-4 w-4" /> Saqlash</button>
                <a href="{{ route('dashboard') }}" class="btn-secondary">Bekor qilish</a>
            </div>
        </div>

        {{-- Jonli ko'rinish --}}
        <div class="space-y-4 lg:sticky lg:top-24 lg:self-start">
            <div class="card overflow-hidden">
                <div class="border-b border-ink-100 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-ink-400 dark:border-ink-800">Yon panel ko'rinishi</div>
                <div class="flex items-center gap-3 p-5">
                    <template x-if="logoPreview"><img :src="logoPreview" class="h-10 w-10 rounded-xl object-contain" alt=""></template>
                    <template x-if="!logoPreview"><span class="brand-chip inline-flex h-10 w-10 items-center justify-center rounded-xl font-black" x-text="(short || 'E').charAt(0).toUpperCase()"></span></template>
                    <div class="leading-tight">
                        <div class="text-base font-extrabold tracking-tight text-ink-900 dark:text-white" x-text="short || '—'"></div>
                        <div class="text-[11px] font-medium uppercase tracking-widest text-ink-400">Filial nomi</div>
                    </div>
                </div>
            </div>
            <div class="card overflow-hidden">
                <div class="border-b border-ink-100 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-ink-400 dark:border-ink-800">Kirish sahifasi</div>
                <div class="login-hero p-6 text-white">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex h-9 w-9 items-center justify-center overflow-hidden rounded-xl bg-white text-lg font-black text-brand-600">
                            <template x-if="logoPreview"><img :src="logoPreview" class="h-full w-full object-contain" alt=""></template>
                            <template x-if="!logoPreview"><span x-text="(short || 'E').charAt(0).toUpperCase()"></span></template>
                        </span>
                        <span class="text-lg font-extrabold" x-text="short || '—'"></span>
                    </div>
                    <p class="mt-4 text-lg font-bold leading-snug" x-text="tagline || @js(\App\Support\Brand::DEFAULT_TAGLINE)"></p>
                </div>
                <div class="p-5 text-xs text-ink-500">Sarlavha: <b class="text-ink-800 dark:text-ink-100" x-text="'Kirish · ' + (name || '—')"></b></div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('brandForm', (init) => ({
                name: init.name, short: init.short, tagline: init.tagline,
                logoPreview: init.logo, widePreview: init.wide, errors: { logo: '', wide: '' },
                // Mantiqiy (true/false) qiymat bo'lishi shart: bo'sh matn ('') Alpine'da tugmani o'chirib qo'yadi
                get hasError() { return Boolean(this.errors.logo || this.errors.wide); },
                // Brauzerda oldindan tekshirish (server baribir qayta tekshiradi): aniq o'lcham va PNG
                pick(e, kind, w, h) {
                    const file = e.target.files[0];
                    this.errors[kind] = '';
                    if (!file) return;
                    const fail = (msg) => { this.errors[kind] = msg; e.target.value = ''; };
                    if (file.type !== 'image/png') return fail('Faqat PNG formatidagi rasm qabul qilinadi.');
                    if (file.size > {{ \App\Support\Brand::MAX_KB }} * 1024) return fail('Fayl hajmi {{ \App\Support\Brand::MAX_KB }} KB dan oshmasligi kerak.');
                    const url = URL.createObjectURL(file);
                    const img = new Image();
                    img.onload = () => {
                        if (img.naturalWidth !== w || img.naturalHeight !== h) {
                            URL.revokeObjectURL(url);
                            return fail('Rasm aniq ' + w + '×' + h + ' px bo\'lishi kerak. Siz tanlagan rasm: ' + img.naturalWidth + '×' + img.naturalHeight + ' px.');
                        }
                        if (kind === 'logo') this.logoPreview = url; else this.widePreview = url;
                    };
                    img.onerror = () => fail('Rasm faylini o\'qib bo\'lmadi.');
                    img.src = url;
                },
            }));
        });
    </script>
@endsection
