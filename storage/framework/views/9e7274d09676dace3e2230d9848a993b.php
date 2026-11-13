<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['class' => 'h-10 w-10']));

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

foreach (array_filter((['class' => 'h-10 w-10']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($logo = \App\Support\Brand::logoUrl()): ?>
    <img src="<?php echo e($logo); ?>" alt="<?php echo e(\App\Support\Brand::name()); ?>" <?php echo e($attributes->merge(['class' => "inline-block $class shrink-0 rounded-xl object-contain"])); ?>>
<?php else: ?>
    <span <?php echo e($attributes->merge(['class' => "inline-flex $class shrink-0 items-center justify-center rounded-xl bg-brand-600 font-black tracking-tight text-white shadow-sm"])); ?>><?php echo e(\App\Support\Brand::initial()); ?></span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/components/logo.blade.php ENDPATH**/ ?>