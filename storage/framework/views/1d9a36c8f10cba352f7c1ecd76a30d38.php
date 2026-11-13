<?php $__env->startSection('title', 'Ish haqi'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['icon' => 'briefcase','title' => 'Hodimlar','subtitle' => 'O\'qituvchi va hodimlarga to\'lovlar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'briefcase','title' => 'Hodimlar','subtitle' => 'O\'qituvchi va hodimlarga to\'lovlar']); ?>
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
    <?php echo $__env->make('staff._tabs', ['active' => $tab], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table class="table table-anim">
                <thead><tr><th>F.I.O</th><th>Lavozim</th><?php if($tab === 'teachers'): ?><th class="text-right">To'lanmagan qoldiq</th><?php endif; ?><th class="text-right">Shu oy to'landi</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr data-href="<?php echo e(route('payroll.show', $r['user'])); ?>">
                        <td class="font-medium text-ink-900 dark:text-white"><a href="<?php echo e(route('payroll.show', $r['user'])); ?>" class="hover:text-brand-600"><?php echo e($r['user']->name); ?></a></td>
                        <td><span class="<?php echo e($r['user']->role->badgeClass()); ?>"><?php echo e($r['user']->role->label()); ?></span></td>
                        <?php if($tab === 'teachers'): ?><td class="text-right font-semibold <?php echo e($r['remaining'] > 0 ? 'text-brand-600' : ''); ?>"><span data-countup="<?php echo e((int) $r['remaining']); ?>" data-unit="money"><?php echo e(\App\Support\Format::money($r['remaining'])); ?></span></td><?php endif; ?>
                        <td class="text-right"><span data-countup="<?php echo e((int) $r['paid_month']); ?>" data-unit="money"><?php echo e(\App\Support\Format::money($r['paid_month'])); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="py-10 text-center text-ink-500">Ro'yxat bo'sh.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/payroll/index.blade.php ENDPATH**/ ?>