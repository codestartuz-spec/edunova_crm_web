<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kirish · Edunova CRM</title>
    <link rel="icon" href="<?php echo e(asset('favicon.svg')); ?>" type="image/svg+xml">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full">
<div class="flex min-h-full items-center justify-center bg-white p-6 dark:bg-ink-950">
    <div class="w-full max-w-sm">
        <h1 class="text-2xl font-bold tracking-tight text-ink-900 dark:text-white">Akkauntni tanlang</h1>
        <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-400">Bu telefon raqamiga bir nechta akkaunt biriktirilgan.</p>
        <div class="mt-6 space-y-3">
            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <form method="POST" action="<?php echo e(route('login.choose')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="user_id" value="<?php echo e($a->id); ?>">
                    <button class="w-full rounded-xl border border-ink-200 p-4 text-left transition hover:border-brand-500 hover:bg-brand-50 dark:border-ink-700 dark:hover:bg-ink-800">
                        <div class="font-semibold text-ink-900 dark:text-white"><?php echo e($a->name); ?></div>
                        <div class="mt-0.5 text-xs text-ink-500"><?php echo e($a->role->label()); ?><?php echo e($a->branch ? ' · '.$a->branch->name : ''); ?></div>
                    </button>
                </form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <a href="<?php echo e(route('login')); ?>" class="mt-6 block text-center text-sm text-ink-500 hover:text-brand-600">Orqaga</a>
        <?php echo $__env->make('partials.legal-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php echo $__env->make('partials.cookie-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/auth/choose-account.blade.php ENDPATH**/ ?>