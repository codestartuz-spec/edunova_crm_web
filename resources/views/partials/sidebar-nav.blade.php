{{--
    v13: yon menyu bo'limlarga ajratilgan va yig'iladi. Bo'lim ichida foydalanuvchiga ko'rinadigan band
    bo'lmasa, bo'lim sarlavhasi ham chiqmaydi. Akkordeon: faqat faol sahifaning bo'limi ochiq turadi, qolganlari yopiq;
    boshqasini bossangiz u ochilib, oldingisi yopiladi.
--}}
@php
    $isTeacher = $me->role === \App\Enums\Role::Teacher;

    $groups = [
        [
            'key' => 'learn', 'title' => "O'quv jarayoni", 'icon' => 'cap',
            'items' => [
                ['label' => "O'quvchilar", 'href' => route('students.index'), 'icon' => 'cap', 'active' => request()->routeIs('students.*'), 'show' => $me->can('students.view')],
                ['label' => $isTeacher ? 'Guruhlarim' : 'Guruhlar', 'href' => route('groups.index'), 'icon' => 'users', 'active' => request()->routeIs('groups.*'), 'show' => $me->can('viewAny', \App\Models\Group::class)],
                ['label' => 'Bugungi davomad', 'href' => route('attendance.today'), 'icon' => 'calendar', 'active' => request()->routeIs('attendance.today'), 'show' => $me->can('attendance.view')],
                ['label' => 'Davomad statistikasi', 'href' => route('attendance.stats'), 'icon' => 'pulse', 'active' => request()->routeIs('attendance.stats'), 'show' => $me->can('attendance.stats')],
            ],
        ],
        [
            'key' => 'sales', 'title' => 'Murojaat va aloqa', 'icon' => 'funnel',
            'items' => [
                ['label' => 'Varonka', 'href' => route('leads.index'), 'icon' => 'funnel', 'active' => request()->routeIs('leads.*'), 'show' => $me->can('leads.view')],
                ['label' => 'SMS', 'href' => route('sms.index'), 'icon' => 'message', 'active' => request()->routeIs('sms.*'), 'show' => $me->canany(['sms.view', 'sms.send', 'sms.manage'])],
            ],
        ],
        [
            'key' => 'money', 'title' => 'Moliya', 'icon' => 'wallet',
            'items' => [
                ['label' => "To'lovlar", 'href' => route('payments.index'), 'icon' => 'banknote', 'active' => request()->routeIs('payments.*'), 'show' => $me->can('payments.view')],
                ['label' => 'Kassa', 'href' => route('cashbox.index'), 'icon' => 'cashbox', 'active' => request()->routeIs('cashbox.*'), 'show' => $me->can('cashbox.view')],
                ['label' => 'Moliya', 'href' => route('finance.index'), 'icon' => 'coins', 'active' => request()->routeIs('finance.*'), 'show' => $me->can('finance.view')],
            ],
        ],
        [
            'key' => 'analytics', 'title' => 'Tahlil', 'icon' => 'chart',
            'items' => [
                ['label' => 'Statistika', 'href' => route('statistics.index'), 'icon' => 'chart', 'active' => request()->routeIs('statistics.*'), 'show' => $me->can('statistics.view')],
                ['label' => 'Sertifikatlar', 'href' => route('certificates.index'), 'icon' => 'award', 'active' => request()->routeIs('certificates.*'), 'show' => $me->can('certificates.issue')],
                ['label' => 'Hisobotlar', 'href' => route('reports.index'), 'icon' => 'report', 'active' => request()->routeIs('reports.*'), 'show' => $me->can('reports.view')],
            ],
        ],
        [
            'key' => 'manage', 'title' => 'Boshqaruv', 'icon' => 'shield',
            'items' => [
                $me->can('staff.view')
                    ? ['label' => 'Hodimlar', 'href' => route('staff.index'), 'icon' => 'badge', 'active' => request()->routeIs('staff.index', 'staff.create', 'staff.edit', 'staff.permissions.*', 'payroll.*'), 'show' => true]
                    : ['label' => "O'qituvchilar", 'href' => route('staff.index', ['role' => 'teacher']), 'icon' => 'badge', 'active' => request()->routeIs('staff.index', 'staff.create', 'staff.edit', 'staff.permissions.*', 'payroll.*'), 'show' => $me->can('teachers.view')],
                ['label' => 'Sozlamalar', 'href' => route('settings.index'), 'icon' => 'cog', 'active' => request()->is('settings', 'settings/*'), 'show' => $me->canany(['settings.branch', 'courses.view', 'courses.manage'])],
            ],
        ],
        [
            'key' => 'system', 'title' => 'Tizim', 'icon' => 'cog',
            'items' => [
                ['label' => 'Filiallar', 'href' => route('branches.index'), 'icon' => 'building', 'active' => request()->routeIs('branches.*'), 'show' => $me->can('branches.manage')],
                ['label' => 'Obuna', 'href' => route('subscriptions.index'), 'icon' => 'crown', 'active' => request()->routeIs('subscriptions.*'), 'show' => $me->isSuperAdmin() || $me->isDirector()],
                ['label' => 'Direktorlar', 'href' => route('directors.index'), 'icon' => 'briefcase', 'active' => request()->routeIs('directors.*'), 'show' => $me->isSuperAdmin()],
                ['label' => 'Brend', 'href' => route('brand.edit'), 'icon' => 'palette', 'active' => request()->routeIs('brand.*'), 'show' => $me->isSuperAdmin()],
                ['label' => 'Telegram bot', 'href' => route('telegram.index'), 'icon' => 'send', 'active' => request()->routeIs('telegram.*'), 'show' => $me->isSuperAdmin()],
                ['label' => "Eski bazani ko'chirish", 'href' => route('legacy.index'), 'icon' => 'database', 'active' => request()->routeIs('legacy.*'), 'show' => $me->isSuperAdmin() && config('features.legacy_import')],
                ['label' => 'Zaxira nusxalar', 'href' => route('backups.index'), 'icon' => 'cloud', 'active' => request()->routeIs('backups.*'), 'show' => $me->isSuperAdmin()],
                ['label' => 'Huquqiy hujjatlar', 'href' => route('legal.admin'), 'icon' => 'scale', 'active' => request()->routeIs('legal.admin*', 'legal.journal*'), 'show' => $me->can('legal.manage')],
                ['label' => 'Tizim holati', 'href' => route('system-status.index'), 'icon' => 'heartbeat', 'active' => request()->routeIs('system-status.*'), 'show' => $me->can('system.view')],
                ['label' => 'Bildirishnomalar', 'href' => route('notifications.index'), 'icon' => 'bell', 'active' => request()->routeIs('notifications.*'), 'show' => $me->can('notifications.send')],
                ['label' => 'Harakatlar jurnali', 'href' => route('audit.index'), 'icon' => 'history', 'active' => request()->routeIs('audit.*'), 'show' => $me->can('audit.view')],
            ],
        ],
    ];
