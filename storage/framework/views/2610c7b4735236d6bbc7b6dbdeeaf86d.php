<?php $__env->startSection('title', 'Filialni tanlang'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card card-body">
        <h1 class="text-xl font-bold text-ink-900">Filialni tanlang</h1>
        <p class="mt-1 text-sm text-ink-500">Murojaat qoldirmoqchi bo'lgan filialni tanlang.</p>
        <div class="mt-5 space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('apply.show', $b->code)); ?>" class="block rounded-xl border border-ink-200 px-4 py-3 hover:border-brand-500 hover:bg-brand-50">
                    <div class="font-semibold text-ink-900"><?php echo e($b->name); ?></div>
                    <?php if($b->address): ?><div class="text-sm text-ink-500"><?php echo e($b->address); ?></div><?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-500">Hozircha faol filial yo'q.</p>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/apply/index.blade.php ENDPATH**/ ?>