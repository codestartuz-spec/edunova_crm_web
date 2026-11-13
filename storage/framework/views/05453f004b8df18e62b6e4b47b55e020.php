<?php $__env->startSection('title', 'Harakatlar jurnali'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Harakatlar jurnali','subtitle' => 'Kim, qachon, nimani o\'zgartirgani']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Harakatlar jurnali','subtitle' => 'Kim, qachon, nimani o\'zgartirgani']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if($scopeBranch): ?>
                <span class="badge-blue">Filial: <?php echo e($scopeBranch->name); ?></span>
            <?php elseif($allMode): ?>
                <span class="badge-gray">Barcha filiallar</span>
            <?php endif; ?>
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

    <div class="card overflow-hidden">
        <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 dark:border-ink-800 sm:grid-cols-2 <?php echo e($allMode ? 'lg:grid-cols-6' : 'lg:grid-cols-5'); ?>">
            <?php if($allMode): ?>
                <select name="branch" class="input" aria-label="Filial" onchange="this.form.submit()">
                    <option value="">Barcha filiallar</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($b->id); ?>" <?php if($branchFilter === (string) $b->id): echo 'selected'; endif; ?>><?php echo e($b->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <option value="none" <?php if($branchFilter === 'none'): echo 'selected'; endif; ?>>Filialsiz (tizim)</option>
                </select>
            <?php endif; ?>
            <input name="user" value="<?php echo e(\App\Support\SafeInput::string(request('user'))); ?>" placeholder="Foydalanuvchi" class="input">
            <input name="action" value="<?php echo e(\App\Support\SafeInput::string(request('action'))); ?>" placeholder="Harakat (masalan: auth, branch)" class="input">
            <input type="date" name="from" value="<?php echo e(\App\Support\SafeInput::date(request('from'))); ?>" class="input">
            <input type="date" name="to" value="<?php echo e(\App\Support\SafeInput::date(request('to'))); ?>" class="input">
            <button class="btn-secondary">Saralash</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Vaqt</th><th>Foydalanuvchi</th><?php if($allMode): ?><th>Filial</th><?php endif; ?><th>Harakat</th><th>Izoh</th><th>IP</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="whitespace-nowrap"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                        <td><?php echo e($log->user?->name ?? '—'); ?></td>
                        <?php if($allMode): ?><td><?php echo e($log->branch?->name ?? '—'); ?></td><?php endif; ?>
                        <td><span class="badge-gray"><?php echo e($log->action); ?></span></td>
                        <td><?php echo e($log->description); ?></td>
                        <td class="text-xs text-ink-400"><?php echo e($log->ip_address); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="<?php echo e($allMode ? 6 : 5); ?>" class="py-12 text-center text-ink-500">Yozuv topilmadi.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($logs->links()); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/staff/audit.blade.php ENDPATH**/ ?>