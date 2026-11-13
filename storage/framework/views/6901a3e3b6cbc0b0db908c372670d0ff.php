
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['stages', 'height' => 62, 'width' => 640]));

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

foreach (array_filter((['stages', 'height' => 62, 'width' => 640]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $W = (int) $width; $gap = 4; $n = count($stages);
    $max = max(1, (int) ($stages[0]['value'] ?? 0));
    $H = $n * $height + ($n - 1) * $gap;
    $minW = max(120, (int) round($W * 0.3));   // eng tor bosqichda ham yozuv sig'ishi uchun
    $widthOf = fn (int $v) => max($minW, (int) round($W * $v / $max));
    $fills = ['var(--viz-f1)', 'var(--viz-f2)', 'var(--viz-f3)', 'var(--viz-f4)'];
    $dark = ['#0b0b0b', '#ffffff', '#ffffff', '#ffffff'];
?>
<div class="w-full">
    <svg viewBox="0 0 <?php echo e($W); ?> <?php echo e($H); ?>" class="funnel-anim w-full" role="img" aria-label="Varonka: <?php echo e(collect($stages)->map(fn ($s) => $s['label'].' '.$s['value'])->implode(', ')); ?>">
        <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $top = $widthOf((int) $s['value']);
                $bottom = isset($stages[$i + 1]) ? $widthOf((int) $stages[$i + 1]['value']) : max((int) round($minW * 0.8), (int) round($top * 0.86));
                $y = $i * ($height + $gap);
                $cx = $W / 2;
                $pts = sprintf('%.1f,%d %.1f,%d %.1f,%d %.1f,%d', $cx - $top / 2, $y, $cx + $top / 2, $y, $cx + $bottom / 2, $y + $height, $cx - $bottom / 2, $y + $height);
                $share = $max ? round($s['value'] * 100 / $max) : 0;
                $conv = $i > 0 && ($stages[$i - 1]['value'] ?? 0) > 0 ? round($s['value'] * 100 / $stages[$i - 1]['value']) : null;
                $ci = min($i, 3);
            ?>
            <g>
                <title><?php echo e($s['label']); ?>: <?php echo e($s['value']); ?> (<?php echo e($share); ?>%)<?php echo e($conv !== null ? ", oldingi bosqichdan {$conv}%" : ''); ?></title>
                <polygon points="<?php echo e($pts); ?>" fill="<?php echo e($fills[$ci]); ?>" />
                <text x="<?php echo e($cx); ?>" y="<?php echo e($y + $height / 2 - 6); ?>" text-anchor="middle" font-size="13" font-weight="600" fill="<?php echo e($dark[$ci]); ?>"><?php echo e($s['label']); ?></text>
                <text x="<?php echo e($cx); ?>" y="<?php echo e($y + $height / 2 + 14); ?>" text-anchor="middle" font-size="18" font-weight="700" fill="<?php echo e($dark[$ci]); ?>"><tspan data-countup="<?php echo e((int) $s['value']); ?>"><?php echo e(number_format($s['value'], 0, '', ' ')); ?></tspan> <tspan font-size="12" font-weight="500" opacity=".85">· <?php echo e($share); ?>%</tspan></text>
            </g>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </svg>
    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs text-ink-500 dark:text-ink-400">
        <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($i > 0 && ($stages[$i - 1]['value'] ?? 0) > 0): ?>
                <span><?php echo e($stages[$i - 1]['label']); ?> → <?php echo e($s['label']); ?>: <b class="text-ink-800 dark:text-ink-100"><?php echo e(round($s['value'] * 100 / $stages[$i - 1]['value'])); ?>%</b></span>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/components/dash/funnel.blade.php ENDPATH**/ ?>