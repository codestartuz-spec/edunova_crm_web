@extends('layouts.app')
@section('title', 'Bildirishnomalar')

@section('content')
    <x-page-header title="Bildirishnomalar" subtitle="Hodimlarga: qo'ng'iroqcha, toast, brauzer va Web Push orqali xabar yuborish" />

    @unless ($pushAvailable)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">Web Push hozir o'chiq (<code>composer require minishlink/web-push</code> bajarilmagan). Qo'ng'iroqcha va brauzer bildirishnomasi ishlaydi.</div>
    @endunless

    <form method="POST" action="{{ route('notifications.store') }}" class="card card-body mb-6 grid gap-5 sm:grid-cols-2" x-data="{ all: {{ old('all_branches', true) ? 'true' : 'false' }} }">
        @csrf
        <x-input name="title" label="Sarlavha" maxlength="150" required />
        <x-input name="url" label="Havola (ixtiyoriy)" hint="Masalan: /groups yoki https://..." />
        <div class="sm:col-span-2">
            <label class="label" for="body">Matn</label>
            <textarea id="body" name="body" rows="3" maxlength="1000" required class="input">{{ old('body') }}</textarea>
            @error('body')<p class="error-text">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="label">Rollar</div>
            <div class="flex flex-wrap gap-3">
                @foreach ($roles as $key => $label)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="roles[]" value="{{ $key }}" class="checkbox" @checked(in_array($key, old('roles', [])))> {{ $label }}</label>
                @endforeach
            </div>
            @error('roles')<p class="error-text">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="label">Filiallar</div>
            <label class="mb-2 flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="all_branches" value="1" class="checkbox" x-model="all"> Barcha filiallar</label>
            <div x-show="!all" x-cloak class="flex flex-wrap gap-3">
                @foreach ($branches as $b)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="branch_ids[]" value="{{ $b->id }}" class="checkbox" @checked(in_array($b->id, old('branch_ids', [])))> {{ $b->name }}</label>
                @endforeach
            </div>
            @error('branch_ids')<p class="error-text">{{ $message }}</p>@enderror
            <p class="hint">Direktorlar filialga bog'lanmagan — roli tanlansa filialdan qat'i nazar oladi.</p>
        </div>

        <label class="flex items-center gap-2 text-sm font-medium sm:col-span-2"><input type="checkbox" name="is_important" value="1" class="checkbox" @checked(old('is_important'))> Muhim (toast darhol chiqadi va yopilmaguncha turadi)</label>
        <div class="sm:col-span-2"><button class="btn-primary">Yuborish</button></div>
    </form>

    <div class="card overflow-hidden"><div class="table-wrap"><table class="table">
        <thead><tr><th>Sarlavha</th><th>Kimga</th><th>Qabul qiluvchi</th><th>O'qilgan</th><th>Yuborgan</th><th>Vaqt</th></tr></thead>
        <tbody>
        @forelse ($history as $n)
            <tr data-href="{{ route('notifications.show', $n) }}">
                <td class="font-medium text-ink-900 dark:text-white">@if ($n->is_important)<span class="badge-red mr-1">Muhim</span>@endif<a href="{{ route('notifications.show', $n) }}">{{ $n->title }}</a></td>
                <td class="text-xs">{{ collect($n->roles ?? [])->map(fn ($r) => \App\Enums\Role::tryFrom($r)?->label())->implode(', ') }}<div class="text-ink-400">{{ $n->branch_ids ? count($n->branch_ids).' ta filial' : 'Barcha filiallar' }}</div></td>
                <td>{{ $n->recipients_count }}</td><td>{{ $n->read_count }}</td>
                <td>{{ $n->sender?->name }}</td>
                <td class="whitespace-nowrap text-ink-500">{{ $n->created_at->format('d.m.Y H:i') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-10 text-center text-ink-500">Hali bildirishnoma yuborilmagan.</td></tr>
        @endforelse
        </tbody>
    </table></div><div class="p-4">{{ $history->links() }}</div></div>
@endsection
