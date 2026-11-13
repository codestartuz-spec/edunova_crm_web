<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => null, 'spec', 'table' => null, 'height' => 'h-72']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'subtitle' => null, 'spec', 'table' => null, 'height' => 'h-72']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $table ??= \App\Support\Viz::table($spec);
    $empty = \App\Support\Viz::isEmpty($spec);
    $hStyle = is_numeric($height) ? 'height: '.$height.'px' : null;
    $hClass = is_numeric($height) ? '' : $height;
?>
<div <?php echo e($attributes->class(['card card-body flex flex-col'])); ?> x-data="{ view: 'chart' }">
    <div class="mb-4 flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="text-base font-semibold text-ink-900 dark:text-white"><?php echo e($title); ?></h3>
            <?php if($subtitle): ?><p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400"><?php echo e($subtitle); ?></p><?php endif; ?>
        </div>
        <div class="flex shrink-0 rounded-lg bg-ink-100 p-0.5 text-xs font-semibold dark:bg-ink-800" role="tablist" aria-label="Ko'rinish">
            <button type="button" role="tab" :aria-selected="view === 'chart'" @click="view = 'chart'" :class="view === 'chart' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-500'" class="rounded-md px-2.5 py-1">Grafik</button>
            <button type="button" role="tab" :aria-selected="view === 'table'" @click="view = 'table'" :class="view === 'table' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-500'" class="rounded-md px-2.5 py-1">Jadval</button>
        </div>
    </div>

    <div x-show="view === 'chart'" class="flex-1">
        <?php if($empty): ?>
            <div <?php if($hStyle): ?> style="<?php echo e($hStyle); ?>" <?php endif; ?> class="flex <?php echo e($hClass); ?> items-center justify-center rounded-xl bg-ink-50 text-sm text-ink-400 dark:bg-ink-800/40">Bu davrda ma'lumot yo'q</div>
        <?php else: ?>
            <div class="relative <?php echo e($hClass); ?>" <?php if($hStyle): ?> style="<?php echo e($hStyle); ?>" <?php endif; ?>>
                <canvas data-spec="<?php echo e(json_encode($spec, JSON_UNESCAPED_UNICODE)); ?>" role="img" aria-label="<?php echo e($title); ?>"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <div x-show="view === 'table'" x-cloak class="flex-1">
        <div class="table-wrap max-h-[26rem] overflow-y-auto rounded-xl border border-ink-100 dark:border-ink-800">
            <table class="table">
                <thead><tr><?php $__currentLoopData = $table['columns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th class="<?php echo e($i > 0 ? 'text-right' : ''); ?>"><?php echo e($c); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $table['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr><?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="<?php echo e($i > 0 ? 'text-right tabular-nums' : 'font-medium text-ink-900 dark:text-white'); ?>">
                            <?php if(is_array($cell) && isset($cell['href'])): ?><a href="<?php echo e($cell['href']); ?>" class="hover:text-brand-600"><?php echo e($cell['text']); ?></a><?php else: ?><?php echo e(is_array($cell) ? $cell['text'] : $cell); ?><?php endif; ?>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="<?php echo e(count($table['columns'])); ?>" class="py-8 text-center text-ink-400">Ma'lumot yo'q</td></tr>
                <?php endif; ?>
                </tbody>
                <?php if(! empty($table['foot'])): ?>
                    <tfoot><tr class="bg-ink-50 font-semibold dark:bg-ink-800/50"><?php $__currentLoopData = $table['foot']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="px-4 py-3 <?php echo e($i > 0 ? 'text-right tabular-nums' : ''); ?>"><?php echo e($cell); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr></tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <?php echo e($slot); ?>

</div>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/components/dash/card.blade.php ENDPATH**/ ?>