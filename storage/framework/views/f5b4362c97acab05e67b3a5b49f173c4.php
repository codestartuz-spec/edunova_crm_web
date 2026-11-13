<?php $__env->startSection('title', 'Test natijasi'); ?>

<?php
    $score = $review['score'];
    $good = $score >= 60;
    $unanswered = collect($review['items'])->whereNull('chosen')->count();
    $wrong = $review['total'] - $review['correct'] - ($review['has_answers'] ? $unanswered : 0);
    $circ = 2 * pi() * 52;
    $msg = $score >= 85 ? "A'lo natija!" : ($score >= 60 ? 'Yaxshi natija!' : "Yana bir bor urinib ko'ring");
    $letters = ['A', 'B', 'C', 'D'];
?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => ($course?->name ?? 'Kurs').' — natija','subtitle' => $attempt->finished_at?->format('d.m.Y H:i')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($course?->name ?? 'Kurs').' — natija'),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attempt->finished_at?->format('d.m.Y H:i'))]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if($course): ?>
                <a href="<?php echo e(route('student.courses.show', $course)); ?>#test" class="btn-secondary">Kursga qaytish</a>
                <form method="POST" action="<?php echo e(route('student.tests.start', $course)); ?>"><?php echo csrf_field(); ?><button class="btn-primary">Qayta urinish</button></form>
            <?php endif; ?>
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

    <div class="card card-body mb-6 flex flex-col items-center gap-6 sm:flex-row">
        <div class="relative h-36 w-36 flex-none">
            <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90">
                <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" class="stroke-ink-100 dark:stroke-ink-800" />
                <circle cx="60" cy="60" r="52" fill="none" stroke-width="10" stroke-linecap="round"
                        class="<?php echo e($good ? 'stroke-emerald-500' : 'stroke-brand-600'); ?>"
                        stroke-dasharray="<?php echo e(round($circ * $score / 100, 1)); ?> <?php echo e(round($circ, 1)); ?>" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-extrabold <?php echo e($good ? 'text-emerald-600' : 'text-brand-600'); ?>"><?php echo e($score); ?>%</span>
            </div>
        </div>
        <div class="text-center sm:text-left">
            <div class="text-xl font-bold text-ink-900 dark:text-white"><?php echo e($msg); ?></div>
            <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                <span class="badge-green">To'g'ri: <?php echo e($review['correct']); ?></span>
                <?php if($review['has_answers']): ?>
                    <span class="badge-red">Noto'g'ri: <?php echo e($wrong); ?></span>
                    <?php if($unanswered): ?><span class="badge-gray">Javobsiz: <?php echo e($unanswered); ?></span><?php endif; ?>
                <?php endif; ?>
                <span class="badge-gray">Jami: <?php echo e($review['total']); ?></span>
            </div>
        </div>
    </div>

    <?php if(! $review['has_answers']): ?>
        <p class="text-sm text-ink-500">Bu urinishning savollar bo'yicha tahlili saqlanmagan.</p>
    <?php else: ?>
        <h2 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">Javoblar tahlili</h2>
        <div class="max-w-3xl space-y-4">
            <?php $__currentLoopData = $review['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card card-body">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div class="text-sm font-semibold text-ink-900 dark:text-white"><?php echo e($n + 1); ?>. <?php echo e($item['question']); ?></div>
                        <span class="flex-none <?php echo e($item['is_correct'] ? 'badge-green' : ($item['chosen'] === null ? 'badge-gray' : 'badge-red')); ?>"><?php echo e($item['is_correct'] ? "To'g'ri" : ($item['chosen'] === null ? 'Javobsiz' : "Noto'g'ri")); ?></span>
                    </div>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oi => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isRight = $oi === $item['correct_index'];
                                $isChosen = $oi === $item['chosen'];
                                $cls = $isRight ? 'border-emerald-500 bg-emerald-50 text-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200'
                                    : ($isChosen ? 'border-red-400 bg-red-50 text-red-900 dark:bg-red-900/20 dark:text-red-200' : 'border-ink-200 text-ink-600 dark:border-ink-700 dark:text-ink-300');
                            ?>
                            <div class="flex items-center gap-3 rounded-xl border px-4 py-2.5 text-sm <?php echo e($cls); ?>">
                                <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-white/70 text-xs font-bold dark:bg-black/20"><?php echo e($letters[$oi] ?? ''); ?></span>
                                <span class="flex-1"><?php echo e($opt); ?></span>
                                <?php if($isRight): ?><span class="text-xs font-semibold">To'g'ri javob</span><?php elseif($isChosen): ?><span class="text-xs font-semibold">Sizning javobingiz</span><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/test-result.blade.php ENDPATH**/ ?>