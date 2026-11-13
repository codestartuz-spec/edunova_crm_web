
<?php
    $me = auth()->user();
    $on = 'bg-white text-brand-700 shadow-sm dark:bg-ink-900';
    $off = 'text-ink-600 dark:text-ink-300';
?>
<div class="mb-6 flex w-fit max-w-full gap-1 overflow-x-auto rounded-xl bg-ink-100 p-1 dark:bg-ink-800">
    <?php if($me->canany(['staff.view', 'teachers.view'])): ?>
        <a href="<?php echo e(route('staff.index')); ?>" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium <?php echo e($active === 'list' ? $on : $off); ?>">Ro'yxat</a>
    <?php endif; ?>
    <?php if($me->can('teachers.view')): ?>
        <a href="<?php echo e(route('payroll.index', ['tab' => 'teachers'])); ?>" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium <?php echo e($active === 'teachers' ? $on : $off); ?>">O'qituvchilar ish haqi</a>
    <?php endif; ?>
    <?php if($me->can('staff.view')): ?>
        <a href="<?php echo e(route('payroll.index', ['tab' => 'staff'])); ?>" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium <?php echo e($active === 'staff' ? $on : $off); ?>">Hodimlar ish haqi</a>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/staff/_tabs.blade.php ENDPATH**/ ?>