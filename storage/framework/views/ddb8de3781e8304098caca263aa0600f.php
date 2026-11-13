
<?php $me = auth()->user(); $isDir = $person->role === \App\Enums\Role::Director; ?>
<?php if (! ($isDir)): ?>
<div class="mb-6 flex w-fit flex-wrap gap-1 rounded-xl bg-ink-100 p-1 dark:bg-ink-800">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage', $person)): ?>
        <a href="<?php echo e(route('staff.edit', $person)); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo e($active === 'profile' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300'); ?>">Profil</a>
    <?php endif; ?>
    <?php if($me->can($person->role === \App\Enums\Role::Teacher ? 'teachers.view' : 'staff.view') && $person->role !== \App\Enums\Role::SAdmin): ?>
        <a href="<?php echo e(route('payroll.show', $person)); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo e($active === 'payroll' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300'); ?>">Ish haqi</a>
    <?php endif; ?>
    <?php if($person->role === \App\Enums\Role::Teacher && $me->can('groups.view')): ?>
        <a href="<?php echo e(route('groups.index', ['teacher_id' => $person->id])); ?>" class="rounded-lg px-4 py-2 text-sm font-medium text-ink-600 dark:text-ink-300">Guruhlar</a>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assignPermissions', $person)): ?>
        <a href="<?php echo e(route('staff.permissions.edit', $person)); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo e($active === 'permissions' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300'); ?>">Ruxsatlar</a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/staff/_person-tabs.blade.php ENDPATH**/ ?>