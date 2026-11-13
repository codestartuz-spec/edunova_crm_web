<?php $__env->startSection('title', 'Sertifikatlar'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Sertifikatlar','subtitle' => 'Berilgan sertifikatlar, bekor qilish va qayta berish']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Sertifikatlar','subtitle' => 'Berilgan sertifikatlar, bekor qilish va qayta berish']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('certificates.manage')): ?><a href="<?php echo e(route('certificates.settings')); ?>" class="btn-secondary btn-sm">Sozlamalar</a><?php endif; ?>
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

    <form method="GET" class="card card-body mb-4 grid gap-3 sm:grid-cols-[1fr_auto_auto] sm:items-end">
        <div><label class="label">Qidirish</label><input type="search" name="q" value="<?php echo e($q); ?>" class="input" placeholder="Ism, raqam yoki kurs"></div>
        <div>
            <label class="label">Holati</label>
            <select name="status" class="input">
                <option value="">Barchasi</option>
                <option value="issued" <?php if($status === 'issued'): echo 'selected'; endif; ?>>Amalda</option>
                <option value="revoked" <?php if($status === 'revoked'): echo 'selected'; endif; ?>>Bekor qilingan</option>
            </select>
        </div>
        <button class="btn-secondary justify-center">Ko'rsatish</button>
    </form>

    <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $certs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card card-body flex flex-wrap items-center justify-between gap-3 text-sm" x-data="{ revoking: false }">
                <div class="min-w-0">
                    <div class="font-semibold text-ink-900 dark:text-white"><?php echo e($c->student_name); ?>

                        <span class="<?php echo e($c->isRevoked() ? 'badge-red' : 'badge-green'); ?> ml-2"><?php echo e($c->isRevoked() ? 'Bekor qilingan' : 'Amalda'); ?></span>
                    </div>
                    <div class="text-xs text-ink-500"><?php echo e($c->number); ?> · <?php echo e($c->course_name); ?><?php if($c->level_name): ?> / <?php echo e($c->level_name); ?><?php endif; ?> · <?php echo e($c->issued_on->format('d.m.Y')); ?><?php if($c->branch): ?> · <?php echo e($c->branch->name); ?><?php endif; ?></div>
                    <?php if($c->isRevoked()): ?><div class="text-xs text-red-600">Sabab: <?php echo e($c->revoke_reason); ?></div><?php endif; ?>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <?php if (! ($c->isRevoked())): ?>
                        <a href="<?php echo e(route('certificates.pdf', $c)); ?>" target="_blank" class="btn-secondary btn-sm">PDF</a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('certificates.manage')): ?>
                            <form method="POST" action="<?php echo e(route('certificates.reissue', $c)); ?>" data-confirm="Eskisi bekor qilinib, yangi raqam bilan beriladi." data-confirm-ok="Qayta berish"><?php echo csrf_field(); ?><button class="btn-ghost btn-sm">Qayta berish</button></form>
                            <button type="button" class="btn-ghost btn-sm text-red-600" @click="revoking = !revoking">Bekor qilish</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('certificates.manage')): ?>
                    <form x-show="revoking" x-cloak method="POST" action="<?php echo e(route('certificates.revoke', $c)); ?>" class="flex w-full flex-wrap gap-2">
                        <?php echo csrf_field(); ?>
                        <input name="reason" required maxlength="200" class="input flex-1" placeholder="Bekor qilish sababi">
                        <button class="btn-primary">Tasdiqlash</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="card card-body text-center text-sm text-ink-400">Hali sertifikat berilmagan. Guruh yoki o'quvchi sahifasidan bering.</div>
        <?php endif; ?>
    </div>
    <div class="mt-4"><?php echo e($certs->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/certificates/index.blade.php ENDPATH**/ ?>