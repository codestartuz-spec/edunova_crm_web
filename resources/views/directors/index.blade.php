@extends('layouts.app')
@section('title', 'Direktorlar')

@section('content')
    <x-page-header title="Direktorlar" subtitle="Siz belgilagan filiallarni ko'radigan va faqat berilgan ruxsatlar doirasida ishlaydigan foydalanuvchilar">
        <x-slot:actions>
            <a href="{{ route('directors.permissions.edit') }}" class="btn-secondary">Umumiy ruxsatlar</a>
            <a href="{{ route('directors.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Direktor qo'shish</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>F.I.O</th><th>Login</th><th>Telefon</th><th>Ruxsatlar</th><th>Filiallar</th><th>Holat</th><th>Oxirgi kirish</th><th class="text-right">Amallar</th></tr></thead>
                <tbody>
                @forelse ($directors as $d)
                    <tr>
                        <td class="font-medium text-ink-900 dark:text-white">{{ $d->name }}</td>
                        <td>{{ $d->username }}</td>
                        <td>{{ $d->phone ?? '—' }}</td>
                        <td>
                            <a href="{{ route('directors.permissions.edit') }}" class="badge-blue">{{ $counts[$d->id] ?? 0 }} / {{ $total }}</a>
                        </td>
                        <td>
                            @if ($d->all_branches ?? true)
                                <span class="badge-green">Barcha filiallar</span>
                            @else
                                @php $names = $assigned[$d->id] ?? []; @endphp
                                <div class="flex max-w-[16rem] flex-wrap gap-1">
                                    @forelse ($names as $n)<span class="badge-blue">{{ $n }}</span>@empty<span class="badge-red">Filial berilmagan</span>@endforelse
                                </div>
                            @endif
                        </td>
                        <td><span class="{{ $d->isActive() ? 'badge-green' : 'badge-gray' }}">{{ $d->status->label() }}</span></td>
                        <td class="text-xs text-ink-500">{{ $d->last_login_at?->format('d.m.Y H:i') ?? '—' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('directors.edit', $d->id) }}" class="btn-ghost btn-sm">Tahrirlash</a>
                                <form method="POST" action="{{ route('directors.toggle', $d->id) }}">@csrf
                                    <button class="btn-ghost btn-sm">{{ $d->isActive() ? 'Bloklash' : 'Faollashtirish' }}</button>
                                </form>
                                <form method="POST" action="{{ route('directors.destroy', $d->id) }}" onsubmit="return confirm('Bu direktor olib tashlansinmi? U tizimga kira olmaydi.')">@csrf @method('DELETE')
                                    <button class="btn-ghost btn-sm text-brand-600">Olib tashlash</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-10 text-center text-ink-500">Hali direktor qo'shilmagan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <p class="mt-4 text-xs text-ink-500">Tizimda yagona sAdmin bor: u barcha ruxsatlarga ega. Barcha direktorlar uchun ruxsatlar bitta: «Umumiy ruxsatlar» sahifasida belgilanadi va hammasiga birdek qo'llanadi. Olib tashlangan direktorning tarixi (jurnal, to'lovlar) saqlanib qoladi.</p>
@endsection
