<?php $__env->startSection('title', 'Davomad'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Davomad','subtitle' => $overall !== null ? 'Umumiy davomat: '.$overall.'%' : 'Davomat ma\'lumoti hali yo\'q']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Davomad','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overall !== null ? 'Umumiy davomat: '.$overall.'%' : 'Davomat ma\'lumoti hali yo\'q')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card card-body">
                <div class="text-sm font-medium text-ink-900 dark:text-white"><?php echo e($r['group']); ?></div>
                <div class="mt-2 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($r['rate'] !== null ? $r['rate'].'%' : '—'); ?></div>
                <div class="mt-1 text-xs text-ink-500"><?php echo e($r['present']); ?> / <?php echo e($r['total']); ?> dars</div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800"><div class="h-full rounded-full bg-emerald-500" style="width: <?php echo e((int) ($r['rate'] ?? 0)); ?>%"></div></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-ink-500">Davomat ma'lumoti hali yo'q.</p>
        <?php endif; ?>
    </div>

    <div class="card overflow-hidden"><div class="table-wrap">
        <table class="table">
            <thead><tr><th>Sana</th><th>Guruh</th><th>Holat</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($a->date->format('d.m.Y')); ?></td>
                    <td><?php echo e($a->group?->name ?? '—'); ?></td>
                    <td><span class="<?php echo e($a->is_present ? 'badge-green' : 'badge-red'); ?>"><?php echo e($a->is_present ? 'Keldi' : 'Kelmadi'); ?></span></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="3" class="py-8 text-center text-ink-500">So'nggi yozuvlar yo'q.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/attendance.blade.php ENDPATH**/ ?>