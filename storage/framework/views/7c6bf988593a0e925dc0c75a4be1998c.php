<?php $__env->startSection('title', 'Rozilik jurnali'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Rozilik jurnali','subtitle' => 'Kim, qachon va qaysi versiyadagi hujjatni qabul qilgani']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Rozilik jurnali','subtitle' => 'Kim, qachon va qaysi versiyadagi hujjatni qabul qilgani']); ?>
         <?php $__env->slot('actions', null, []); ?> 
        <a href="<?php echo e(route('legal.admin')); ?>" class="btn-secondary btn-sm">← Hujjatlar</a>
        <a href="<?php echo e(route('legal.journal.export', $filters)); ?>" class="btn-secondary btn-sm">CSV yuklab olish</a>
         <?php $__env->endSlot(); ?>
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

    <form method="GET" class="card card-body mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1.4fr_auto_auto_auto] lg:items-end">
        <div>
            <label class="label">Hujjat</label>
            <select name="type" class="input">
                <option value="">Barchasi</option>
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t); ?>" <?php if(($filters['type'] ?? '') === $t): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div><label class="label">Foydalanuvchi yoki IP</label><input type="search" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" class="input" placeholder="Ism, IP manzil"></div>
        <div><label class="label">Dan</label><input type="date" name="from" value="<?php echo e($filters['from'] ?? ''); ?>" class="input"></div>
        <div><label class="label">Gacha</label><input type="date" name="to" value="<?php echo e($filters['to'] ?? ''); ?>" class="input"></div>
        <button class="btn-secondary justify-center">Ko'rsatish</button>
    </form>

    <div class="card overflow-hidden">
        
        <div class="divide-y divide-ink-100 dark:divide-ink-800 md:hidden">
            <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="space-y-1 p-4 text-sm">
                    <div class="flex items-center justify-between gap-2"><b class="text-ink-900 dark:text-white"><?php echo e($r->user_name); ?></b><span class="badge-gray">v<?php echo e($r->version); ?></span></div>
                    <div class="text-ink-600 dark:text-ink-300"><?php echo e($types[$r->document_type] ?? $r->document_type); ?></div>
                    <div class="text-xs text-ink-400"><?php echo e($r->accepted_at->timezone('Asia/Tashkent')->format('d.m.Y H:i')); ?> · <?php echo e($r->ip); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="p-6 text-center text-sm text-ink-400">Hali yozuv yo'q.</p>
            <?php endif; ?>
        </div>
        <div class="table-wrap hidden md:block">
            <table class="table">
                <thead><tr><th>Sana</th><th>Foydalanuvchi</th><th>Hujjat</th><th>Versiya</th><th>IP</th><th>Qurilma</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="whitespace-nowrap"><?php echo e($r->accepted_at->timezone('Asia/Tashkent')->format('d.m.Y H:i')); ?></td>
                        <td class="font-medium text-ink-900 dark:text-white"><?php echo e($r->user_name); ?> <span class="text-xs font-normal text-ink-400"><?php echo e($r->user_role); ?></span></td>
                        <td><?php echo e($types[$r->document_type] ?? $r->document_type); ?></td>
                        <td><span class="badge-gray">v<?php echo e($r->version); ?></span></td>
                        <td class="whitespace-nowrap text-xs text-ink-500"><?php echo e($r->ip); ?></td>
                        <td class="max-w-xs truncate text-xs text-ink-400" title="<?php echo e($r->user_agent); ?>"><?php echo e($r->user_agent); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="py-8 text-center text-ink-400">Hali yozuv yo'q.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4"><?php echo e($rows->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/legal/journal.blade.php ENDPATH**/ ?>