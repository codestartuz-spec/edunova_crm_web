@extends('layouts.app')
@section('title', 'Sertifikatlar')

@section('content')
    <x-page-header title="Sertifikatlar" subtitle="Berilgan sertifikatlar, bekor qilish va qayta berish">
        <x-slot:actions>
            @can('certificates.manage')<a href="{{ route('certificates.settings') }}" class="btn-secondary btn-sm">Sozlamalar</a>@endcan
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="card card-body mb-4 grid gap-3 sm:grid-cols-[1fr_auto_auto] sm:items-end">
        <div><label class="label">Qidirish</label><input type="search" name="q" value="{{ $q }}" class="input" placeholder="Ism, raqam yoki kurs"></div>
        <div>
            <label class="label">Holati</label>
            <select name="status" class="input">
                <option value="">Barchasi</option>
                <option value="issued" @selected($status === 'issued')>Amalda</option>
                <option value="revoked" @selected($status === 'revoked')>Bekor qilingan</option>
            </select>
        </div>
        <button class="btn-secondary justify-center">Ko'rsatish</button>
    </form>

    <div class="space-y-3">
        @forelse ($certs as $c)
            <div class="card card-body flex flex-wrap items-center justify-between gap-3 text-sm" x-data="{ revoking: false }">
                <div class="min-w-0">
                    <div class="font-semibold text-ink-900 dark:text-white">{{ $c->student_name }}
                        <span class="{{ $c->isRevoked() ? 'badge-red' : 'badge-green' }} ml-2">{{ $c->isRevoked() ? 'Bekor qilingan' : 'Amalda' }}</span>
                    </div>
                    <div class="text-xs text-ink-500">{{ $c->number }} · {{ $c->course_name }}@if($c->level_name) / {{ $c->level_name }}@endif · {{ $c->issued_on->format('d.m.Y') }}@if($c->branch) · {{ $c->branch->name }}@endif</div>
                    @if ($c->isRevoked())<div class="text-xs text-red-600">Sabab: {{ $c->revoke_reason }}</div>@endif
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @unless ($c->isRevoked())
                        <a href="{{ route('certificates.pdf', $c) }}" target="_blank" class="btn-secondary btn-sm">PDF</a>
                        @can('certificates.manage')
                            <form method="POST" action="{{ route('certificates.reissue', $c) }}" data-confirm="Eskisi bekor qilinib, yangi raqam bilan beriladi." data-confirm-ok="Qayta berish">@csrf<button class="btn-ghost btn-sm">Qayta berish</button></form>
                            <button type="button" class="btn-ghost btn-sm text-red-600" @click="revoking = !revoking">Bekor qilish</button>
                        @endcan
                    @endunless
                </div>
                @can('certificates.manage')
                    <form x-show="revoking" x-cloak method="POST" action="{{ route('certificates.revoke', $c) }}" class="flex w-full flex-wrap gap-2">
                        @csrf
                        <input name="reason" required maxlength="200" class="input flex-1" placeholder="Bekor qilish sababi">
                        <button class="btn-primary">Tasdiqlash</button>
                    </form>
                @endcan
            </div>
        @empty
            <div class="card card-body text-center text-sm text-ink-400">Hali sertifikat berilmagan. Guruh yoki o'quvchi sahifasidan bering.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $certs->links() }}</div>
@endsection
