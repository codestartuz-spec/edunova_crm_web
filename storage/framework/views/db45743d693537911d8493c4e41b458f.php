<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($doc->title); ?> · Edunova CRM</title>
    <meta name="robots" content="noindex">
    <link rel="icon" href="<?php echo e(asset('favicon.svg')); ?>" type="image/svg+xml">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-full bg-ink-50 text-ink-800 dark:bg-ink-950 dark:text-ink-100">
<div class="mx-auto max-w-3xl px-4 py-8 sm:py-12">
    <div class="mb-6 flex items-center justify-between gap-3">
        <a href="<?php echo e(auth()->check() ? route('dashboard') : route('login')); ?>" class="flex items-center gap-3"><?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
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
<?php endif; ?><span class="text-xl font-extrabold tracking-tight text-ink-900 dark:text-white">Edunova</span></a>
        <a href="<?php echo e(auth()->check() ? route('dashboard') : route('login')); ?>" class="text-sm text-ink-500 hover:text-brand-600">← Orqaga</a>
    </div>

    <div class="mb-4 flex flex-wrap gap-2 text-sm font-semibold">
        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('legal.show', $t)); ?>" class="rounded-lg px-3 py-1.5 <?php echo e($doc->type === $t ? 'bg-brand-600 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-100 hover:bg-ink-100 dark:bg-ink-900 dark:text-ink-300 dark:ring-ink-800'); ?>"><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-ink-100 dark:bg-ink-900 dark:ring-ink-800 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white"><?php echo e($doc->title); ?></h1>
        <p class="mt-1 text-xs text-ink-400">Versiya <?php echo e($doc->version); ?> · <?php echo e(($doc->published_at ?? $doc->updated_at)->timezone('Asia/Tashkent')->format('d.m.Y')); ?> dan kuchda</p>
        <div class="legal-prose mt-6 text-ink-700 dark:text-ink-200"><?php echo $html; ?></div>
    </article>
    <?php echo $__env->make('partials.legal-links', ['class' => 'mt-6'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php if(auth()->guard()->guest()): ?> <?php echo $__env->make('partials.cookie-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/legal/show.blade.php ENDPATH**/ ?>