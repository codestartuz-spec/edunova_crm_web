<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rozilik · Edunova CRM</title>
    <link rel="icon" href="<?php echo e(asset('favicon.svg')); ?>" type="image/svg+xml">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-full bg-ink-50 dark:bg-ink-950">
<div class="mx-auto flex min-h-full max-w-2xl flex-col justify-center px-4 py-6 sm:py-10"
     x-data="{ n: <?php echo e($pending->count()); ?>, ok: {}, get all() { return Object.values(this.ok).filter(Boolean).length === this.n }, toggleAll(v) { <?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> this.ok['<?php echo e($d->type); ?>'] = v; <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> } }">
    <div class="rounded-3xl bg-white p-5 shadow-xl ring-1 ring-black/5 dark:bg-ink-900 dark:ring-white/10 sm:p-8">
        <div class="mb-5 flex items-center gap-3"><?php if (isset($component)) { $__componentOriginal987d96ec78ed1cf75b349e2e5981978f = $component; } ?>
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
<?php endif; ?><span class="text-xl font-extrabold tracking-tight text-ink-900 dark:text-white">Edunova</span></div>

        <h1 class="text-xl font-bold tracking-tight text-ink-900 dark:text-white sm:text-2xl">
            <?php echo e($updated ? 'Hujjatlar yangilandi' : 'Davom etishdan oldin'); ?>

        </h1>
        <p class="mt-1.5 text-sm leading-relaxed text-ink-500 dark:text-ink-400">
            <?php echo e($updated
                ? "Quyidagi hujjatlarning yangi versiyasi e'lon qilindi. Tizimdan foydalanishda davom etish uchun ularni o'qib chiqib, rozilik bering."
                : "Tizimdan foydalanish uchun quyidagi hujjatlar bilan tanishib, ularni qabul qilishingiz kerak. Roziligingiz sanasi va hujjat versiyasi jurnalga yoziladi."); ?>

        </p>

        <?php if($errors->any()): ?>
            <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-700 dark:bg-brand-950/60 dark:text-brand-300" role="alert"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('legal.accept')); ?>" class="mt-5">
            <?php echo csrf_field(); ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-2xl border border-ink-100 dark:border-ink-800" x-data="{ open: false }">
                        <div class="flex items-center gap-3 p-3.5">
                            <label class="flex min-w-0 flex-1 cursor-pointer items-start gap-3 text-sm text-ink-700 dark:text-ink-200">
                                <input type="checkbox" name="agree[]" value="<?php echo e($d->type); ?>" x-model="ok['<?php echo e($d->type); ?>']" class="checkbox mt-0.5">
                                <span class="min-w-0"><b class="text-ink-900 dark:text-white"><?php echo e($d->title); ?></b> bilan tanishdim va roziman <span class="text-xs text-ink-400">(v<?php echo e($d->version); ?>)</span></span>
                            </label>
                            <button type="button" @click="open = !open" class="shrink-0 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-950/60" :aria-expanded="open">
                                <span x-text="open ? 'Yopish' : 'O\'qish'"></span>
                            </button>
                        </div>
                        <div x-show="open" x-cloak x-transition.opacity class="border-t border-ink-100 dark:border-ink-800">
                            <div class="legal-prose max-h-72 overflow-y-auto p-4 text-ink-700 dark:text-ink-200"><?php echo \App\Services\LegalService::render($d->body); ?></div>
                            <div class="border-t border-ink-100 px-4 py-2 text-right dark:border-ink-800"><a href="<?php echo e(route('legal.show', $d->type)); ?>" target="_blank" rel="noopener" class="text-xs font-semibold text-brand-600 hover:underline">Alohida sahifada ochish ↗</a></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($pending->count() > 1): ?>
                <label class="mt-4 flex cursor-pointer items-center gap-3 text-sm font-semibold text-ink-700 dark:text-ink-200">
                    <input type="checkbox" class="checkbox" :checked="all" @change="toggleAll($event.target.checked)"> Hammasiga roziman
                </label>
            <?php endif; ?>

            <div class="mt-6 grid gap-3 sm:grid-cols-[1fr_auto]">
                <button type="submit" class="btn-primary justify-center py-3 disabled:cursor-not-allowed disabled:opacity-50" :disabled="!all">Qabul qilaman va davom etaman</button>
            </div>
        </form>

        <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-4 text-center">
            <?php echo csrf_field(); ?>
            <button class="text-sm text-ink-500 hover:text-brand-600">Rozi emasman — tizimdan chiqish</button>
        </form>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/legal/consent.blade.php ENDPATH**/ ?>