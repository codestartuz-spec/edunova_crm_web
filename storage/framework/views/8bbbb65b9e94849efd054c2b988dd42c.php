<?php $__env->startSection('title', $name); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name)]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('reports.index')); ?>" class="btn-secondary">Hisobotlar</a>
            <?php if($canExport): ?><a href="<?php echo e(route('reports.export', ['report' => $key, 'from' => $from->toDateString(), 'to' => $to->toDateString()])); ?>" class="btn-primary">Excelga yuklab olish</a><?php endif; ?>
         <?php $__env->endSlot(); ?>
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

    <?php if($needsPeriod): ?>
        <form method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div><label class="label">Boshlanishi</label><input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="input"></div>
            <div><label class="label">Tugashi</label><input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="input"></div>
            <button class="btn-secondary">Ko'rsatish</button>
        </form>
    <?php endif; ?>

    <?php if($data['summary']): ?>
        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <?php $__currentLoopData = $data['summary']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card card-body"><div class="text-sm text-ink-500"><?php echo e($label); ?></div>
                    <div class="mt-1 text-xl font-bold text-ink-900 dark:text-white"><?php echo e(is_numeric($value) ? number_format($value, is_float($value) ? 1 : 0, '.', ' ') : $value); ?></div></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="card overflow-hidden">
        <div class="table-wrap"><table class="table">
            <thead><tr><?php $__currentLoopData = $data['columns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th><?php echo e($c); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $shown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr><?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="<?php echo e(is_int($cell) ? 'text-right' : ''); ?>"><?php echo e(is_int($cell) ? number_format($cell, 0, '', ' ') : $cell); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="<?php echo e(count($data['columns'])); ?>" class="py-12 text-center text-ink-500">Ma'lumot yo'q.</td></tr>
            <?php endif; ?>
            </tbody></table></div>
        <?php if(count($data['rows']) > count($shown)): ?>
            <p class="border-t border-ink-100 px-5 py-3 text-sm text-ink-500 dark:border-ink-800">Sahifada dastlabki <?php echo e(count($shown)); ?> ta qator ko'rsatildi. Barcha <?php echo e(count($data['rows'])); ?> qator Excel faylida.</p>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/reports/show.blade.php ENDPATH**/ ?>