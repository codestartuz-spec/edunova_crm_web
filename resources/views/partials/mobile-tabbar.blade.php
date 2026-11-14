{{-- Telefon va planshetda pastki tez panel (lg dan katta ekranda yashirin). Elementlar foydalanuvchi huquqiga qarab tanlanadi. --}}
@php
    if ($me->isStudent()) {
        $tabs = [
            ['Bosh', 'home', route('student.home'), request()->routeIs('student.home')],
            ['Guruh', 'users', route('student.groups'), request()->routeIs('student.groups')],
            ["To'lov", 'banknote', route('student.payments'), request()->routeIs('student.payments')],
            ['Davomad', 'calendar', route('student.attendance'), request()->routeIs('student.attendance')],
        ];
    } else {
        $cands = [
            ['Bosh', 'home', route('dashboard'), request()->routeIs('dashboard'), true],
            ["O'quvchi", 'cap', route('students.index'), request()->routeIs('students.*'), $me->can('students.view')],
            ['Davomad', 'calendar', route('attendance.today'), request()->routeIs('attendance.*'), $me->can('attendance.view')],
            ["To'lov", 'banknote', route('payments.index'), request()->routeIs('payments.*'), $me->can('payments.view')],
            ['Guruh', 'users', route('groups.index'), request()->routeIs('groups.*'), $me->can('viewAny', \App\Models\Group::class)],
            ['Varonka', 'funnel', route('leads.index'), request()->routeIs('leads.*'), $me->can('leads.view')],
            ['Kassa', 'cashbox', route('cashbox.index'), request()->routeIs('cashbox.*'), $me->can('cashbox.view')],
        ];
        $tabs = array_map(fn ($t) => array_slice($t, 0, 4), array_slice(array_values(array_filter($cands, fn ($t) => $t[4])), 0, 4));
    }
@endphp
<nav class="tabbar lg:hidden" aria-label="Tez panel">
    @foreach ($tabs as [$label, $icon, $href, $active])
        <a href="{{ $href }}" @class(['tab', 'is-active' => $active]) @if ($active) aria-current="page" @endif>
            <x-icon :name="$icon" bubble />
            <span>{{ $label }}</span>
        </a>
    @endforeach
    <button type="button" class="tab" @click="sidebar = true" aria-label="Menyu">
        <x-icon name="menu" bubble tone="violet" />
        <span>Menyu</span>
    </button>
</nav>