@endphp

<x-nav-link :href="route('dashboard')" icon="home" :active="request()->routeIs('dashboard')">Bosh sahifa</x-nav-link>
@if ($me->isSuperAdmin())
    <x-nav-link :href="route('tutorials.index')" icon="play" :active="request()->routeIs('tutorials.index', 'tutorials.show')">Video darslar</x-nav-link>
@endif

@foreach ($groups as $group)
    @php
        $visible = array_values(array_filter($group['items'], fn ($i) => $i['show']));
        $hasActive = (bool) collect($visible)->contains(fn ($i) => $i['active']);
    @endphp
    @if ($visible !== [])
        <div x-data="navGroup('{{ $group['key'] }}', {{ $hasActive ? 'true' : 'false' }})" class="pt-2">
            <button type="button" @click="toggle()" :aria-expanded="open.toString()" aria-expanded="{{ $hasActive ? 'true' : 'false' }}" @class(['nav-group-btn', 'is-current' => $hasActive])>
                <x-icon :name="$group['icon']" colored class="h-4 w-4 shrink-0" />
                <span class="flex-1 text-left">{{ $group['title'] }}</span>
                @if ($hasActive)<span class="nav-dot h-1.5 w-1.5 rounded-full bg-accent-500"></span>@endif
                <x-icon name="chevron" class="nav-chev h-4 w-4 shrink-0" />
            </button>
            <div @class(['nav-panel', 'is-open' => $hasActive]) :class="{ 'is-open': open }" :inert="! open">
                <div class="nav-panel-in">
                    <div class="ml-4 mt-1 space-y-1 border-l-2 border-brand-100 pl-2 dark:border-ink-800">
                        @foreach ($visible as $item)
                            <x-nav-link :href="$item['href']" :icon="$item['icon']" :active="$item['active']" :target="! empty($item['blank']) ? '_blank' : null">{{ $item['label'] }}</x-nav-link>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
