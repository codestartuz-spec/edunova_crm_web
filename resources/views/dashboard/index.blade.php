@extends('layouts.app')
@section('title', 'Bosh sahifa')

@section('content')
    <x-page-header icon="home" :title="'Salom, '.auth()->user()->name" :subtitle="$currentBranch ? $currentBranch->name.' filiali' : (auth()->user()->isGlobal() ? 'Barcha filiallar bo\'yicha umumiy ko\'rinish' : '')" />

    @if (auth()->user()->needsPasswordReminder())
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
            Parolingiz {{ auth()->user()->password_changed_at->diffInDays(now()) }} kundan beri o'zgarmagan. Xavfsizlik uchun vaqti-vaqti bilan yangilab turishni tavsiya qilamiz -
            <a href="{{ route('profile.edit') }}" class="font-semibold underline">profil sahifasida</a> o'zgartirishingiz mumkin.
        </div>
    @endif

    @if ($todo)
        <div class="mb-6 grid gap-4 lg:grid-cols-2">
            @foreach ($todo as $group)
                <div class="card card-hover card-body">
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="stat-icon !h-9 !w-9 !rounded-lg"><x-icon :name="$group['icon']" class="h-5 w-5" /></span>
                        <h3 class="text-sm font-semibold text-ink-900 dark:text-white">{{ $group['title'] }}</h3>
                        <span class="badge-gray ml-auto">{{ count($group['items']) }}</span>
                    </div>
                    <div class="space-y-2 {{ in_array($group['key'], ['debtors', 'leads'], true) ? 'max-h-72 overflow-y-auto pr-1' : '' }}">
                        @foreach ($group['items'] as $item)
                            <div class="todo-row flex items-center justify-between gap-3 rounded-lg bg-ink-50 px-3 py-2 text-sm dark:bg-ink-800/60">
                                <a href="{{ $item['url'] }}" class="min-w-0 flex-1">
                                    <div class="truncate font-medium text-ink-900 dark:text-white">{{ $item['label'] }}</div>
                                    @if ($item['meta'])<div class="truncate text-xs text-ink-500">{{ $item['meta'] }}</div>@endif
                                </a>
                                <form method="POST" action="{{ route('tasks.dismiss') }}">
                                    @csrf
                                    <input type="hidden" name="task_key" value="{{ $item['key'] }}">
                                    <button class="btn-ghost btn-sm shrink-0" title="Bajardim (bugunga yashirish)">
                                        <x-icon name="check" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if (! empty($weeklyActivity))
        <div class="card card-body mb-6">
            <h3 class="mb-3 text-sm font-semibold text-ink-900 dark:text-white">Xodimlar faolligi (so'nggi 7 kun, bajarilgan vazifalar)</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($weeklyActivity as $row)
                    <span class="badge-gray">{{ $row['name'] }}: {{ $row['count'] }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['Adminlar', $staffCounts['admin'] ?? 0, 'shield'],
                ['Menejerlar', $staffCounts['manager'] ?? 0, 'users'],
                ["O'qituvchilar", $staffCounts['teacher'] ?? 0, 'user'],
                ['Operatorlar', $staffCounts['operator'] ?? 0, 'phone'],
            ];
            if (auth()->user()->isGlobal()) {
                $cards[] = ['Faol filiallar', $branches->where('status', \App\Enums\BranchStatus::Active)->count(), 'building'];
            }
        @endphp
        @foreach ($cards as [$label, $value, $icon])
            <x-stat :label="$label" :value="$value" :icon="$icon" />
        @endforeach
    </div>

    @if ($money)
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([["Bugungi tushum", $money['income'], 'banknote', 'emerald', 'money'], ['Bugun qaytarilgan', $money['refunds'], 'wallet', 'amber', 'money'], ['Qarzdorlar soni', $money['debtors'], 'users', 'ink', 'int'], ['Umumiy qarz', $money['debt'], 'alert', 'red', 'money']] as [$l, $v, $ic, $tn, $un])
                <x-stat :label="$l" :value="$v" :icon="$ic" :tone="$tn" :unit="$un" />
            @endforeach
        </div>
    @endif

    @if ($groupStats)
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-stat label="Faol guruhlar" :value="$groupStats['active']['groups']" icon="users" tone="emerald" :sub="number_format($groupStats['active']['students'], 0, '', ' ').' o\'quvchi'" />
            <x-stat label="Boshlanishi kutilayotgan guruhlar" :value="$groupStats['upcoming']['groups']" icon="calendar" tone="amber" :sub="number_format($groupStats['upcoming']['students'], 0, '', ' ').' o\'quvchi'" />
        </div>
    @endif

    @if ($calendar)
        @include('dashboard._calendar')
    @else
        <div class="card card-body mt-8 flex items-center gap-4 text-sm text-ink-500">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950 dark:text-brand-300"><x-icon name="building" class="h-5 w-5" /></span>
            <p><b class="text-ink-800 dark:text-ink-100">Dars kalendari.</b> Kalendarni ko'rish uchun yuqoridagi ro'yxatdan <b>filialni tanlang</b>: shu filialda qaysi kuni, qaysi vaqtda, qaysi xonada qaysi guruhning darsi borligi ko'rinadi.</p>
        </div>
    @endif

    @if (auth()->user()->isGlobal())
        <div class="card mt-8 overflow-hidden">
            <div class="flex items-center justify-between px-5 pt-5 sm:px-6">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-ink-900 dark:text-white"><span class="page-accent !h-5"></span>Filiallar</h2>
                <a href="{{ route('branches.index') }}" class="btn-ghost btn-sm">Barchasi</a>
            </div>
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead><tr><th>Filial</th><th>Hodimlar</th><th>O'quvchilar</th><th>Holat</th></tr></thead>
                    <tbody>
                    @forelse ($branches as $b)
                        <tr>
                            <td><a href="{{ route('branches.show', $b) }}" class="flex items-center gap-2.5 font-medium text-ink-900 hover:text-brand-600 dark:text-white"><span class="avatar-sm" style="{{ \App\Support\BrandTheme::valid($b->brand_color ?? '') ? 'background:'.$b->brand_color.'22;color:'.$b->brand_color : '' }}">{{ mb_strtoupper(mb_substr($b->name, 0, 1)) }}</span>{{ $b->name }}</a></td>
                            <td>{{ $b->staff_count }}</td>
                            <td>{{ $b->students_count }}</td>
                            <td><span class="{{ $b->isActive() ? 'badge-green' : 'badge-gray' }}">{{ $b->status->label() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-ink-500">Hali filial ochilmagan. <a class="font-semibold text-brand-600" href="{{ route('branches.create') }}">Birinchi filialni oching</a>.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
