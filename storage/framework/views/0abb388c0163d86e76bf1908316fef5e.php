<?php $__env->startSection('title', 'Bugungi davomad'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Bugungi davomad','subtitle' => today()->translatedFormat('l, d.m.Y')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Bugungi davomad','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(today()->translatedFormat('l, d.m.Y'))]); ?>
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

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $row = $byGroup[$g->id] ?? null; ?>
            <div class="card card-body flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <a href="<?php echo e(route('groups.show', $g)); ?>" class="text-lg font-semibold text-ink-900 hover:text-brand-600 dark:text-white"><?php echo e($g->name); ?></a>
                        <p class="text-sm text-ink-500"><?php echo e($g->course->name); ?> · <?php echo e($g->teacher->name); ?></p>
                    </div>
                    <span class="<?php echo e($row && $row['taken'] ? 'badge-green' : 'badge-amber'); ?>"><?php echo e($row && $row['taken'] ? 'Olingan' : 'Olinmagan'); ?></span>
                </div>
                <div class="mt-3 text-sm text-ink-600 dark:text-ink-300"><?php echo e($g->lessonTime->label); ?> · <?php echo e($g->room->name); ?> · <?php echo e($g->students_count); ?> o'quvchi</div>
                <?php if($row && $row['taken']): ?>
                    <div class="mt-3 text-sm">Keldi: <b class="text-emerald-600"><?php echo e($row['present']); ?></b> · Kelmadi: <b class="text-brand-600"><?php echo e($row['absent']); ?></b> · <?php echo e($row['rate']); ?>%</div>
                <?php endif; ?>
                <div class="mt-4 border-t border-ink-100 pt-4 dark:border-ink-800">
                    <a href="<?php echo e(route('groups.show', $g)); ?>" class="btn-primary btn-sm"><?php echo e($row && $row['taken'] ? 'Tahrirlash' : 'Davomad olish'); ?></a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="card card-body col-span-full py-12 text-center text-ink-500">Bugun dars bo'ladigan guruh yo'q.</div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/attendance/today.blade.php ENDPATH**/ ?>