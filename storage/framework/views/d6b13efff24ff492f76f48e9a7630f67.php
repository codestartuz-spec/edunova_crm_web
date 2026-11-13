<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kirish · <?php echo e(\App\Support\Brand::name()); ?></title>
    <?php echo $__env->make('partials.pwa-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="icon" href="<?php echo e(\App\Support\Brand::faviconUrl()); ?>">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full">
<div class="grid min-h-full lg:grid-cols-2">
    <div class="relative hidden overflow-hidden login-hero lg:flex lg:flex-col lg:justify-between lg:p-12">
        <div class="login-orb absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
        <div class="login-orb login-orb-2 absolute -bottom-32 -left-16 h-[28rem] w-[28rem] rounded-full bg-black/15"></div>
        <div class="relative flex items-center gap-3 text-white">
            <span class="inline-flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-white text-xl font-black text-brand-600"><?php if($logo = \App\Support\Brand::logoUrl()): ?><img src="<?php echo e($logo); ?>" alt="" class="h-full w-full object-contain"><?php else: ?><?php echo e(\App\Support\Brand::initial()); ?><?php endif; ?></span>
            <span class="text-2xl font-extrabold tracking-tight"><?php echo e(\App\Support\Brand::short()); ?></span>
        </div>
        <div class="login-rise login-rise-2 relative max-w-md text-white">
            <h2 class="text-4xl font-extrabold leading-tight tracking-tight"><?php echo e(\App\Support\Brand::tagline()); ?></h2>
            <p class="mt-4 text-lg text-brand-100">Filiallar, o'quvchilar, davomad, to'lovlar va hisobotlar — barchasi bir joyda.</p>
        </div>
        <p class="relative text-sm text-brand-200">© <?php echo e(date('Y')); ?> <?php echo e(\App\Support\Brand::name()); ?></p>
    </div>

    <div class="flex items-center justify-center bg-white p-6 dark:bg-ink-950 sm:p-12">
        <div class="login-rise w-full max-w-sm">
            <?php if (isset($component)) { $__componentOriginal282477a66be4db050d8fe3eab27ef4c6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal282477a66be4db050d8fe3eab27ef4c6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-mark','data' => ['class' => 'mb-8 lg:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-mark'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-8 lg:hidden']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal282477a66be4db050d8fe3eab27ef4c6)): ?>
<?php $attributes = $__attributesOriginal282477a66be4db050d8fe3eab27ef4c6; ?>
<?php unset($__attributesOriginal282477a66be4db050d8fe3eab27ef4c6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal282477a66be4db050d8fe3eab27ef4c6)): ?>
<?php $component = $__componentOriginal282477a66be4db050d8fe3eab27ef4c6; ?>
<?php unset($__componentOriginal282477a66be4db050d8fe3eab27ef4c6); ?>
<?php endif; ?>

            <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">Tizimga kirish</h1>
            <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-400">Login (yoki telefon raqami) va parolingizni kiriting.</p>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="login-rise login-rise-3 mt-8 space-y-5">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'login','label' => 'Login, email yoki telefon','autocomplete' => 'username','required' => true,'autofocus' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'login','label' => 'Login, email yoki telefon','autocomplete' => 'username','required' => true,'autofocus' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'password','type' => 'password','label' => 'Parol','autocomplete' => 'current-password','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password','type' => 'password','label' => 'Parol','autocomplete' => 'current-password','required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>

                <label class="flex items-center gap-2 text-sm text-ink-600 dark:text-ink-300">
                    <input type="checkbox" name="remember" value="1" class="checkbox"> Meni eslab qol
                </label>

                <button type="submit" class="btn-primary w-full py-3">Kirish</button>
                <a href="<?php echo e(route('password.forgot')); ?>" class="block text-center text-sm text-ink-500 hover:text-brand-600">Parolni unutdingizmi?</a>
            </form>
            <?php echo $__env->make('partials.legal-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php echo $__env->make('partials.cookie-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/auth/login.blade.php ENDPATH**/ ?>