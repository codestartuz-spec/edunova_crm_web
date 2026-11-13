
<nav class="<?php echo e($class ?? 'mt-8'); ?> flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs text-ink-400" aria-label="Huquqiy hujjatlar">
    <?php $__currentLoopData = \App\Services\LegalService::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('legal.show', $t)); ?>" class="hover:text-brand-600 hover:underline"><?php echo e($label); ?></a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/partials/legal-links.blade.php ENDPATH**/ ?>