<?php $__env->startSection('title', 'Tizim holati'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $fail = collect($checks)->where('status', 'fail')->count();
        $warn = collect($checks)->where('status', 'warn')->count();
        $overall = $fail ? 'fail' : ($warn ? 'warn' : 'ok');
        $tone = ['ok' => 'badge-green', 'warn' => 'badge-amber', 'fail' => 'badge-red'];
        $name = ['ok' => 'Yaxshi', 'warn' => 'Diqqat', 'fail' => 'Muammo'];
        $dot = ['ok' => 'bg-emerald-500', 'warn' => 'bg-amber-500', 'fail' => 'bg-rose-500'];
    ?>

    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Tizim holati','subtitle' => 'Server, navbat, zaxira, xizmatlar, xavfsizlik va ma\'lumotlar yaxlitligi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tizim holati','subtitle' => 'Server, navbat, zaxira, xizmatlar, xavfsizlik va ma\'lumotlar yaxlitligi']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

    <div class="card mb-4 p-4 flex flex-wrap items-center gap-4">
        <span class="relative flex h-4 w-4">
            <?php if($overall !== 'ok'): ?><span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-60 <?php echo e($dot[$overall]); ?>"></span><?php endif; ?>
            <span class="relative inline-flex h-4 w-4 rounded-full <?php echo e($dot[$overall]); ?>"></span>
        </span>
        <div class="min-w-0 flex-1">
            <div class="text-lg font-semibold text-ink-900 dark:text-white">
                <?php echo e(['ok' => 'Hammasi joyida', 'warn' => "Ba'zi narsalarga e'tibor bering", 'fail' => 'Zudlik bilan tuzatish kerak'][$overall]); ?>

            </div>
            <div class="text-sm text-ink-500 dark:text-ink-300"><?php echo e($fail); ?> ta muammo · <?php echo e($warn); ?> ta ogohlantirish · <?php echo e(count($checks) - $fail - $warn); ?> ta yaxshi</div>
        </div>
        <?php if(auth()->user()->isSuperAdmin()): ?>
            <form method="POST" action="<?php echo e(route('system-status.run')); ?>"><?php echo csrf_field(); ?> <button class="btn-secondary btn-sm">Hozir tekshirish</button></form>
            <form method="POST" action="<?php echo e(route('system-status.backup')); ?>" data-confirm="Zaxira nusxa hozir olinsinmi? Bir necha daqiqa vaqt olishi mumkin.">
                <?php echo csrf_field(); ?> <button class="btn-secondary btn-sm">Hozir zaxira olish</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <?php $__currentLoopData = $groupLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(! isset($groups[$key])) continue; ?>
            <div class="card overflow-hidden">
                <div class="border-b border-ink-100 px-4 py-3 text-sm font-semibold text-ink-900 dark:border-ink-700 dark:text-white"><?php echo e($label); ?></div>
                <ul class="divide-y divide-ink-100 dark:divide-ink-700">
                    <?php $__currentLoopData = $groups[$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-start gap-3 px-4 py-3">
                            <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full <?php echo e($dot[$c['status']]); ?>"></span>
                            <div class="min-w-0 flex-1">
                                <div class="font-medium text-ink-900 dark:text-white"><?php echo e($c['label']); ?></div>
                                <div class="text-sm text-ink-600 dark:text-ink-300"><?php echo e($c['detail']); ?></div>
                            </div>
                            <span class="<?php echo e($tone[$c['status']]); ?> whitespace-nowrap"><?php echo e($name[$c['status']]); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="card mt-4 p-4">
        <div class="mb-3 text-sm font-semibold text-ink-900 dark:text-white">Tekshiruvlar tarixi (oxirgi <?php echo e($history->count()); ?> ta)</div>
        <?php if($history->isEmpty()): ?>
            <p class="text-sm text-ink-500">Hali tarix yo'q. Cron ishga tushgach har 15 daqiqada yoziladi, yoki sAdmin tekshiruvni qo'lda ishga tushiradi.</p>
        <?php else: ?>
            <div class="flex h-12 items-end gap-1" role="img" aria-label="Tekshiruvlar tarixi">
                <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex-1 rounded-sm <?php echo e($dot[$h->status]); ?>" style="height: <?php echo e($h->status === 'ok' ? 35 : ($h->status === 'warn' ? 65 : 100)); ?>%"
                         title="<?php echo e($h->created_at->timezone('Asia/Tashkent')->format('d.m H:i')); ?> — <?php echo e($h->fail_count); ?> muammo, <?php echo e($h->warn_count); ?> ogohlantirish"></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-1 flex justify-between text-xs text-ink-500">
                <span><?php echo e($history->first()->created_at->timezone('Asia/Tashkent')->format('d.m H:i')); ?></span>
                <span><?php echo e($history->last()->created_at->timezone('Asia/Tashkent')->format('d.m H:i')); ?></span>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/system-status/index.blade.php ENDPATH**/ ?>