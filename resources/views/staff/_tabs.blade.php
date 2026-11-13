{{-- v15: bitta tab qatori: Ro'yxat | O'qituvchilar ish haqi | Hodimlar ish haqi. $active: 'list' | 'teachers' | 'staff' --}}
@php
    $me = auth()->user();
    $on = 'bg-white text-brand-700 shadow-sm dark:bg-ink-900';
    $off = 'text-ink-600 dark:text-ink-300';
@endphp
<div class="mb-6 flex w-fit max-w-full gap-1 overflow-x-auto rounded-xl bg-ink-100 p-1 dark:bg-ink-800">
    @if ($me->canany(['staff.view', 'teachers.view']))
        <a href="{{ route('staff.index') }}" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium {{ $active === 'list' ? $on : $off }}">Ro'yxat</a>
    @endif
    @if ($me->can('teachers.view'))
        <a href="{{ route('payroll.index', ['tab' => 'teachers']) }}" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium {{ $active === 'teachers' ? $on : $off }}">O'qituvchilar ish haqi</a>
    @endif
    @if ($me->can('staff.view'))
        <a href="{{ route('payroll.index', ['tab' => 'staff']) }}" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium {{ $active === 'staff' ? $on : $off }}">Hodimlar ish haqi</a>
    @endif
</div>
