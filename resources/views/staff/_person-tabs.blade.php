{{-- v14: hodim kartochkasi tablari. $person (User), $active: profile | payroll | permissions --}}
@php $me = auth()->user(); $isDir = $person->role === \App\Enums\Role::Director; @endphp
@unless ($isDir)
<div class="mb-6 flex w-fit flex-wrap gap-1 rounded-xl bg-ink-100 p-1 dark:bg-ink-800">
    @can('manage', $person)
        <a href="{{ route('staff.edit', $person) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ $active === 'profile' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300' }}">Profil</a>
    @endcan
    @if ($me->can($person->role === \App\Enums\Role::Teacher ? 'teachers.view' : 'staff.view') && $person->role !== \App\Enums\Role::SAdmin)
        <a href="{{ route('payroll.show', $person) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ $active === 'payroll' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300' }}">Ish haqi</a>
    @endif
    @if ($person->role === \App\Enums\Role::Teacher && $me->can('groups.view'))
        <a href="{{ route('groups.index', ['teacher_id' => $person->id]) }}" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-600 dark:text-ink-300">Guruhlar</a>
    @endif
    @can('assignPermissions', $person)
        <a href="{{ route('staff.permissions.edit', $person) }}" class="rounded-lg px-4 py-2 text-sm font-medium {{ $active === 'permissions' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300' }}">Ruxsatlar</a>
    @endcan
</div>
@endunless
