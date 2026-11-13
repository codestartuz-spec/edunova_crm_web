<?php if($paginator->hasPages()): ?>
    <nav role="navigation" class="flex flex-col items-center justify-between gap-3 border-t border-ink-100 px-4 py-3 text-sm dark:border-ink-800 sm:flex-row">
        <p class="text-ink-500 dark:text-ink-400">
            <?php echo e($paginator->firstItem()); ?>–<?php echo e($paginator->lastItem()); ?> / <?php echo e($paginator->total()); ?>

        </p>
        <div class="flex items-center gap-1">
            <?php if($paginator->onFirstPage()): ?>
                <span class="btn-ghost btn-sm pointer-events-none opacity-40">Oldingi</span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="btn-secondary btn-sm">Oldingi</a>
            <?php endif; ?>

            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_string($element)): ?>
                    <span class="px-2 text-ink-400"><?php echo e($element); ?></span>
                <?php endif; ?>
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <span class="btn btn-sm bg-brand-600 text-white"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>" class="btn-ghost btn-sm"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="btn-secondary btn-sm">Keyingi</a>
            <?php else: ?>
                <span class="btn-ghost btn-sm pointer-events-none opacity-40">Keyingi</span>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/vendor/pagination/edunova.blade.php ENDPATH**/ ?>