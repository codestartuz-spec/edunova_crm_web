@extends('layouts.app')
@section('title', 'Video darslar — sozlamalar')

@section('content')
    <x-page-header icon="play" title="Video darslar" subtitle="Tizim haqidagi video qo'llanmalar. Ko'rish hamma uchun, boshqarish faqat sAdmin uchun">
        <x-slot:actions>
            <a href="{{ route('tutorials.index') }}" class="btn-secondary btn-sm"><x-icon name="eye" colored class="h-4 w-4" /> Foydalanuvchi ko'rinishi</a>
            <a href="{{ route('settings.index') }}" class="btn-secondary btn-sm">← Sozlamalar</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 rounded-2xl border border-brand-100 bg-brand-50/60 p-4 text-sm leading-relaxed text-ink-600 dark:border-brand-900/40 dark:bg-brand-950/20 dark:text-ink-300">
        <div class="mb-1 flex items-center gap-2 font-bold text-brand-700 dark:text-brand-300"><x-icon name="info" colored class="h-4 w-4" /> Reklamasiz ko'rsatish uchun</div>
        YouTube'ga yuklashda videoni <b>«Ro'yxatga kiritilmagan» (Unlisted)</b> qiling va YouTube Studio'da <b>monetizatsiyani o'chiring</b> — shunda pleyerda reklama chiqmaydi.
        Tizim videoni maxfiy rejimli <code class="rounded bg-white px-1 dark:bg-ink-800">youtube-nocookie.com</code> pleyeri orqali, tavsiyalarsiz ko'rsatadi.
        Butunlay reklamasiz variant — videoni <b>.mp4 fayl</b> sifatida o'z serveringizga yuklab, shu faylning to'g'ridan-to'g'ri havolasini kiritish.
    </div>

    <form method="POST" action="{{ route('tutorials.store') }}" class="card card-body mb-6 space-y-4">
        @csrf
        <h2 class="text-base font-bold text-ink-900 dark:text-white">Yangi video dars</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label" for="n_title">Sarlavha <span class="text-danger-600">*</span></label>
                <input id="n_title" name="title" required maxlength="150" value="{{ old('_method') ? '' : old('title') }}" class="input @error('title') input-error @enderror" placeholder="Masalan: Yangi o'quvchi qo'shish">
                @error('title')<p class="error-text">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="n_url">Video havolasi <span class="text-danger-600">*</span></label>
                <input id="n_url" name="url" type="url" required maxlength="500" value="{{ old('_method') ? '' : old('url') }}" class="input @error('url') input-error @enderror" placeholder="https://youtu.be/… yoki .mp4 havola">
                @error('url')<p class="error-text">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="label" for="n_cat">Kategoriya</label>
                <input id="n_cat" name="category" list="tutorial-cats" maxlength="60" value="{{ old('_method') ? '' : old('category') }}" class="input" placeholder="Boshlash, To'lovlar, Davomad…">
            </div>
            <div>
                <label class="label" for="n_dur">Davomiyligi</label>
                <input id="n_dur" name="duration" maxlength="10" value="{{ old('_method') ? '' : old('duration') }}" class="input @error('duration') input-error @enderror" placeholder="5:30">
                @error('duration')<p class="error-text">{{ $message }}</p>@enderror
            </div>
            <label class="mt-6 flex items-center gap-2.5 text-sm font-medium text-ink-700 dark:text-ink-200"><input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-ink-300 text-brand-600"> Hammaga ko'rsatilsin</label>
        </div>
        <div>
            <label class="label" for="n_desc">Qisqa tavsif</label>
            <textarea id="n_desc" name="description" rows="2" maxlength="500" class="input">{{ old('_method') ? '' : old('description') }}</textarea>
        </div>
        <div><button class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Qo'shish</button></div>
    </form>
    <datalist id="tutorial-cats">@foreach ($categories as $c)<option value="{{ $c }}">@endforeach</datalist>

    <div class="card overflow-hidden">
        <div class="border-b border-ink-100 px-5 py-3.5 text-sm font-bold text-ink-800 dark:border-ink-800 dark:text-ink-100">Barcha videolar <span class="font-medium text-ink-400">· {{ $videos->count() }}</span></div>
        @forelse ($videos as $v)
            <div x-data="{ edit: {{ $errors->any() && old('_method') === 'PUT' && (int) old('_id') === $v->id ? 'true' : 'false' }} }" class="border-b border-ink-100 last:border-0 dark:border-ink-800">
                <div class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                    <div class="flex flex-col">
                        <form method="POST" action="{{ route('tutorials.move', $v) }}">@csrf<input type="hidden" name="dir" value="up"><button class="rounded p-0.5 text-ink-400 hover:bg-ink-100 hover:text-ink-700 disabled:opacity-30 dark:hover:bg-ink-800" @disabled($loop->first) title="Yuqoriga" aria-label="Yuqoriga"><x-icon name="chevron" class="h-4 w-4 rotate-180" /></button></form>
                        <form method="POST" action="{{ route('tutorials.move', $v) }}">@csrf<input type="hidden" name="dir" value="down"><button class="rounded p-0.5 text-ink-400 hover:bg-ink-100 hover:text-ink-700 disabled:opacity-30 dark:hover:bg-ink-800" @disabled($loop->last) title="Pastga" aria-label="Pastga"><x-icon name="chevron" class="h-4 w-4" /></button></form>
                    </div>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ink-100 text-xs font-bold tabular-nums text-ink-500 dark:bg-ink-800 dark:text-ink-300">{{ $loop->iteration }}</span>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('tutorials.show', $v) }}" class="block truncate font-semibold text-ink-900 hover:text-brand-700 dark:text-white">{{ $v->title }}</a>
                        <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-ink-400">
                            @if ($v->category)<span>{{ $v->category }}</span>@endif
                            @if ($v->duration)<span class="tabular-nums">{{ $v->duration }}</span>@endif
                            <span>{{ $v->embed['label'] }}</span>
                        </div>
                    </div>
                    @if ($v->is_active)<span class="badge-green">Faol</span>@else<span class="badge-gray">Yashirin</span>@endif
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="edit = !edit" class="btn-secondary btn-sm" :class="edit && 'ring-2 ring-brand-300'">Tahrirlash</button>
                        <form method="POST" action="{{ route('tutorials.toggle', $v) }}">@csrf<button class="btn-secondary btn-sm">{{ $v->is_active ? 'Yashirish' : "Ko'rsatish" }}</button></form>
                        <form method="POST" action="{{ route('tutorials.destroy', $v) }}" data-confirm="«{{ $v->title }}» videosi o'chiriladi." data-confirm-title="Videoni o'chirasizmi?" data-confirm-ok="O'chirish" data-confirm-tone="danger">@csrf @method('DELETE')<button class="btn-secondary btn-sm text-danger-600" title="O'chirish" aria-label="O'chirish"><x-icon name="trash" class="h-4 w-4" /></button></form>
                    </div>
                </div>
                <form x-show="edit" x-cloak x-transition method="POST" action="{{ route('tutorials.update', $v) }}" class="space-y-4 border-t border-ink-100 bg-ink-50/60 px-5 py-4 dark:border-ink-800 dark:bg-ink-900/40">
                    @csrf @method('PUT')
                    <input type="hidden" name="_id" value="{{ $v->id }}">
                    @php $mine = old('_method') === 'PUT' && (int) old('_id') === $v->id; @endphp
                    <div class="grid gap-4 md:grid-cols-2">
                        <div><label class="label">Sarlavha</label><input name="title" required maxlength="150" value="{{ $mine ? old('title') : $v->title }}" class="input"></div>
                        <div><label class="label">Video havolasi</label><input name="url" type="url" required maxlength="500" value="{{ $mine ? old('url') : $v->url }}" class="input @if ($mine) @error('url') input-error @enderror @endif">@if ($mine)@error('url')<p class="error-text">{{ $message }}</p>@enderror @endif</div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div><label class="label">Kategoriya</label><input name="category" list="tutorial-cats" maxlength="60" value="{{ $mine ? old('category') : $v->category }}" class="input"></div>
                        <div><label class="label">Davomiyligi</label><input name="duration" maxlength="10" value="{{ $mine ? old('duration') : $v->duration }}" class="input" placeholder="5:30"></div>
                        <label class="mt-6 flex items-center gap-2.5 text-sm font-medium text-ink-700 dark:text-ink-200"><input type="checkbox" name="is_active" value="1" @checked($mine ? old('is_active') : $v->is_active) class="h-4 w-4 rounded border-ink-300 text-brand-600"> Hammaga ko'rsatilsin</label>
                    </div>
                    <div><label class="label">Qisqa tavsif</label><textarea name="description" rows="2" maxlength="500" class="input">{{ $mine ? old('description') : $v->description }}</textarea></div>
                    <div class="flex gap-2"><button class="btn-primary btn-sm">Saqlash</button><button type="button" @click="edit = false" class="btn-secondary btn-sm">Bekor qilish</button></div>
                </form>
            </div>
        @empty
            <x-empty icon="play" title="Hozircha video yo'q" text="Yuqoridagi shakl orqali birinchi video darsni qo'shing." />
        @endforelse
    </div>
@endsection
