<?php $__env->startSection('title', 'Guruhlarim'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Guruhlarim','subtitle' => 'Siz a\'zo bo\'lgan va bo\'lgan barcha guruhlar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Guruhlarim','subtitle' => 'Siz a\'zo bo\'lgan va bo\'lgan barcha guruhlar']); ?>
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
    <div class="card overflow-hidden"><div class="table-wrap">
        <table class="table">
            <thead><tr><th>Guruh</th><th>Kurs</th><th>O'qituvchi</th><th>Boshlanishi</th><th>Tugashi</th><th>Holat</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-medium text-ink-900 dark:text-white"><?php echo e($g->name); ?></td>
                    <td><?php echo e($g->course?->name ?? '—'); ?></td>
                    <td><?php echo e($g->teacher?->name ?? '—'); ?></td>
                    <td><?php echo e($g->starts_on?->format('d.m.Y')); ?></td>
                    <td><?php echo e($g->ends_on?->format('d.m.Y')); ?></td>
                    <td><span class="<?php echo e($g->is_active_member ? 'badge-green' : 'badge-gray'); ?>"><?php echo e($g->is_active_member ? 'Faol' : 'Chiqqan'); ?></span></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="py-10 text-center text-ink-500">Siz hali guruhga qo'shilmagansiz.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/groups.blade.php ENDPATH**/ ?>