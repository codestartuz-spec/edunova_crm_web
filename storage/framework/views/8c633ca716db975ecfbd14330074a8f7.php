<?php $__env->startSection('title', 'Kurs materiallari'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Kurs materiallari','subtitle' => 'Faol guruhlaringiz kurslarining video, audio va testlari']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kurs materiallari','subtitle' => 'Faol guruhlaringiz kurslarining video, audio va testlari']); ?>
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

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('student.courses.show', $c)); ?>" class="card card-body transition hover:shadow-md">
                <div class="text-base font-semibold text-ink-900 dark:text-white"><?php echo e($c->name); ?></div>
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-ink-500">
                    <span class="badge-gray"><?php echo e($c->videos_count); ?> video</span>
                    <span class="badge-gray"><?php echo e($c->audios_count); ?> audio</span>
                    <span class="badge-gray"><?php echo e($c->questions_count); ?> savol</span>
                </div>
                <div class="mt-3 text-xs text-ink-500">
                    <?php if(isset($best[$c->id])): ?> Eng yaxshi natija: <b class="text-ink-900 dark:text-white"><?php echo e($best[$c->id]->best); ?>%</b> · <?php echo e($best[$c->id]->attempts); ?> urinish <?php else: ?> Test hali topshirilmagan <?php endif; ?>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-ink-500">Hozircha faol guruh yo'q, shuning uchun materiallar ko'rinmaydi.</p>
        <?php endif; ?>
    </div>

    <?php if($books->isNotEmpty()): ?>
        <h2 class="mb-3 mt-8 text-base font-semibold text-ink-900 dark:text-white">Kitoblar</h2>
        <div class="card card-body space-y-2">
            <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between text-sm"><span><?php echo e($b->name); ?></span><?php if($b->url): ?><a href="<?php echo e($b->url); ?>" target="_blank" rel="noopener" class="font-semibold text-brand-600">Ochish</a><?php endif; ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/courses.blade.php ENDPATH**/ ?>