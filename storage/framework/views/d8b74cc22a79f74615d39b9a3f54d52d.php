
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['points' => [], 'w' => 96, 'h' => 30]));

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

foreach (array_filter((['points' => [], 'w' => 96, 'h' => 30]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $pts = array_values(array_map('floatval', $points));
    $n = count($pts);
?>
<?php if($n >= 2): ?>
    <?php
        $min = min($pts); $max = max($pts); $span = ($max - $min) ?: 1; $pad = 3;
        $xy = [];
        foreach ($pts as $i => $v) {
            $xy[] = [round($pad + ($w - 2 * $pad) * $i / ($n - 1), 1), round($h - $pad - ($h - 2 * $pad) * ($v - $min) / $span, 1)];
        }
        $line = 'M'.implode(' L', array_map(fn ($p) => $p[0].','.$p[1], $xy));
        $area = $line.' L'.$xy[$n - 1][0].','.$h.' L'.$xy[0][0].','.$h.' Z';
    ?>
    <svg <?php echo e($attributes->class(['spark text-brand-500'])); ?> viewBox="0 0 <?php echo e($w); ?> <?php echo e($h); ?>" width="<?php echo e($w); ?>" height="<?php echo e($h); ?>" aria-hidden="true">
        <path data-area d="<?php echo e($area); ?>" fill="currentColor" opacity=".12" />
        <path data-line d="<?php echo e($line); ?>" pathLength="1" stroke-dasharray="1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/components/dash/spark.blade.php ENDPATH**/ ?>