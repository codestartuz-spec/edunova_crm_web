<?php $__env->startSection('title', 'Bosh sahifa'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Salom, '.$student->name,'subtitle' => 'Sizning shaxsiy kabinetingiz']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Salom, '.$student->name),'subtitle' => 'Sizning shaxsiy kabinetingiz']); ?>
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

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="card card-body">
            <div class="text-sm text-ink-500">Balans</div>
            <div class="mt-1 text-2xl font-bold <?php echo e($student->balance < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e(\App\Support\Format::money($student->balance)); ?></div>
            <?php if($student->balance < 0): ?><div class="mt-1 text-xs text-brand-600">Qarzdorlik: <?php echo e(\App\Support\Format::money(-$student->balance)); ?></div><?php endif; ?>
        </div>
        <div class="card card-body">
            <div class="text-sm text-ink-500">Faol guruhlar</div>
            <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($groups->count()); ?></div>
        </div>
        <div class="card card-body">
            <div class="text-sm text-ink-500">Umumiy davomat</div>
            <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($overallRate !== null ? $overallRate.'%' : '—'); ?></div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card card-body">
            <h2 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">Guruhlarim</h2>
            <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between border-b border-ink-100 py-2 text-sm last:border-0 dark:border-ink-800">
                    <div><div class="font-medium text-ink-900 dark:text-white"><?php echo e($g->name); ?></div><div class="text-xs text-ink-500"><?php echo e($g->course?->name); ?> · <?php echo e($g->teacher?->name); ?></div></div>
                    <div class="text-xs text-ink-500"><?php echo e($g->starts_on?->format('d.m.Y')); ?> — <?php echo e($g->ends_on?->format('d.m.Y')); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-500">Hozircha faol guruh yo'q.</p>
            <?php endif; ?>
        </div>

        <div class="card card-body">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-base font-semibold text-ink-900 dark:text-white">So'nggi harakatlar</h2>
                <a href="<?php echo e(route('student.payments')); ?>" class="text-xs font-semibold text-brand-600">Hammasi</a>
            </div>
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between border-b border-ink-100 py-2 text-sm last:border-0 dark:border-ink-800">
                    <div><div class="font-medium text-ink-900 dark:text-white"><?php echo e($t->typeLabel()); ?></div><div class="text-xs text-ink-500"><?php echo e($t->group?->name); ?> · <?php echo e($t->created_at->format('d.m.Y H:i')); ?></div></div>
                    <div class="font-semibold <?php echo e($t->amount < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e($t->amount > 0 ? '+' : ''); ?><?php echo e(\App\Support\Format::money($t->amount)); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-500">Hozircha harakat yo'q.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if(! empty($certificates) && $certificates->isNotEmpty()): ?>
        <div class="card card-body mt-6">
            <h2 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">Sertifikatlarim</h2>
            <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between gap-3 border-t border-ink-100 py-2 text-sm first:border-0 dark:border-ink-800">
                    <div><b><?php echo e($c->course_name); ?></b> <span class="text-xs text-ink-500">· <?php echo e($c->issued_on->format('d.m.Y')); ?> · <?php echo e($c->number); ?></span></div>
                    <a href="<?php echo e(route('student.certificates.mine', $c->id)); ?>" target="_blank" class="btn-secondary btn-sm">PDF</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/home.blade.php ENDPATH**/ ?>