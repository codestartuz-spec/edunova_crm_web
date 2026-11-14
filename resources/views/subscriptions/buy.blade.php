@extends('layouts.app')
@section('title', 'Obuna: '.$branch->name)

@section('content')
    <x-page-header icon="wallet" :title="$branch->subscriptionExpired() ? 'Obuna sotib olish' : 'Obunani uzaytirish'" :subtitle="$branch->name.' filiali'">
        <x-slot:actions><a href="{{ route('subscriptions.index') }}" class="btn-secondary">Orqaga</a></x-slot:actions>
    </x-page-header>

    @if (! $configured)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Obuna narxi va karta raqami hali belgilanmagan. Administrator bilan bog'laning.</div>
    @elseif ($hasPending)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Bu filial uchun chek allaqachon yuborilgan. Administrator uni {{ \App\Models\SubscriptionRequest::REVIEW_TIME }} ichida ko'rib chiqadi, natija shu sahifada va «Obuna» bo'limida ko'rinadi.</div>
    @else
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card card-body">
                <h3 class="mb-3 font-semibold text-ink-900 dark:text-white">Obuna shartlari</h3>
                <div class="whitespace-pre-line text-sm text-ink-700 dark:text-ink-200">{{ $terms }}</div>
            </div>

            <form method="POST" action="{{ route('subscriptions.store', $branch) }}" enctype="multipart/form-data" class="card card-body space-y-5" x-data="{ plan: 'monthly' }">
                @csrf
                <x-once />
                <div>
                    <div class="label">Tarif</div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="cursor-pointer rounded-xl border p-4" :class="plan === 'monthly' ? 'border-brand-500 bg-brand-50 dark:bg-brand-950/30' : 'border-ink-200 dark:border-ink-700'">
                            <input type="radio" name="plan" value="monthly" x-model="plan" class="sr-only">
                            <div class="font-semibold text-ink-900 dark:text-white">Oylik</div>
                            <div class="text-sm text-ink-500">1 oy</div>
                            <div class="mt-1 text-lg font-bold">{{ \App\Support\Format::money($monthly) }}</div>
                        </label>
                        <label class="cursor-pointer rounded-xl border p-4" :class="plan === 'yearly' ? 'border-brand-500 bg-brand-50 dark:bg-brand-950/30' : 'border-ink-200 dark:border-ink-700'">
                            <input type="radio" name="plan" value="yearly" x-model="plan" class="sr-only">
                            <div class="font-semibold text-ink-900 dark:text-white">Yillik</div>
                            <div class="text-sm text-ink-500">12 oy</div>
                            <div class="mt-1 text-lg font-bold">{{ \App\Support\Format::money($yearly) }}</div>
                            @if ($monthly * 12 > $yearly)<div class="mt-1 text-xs font-medium text-emerald-600">{{ \App\Support\Format::money($monthly * 12 - $yearly) }} tejaysiz (−{{ (int) round(($monthly * 12 - $yearly) / ($monthly * 12) * 100) }}%)</div>@endif
                        </label>
                    </div>
                    @error('plan')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="receipt">To'lov cheki (rasm)</label>
                    <input id="receipt" type="file" name="receipt" accept="image/png,image/jpeg,image/webp" required class="block w-full text-sm">
                    <p class="hint">JPG, PNG yoki WEBP, 5 MB gacha. Summa va sana ko'rinib turishi kerak.</p>
                    @error('receipt')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="note">Izoh (ixtiyoriy)</label>
                    <input id="note" name="note" maxlength="500" value="{{ old('note') }}" class="input">
                </div>
                <div class="rounded-xl border border-ink-200 bg-ink-50 p-3 text-xs text-ink-600 dark:border-ink-700 dark:bg-ink-800/60 dark:text-ink-300">Chek yuborilgach administrator uni <b>{{ \App\Models\SubscriptionRequest::REVIEW_TIME }}</b> ichida ko'rib chiqadi. Tasdiqlangach obuna avtomatik faollashadi.</div>
                <div><button class="btn-primary">Chekni yuborish</button></div>
            </form>
        </div>

        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="card card-body space-y-2">
                <h3 class="font-semibold text-ink-900 dark:text-white">To'lov uchun karta</h3>
                <div class="rounded-xl bg-ink-50 p-4 dark:bg-ink-800/60">
                    <div class="text-xl font-bold tracking-wider text-ink-900 dark:text-white select-all">{{ $card }}</div>
                    @if ($holder)<div class="mt-1 text-sm text-ink-500">{{ $holder }}</div>@endif
                </div>
                <p class="text-xs text-ink-500">Tanlangan tarif summasini aynan shu kartaga o'tkazing, so'ng chekni yuklang.</p>
                @unless ($branch->subscription_exempt)
                    <p class="text-xs text-ink-500">
                        @if ($branch->subscriptionExpired())Obuna <b>{{ $branch->subscriptionLastDay()?->format('d.m.Y') }}</b> kuni tugagan. Yangi muddat to'lov tasdiqlangan kundan boshlanadi.
                        @else Hozirgi obuna <b>{{ $branch->subscriptionLastDay()?->format('d.m.Y') }}</b> kunigacha amal qiladi. Yangi muddat shundan keyin boshlanadi (qolgan kunlar yo'qolmaydi).
                        @endif</p>
                @endunless
            </div>
        </div>
    </div>
    @endif
@endsection
