<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Murojaat'); ?> · <?php echo e(isset($branch) ? $branch->name : \App\Support\Brand::short()); ?></title>
    <link rel="icon" href="<?php echo e(\App\Support\Brand::faviconUrl()); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php if(isset($branch)): ?>
        <style>:root { --brand-public: <?php echo e($branch->effectiveBrandColor()); ?>; }</style>
    <?php endif; ?>
</head>
<body class="min-h-full bg-ink-50">
<div class="mx-auto flex min-h-full max-w-lg flex-col justify-center px-4 py-10">
    <?php if(isset($branch)): ?>
        
        <div class="mb-6 flex flex-col items-center gap-3 text-center">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl text-xl font-extrabold text-white" style="background-color: <?php echo e($branch->effectiveBrandColor()); ?>"><?php echo e(mb_strtoupper(mb_substr($branch->name, 0, 1))); ?></span>
            <span class="text-xl font-extrabold tracking-tight text-ink-900"><?php echo e($branch->name); ?></span>
            <?php if($branch->public_about): ?>
                <p class="max-w-sm text-sm text-ink-500"><?php echo e($branch->public_about); ?></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="mb-6 flex items-center justify-center gap-3">
            <?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.logo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal987d96ec78ed1cf75b349e2e5981978f)): ?>
<?php $attributes = $__attributesOriginal987d96ec78ed1cf75b349e2e5981978f; ?>
<?php unset($__attributesOriginal987d96ec78ed1cf75b349e2e5981978f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal987d96ec78ed1cf75b349e2e5981978f)): ?>
<?php $component = $__componentOriginal987d96ec78ed1cf75b349e2e5981978f; ?>
<?php unset($__componentOriginal987d96ec78ed1cf75b349e2e5981978f); ?>
<?php endif; ?>
            <span class="text-xl font-extrabold tracking-tight text-ink-900"><?php echo e(\App\Support\Brand::short()); ?></span>
        </div>
    <?php endif; ?>
    <?php echo $__env->yieldContent('content'); ?>
    <?php echo $__env->make('partials.legal-links', ['class' => 'mt-8'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <p class="mt-3 text-center text-xs text-ink-400">
        <?php if(isset($branch)): ?>
            © <?php echo e(date('Y')); ?> <?php echo e($branch->name); ?>

        <?php else: ?>
            © <?php echo e(date('Y')); ?> <?php echo e(\App\Support\Brand::name()); ?>

        <?php endif; ?>
    </p>
</div>
<?php echo $__env->make('partials.cookie-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/layouts/public.blade.php ENDPATH**/ ?>