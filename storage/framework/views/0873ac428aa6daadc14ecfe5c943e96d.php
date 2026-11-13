<?php $__env->startSection('title', "To'lovlar va balans"); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'To\'lovlar va balans','subtitle' => 'Hisobingizdagi barcha harakatlar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'To\'lovlar va balans','subtitle' => 'Hisobingizdagi barcha harakatlar']); ?>
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

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="card card-body">
            <div class="text-sm text-ink-500">Joriy balans</div>
            <div class="mt-1 text-2xl font-bold <?php echo e($student->balance < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e(\App\Support\Format::money($student->balance)); ?></div>
        </div>
        <div class="card card-body">
            <div class="text-sm text-ink-500">Qarzdorlik</div>
            <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e(\App\Support\Format::money(max(0, -$student->balance))); ?></div>
        </div>
    </div>

    <div class="card overflow-hidden"><div class="table-wrap">
        <table class="table">
            <thead><tr><th>Sana</th><th>Turi</th><th>Guruh</th><th class="text-right">Summa</th><th class="text-right">Balans</th><th>Izoh</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="whitespace-nowrap"><?php echo e($t->created_at->format('d.m.Y H:i')); ?></td>
                    <td><?php echo e($t->typeLabel()); ?></td>
                    <td><?php echo e($t->group?->name ?? '—'); ?></td>
                    <td class="text-right font-semibold <?php echo e($t->amount < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e($t->amount > 0 ? '+' : ''); ?><?php echo e(\App\Support\Format::money($t->amount)); ?></td>
                    <td class="text-right"><?php echo e(\App\Support\Format::money($t->balance_after)); ?></td>
                    <td class="text-xs text-ink-500"><?php echo e($t->note); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="py-10 text-center text-ink-500">Hali harakat yo'q.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div>
    <div class="mt-4"><?php echo e($transactions->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/payments.blade.php ENDPATH**/ ?>