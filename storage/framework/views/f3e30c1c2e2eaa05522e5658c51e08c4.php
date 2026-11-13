<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null, 'money' => false, 'phone' => false]));

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

foreach (array_filter((['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null, 'money' => false, 'phone' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    // v8 A6: agar shu maydon nomi bilan avval massiv (masalan name[]=x) yuborilgan bo'lsa, old() massiv
    // qaytaradi - {{ }} uni to'g'ridan-to'g'ri chiqarsa 500 xato beradi. SafeInput bunday holatda $value'ni qaytaradi.
    $current = \App\Support\SafeInput::string(old($name), $value, 5000);
    if ($money) {
        $type = 'text';
        $current = filled($current) ? number_format((int) preg_replace('/\D/', '', (string) $current), 0, '', ' ') : $current;
    }
    if ($phone) {
        $type = 'tel';
    }
?>
<div <?php echo e($attributes->only('class')); ?>>
    <label for="<?php echo e($name); ?>" class="label"><?php echo e($label); ?> <?php if($required): ?><span class="text-brand-600">*</span><?php endif; ?></label>
    <input id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" type="<?php echo e($type); ?>"
           <?php if($type !== 'password'): ?> value="<?php echo e($current); ?>" <?php endif; ?>
           <?php if($money): ?> inputmode="numeric" data-money autocomplete="off" <?php endif; ?>
           <?php if($phone): ?> inputmode="tel" data-phone maxlength="17" pattern="\+998 \d{2} \d{3} \d{4}" placeholder="+998 90 123 4567" title="+998 90 123 4567 ko'rinishida" <?php endif; ?>
           <?php if($required): echo 'required'; endif; ?>
           <?php echo e($attributes->except(['class', 'min', 'max', 'step'])->merge(['class' => 'input'.($errors->has($name) ? ' input-error' : '')])); ?>

           <?php if (! ($money)): ?> <?php echo e($attributes->only(['min', 'max', 'step'])); ?> <?php endif; ?>>
    <?php if($hint): ?><p class="hint"><?php echo e($hint); ?></p><?php endif; ?>
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/components/input.blade.php ENDPATH**/ ?>