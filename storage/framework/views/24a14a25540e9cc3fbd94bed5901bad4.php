<?php $__env->startSection('title', 'Zaxira nusxalar'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Zaxira nusxalar','subtitle' => 'Har kuni 01:00 da baza zaxirasi — parollangan ZIP (AES-256), Telegramga faqat Admin ID larga yuboriladi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Zaxira nusxalar','subtitle' => 'Har kuni 01:00 da baza zaxirasi — parollangan ZIP (AES-256), Telegramga faqat Admin ID larga yuboriladi']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <form method="POST" action="<?php echo e(route('backups.run')); ?>"><?php echo csrf_field(); ?><button class="btn-primary">Hozir zaxira olish</button></form>
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

    <?php if (! ($telegramReady)): ?>
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">Telegram sozlanmagan yoki Admin ID yo'q — zaxira faqat serverda saqlanadi. <a class="font-semibold underline" href="<?php echo e(route('telegram.index')); ?>">Telegram bot sozlamalari</a></div>
    <?php endif; ?>

    <div class="card card-body mb-6 max-w-xl space-y-3">
        <h2 class="text-base font-semibold">ZIP paroli</h2>
        <?php if($password): ?><div class="rounded-lg bg-ink-100 p-3 font-mono text-sm dark:bg-ink-800"><?php echo e($password); ?></div><?php endif; ?>
        <form method="POST" action="<?php echo e(route('backups.password')); ?>" class="flex flex-wrap items-end gap-3"><?php echo csrf_field(); ?>
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'password','type' => 'password','label' => 'Hisob parolingiz','class' => 'w-56','autocomplete' => 'current-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password','type' => 'password','label' => 'Hisob parolingiz','class' => 'w-56','autocomplete' => 'current-password']); ?>
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
            <button class="btn-secondary">Parolni ko'rsatish</button></form>
        <p class="text-xs text-ink-500">Tiklash: <code>php artisan app:restore storage/app/backups/encrypted/&lt;fayl&gt;.zip</code> (veb orqali tiklash yo'q).</p>
    </div>

    <div class="card overflow-hidden"><div class="table-wrap"><table class="table">
        <thead><tr><th>Sana</th><th>Holat</th><th>Turi</th><th class="text-right">Hajmi</th><th>SHA-256</th><th>Davomiyligi</th><th>Telegram</th><th class="text-right">Amallar</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="whitespace-nowrap"><?php echo e($b->created_at->format('d.m.Y H:i')); ?></td>
                <td><span class="<?php echo e($b->status === 'ok' ? 'badge-green' : ($b->status === 'failed' ? 'badge-red' : 'badge-gray')); ?>"><?php echo e(['ok' => 'Tayyor', 'failed' => 'Xato', 'running' => 'Bajarilmoqda'][$b->status] ?? $b->status); ?></span><?php if($b->error): ?><div class="max-w-xs text-xs text-brand-600"><?php echo e($b->error); ?></div><?php endif; ?></td>
                <td><?php echo e($b->trigger === 'manual' ? "Qo'lda" : 'Jadval'); ?></td>
                <td class="text-right"><?php echo e($b->size ? round($b->size / 1048576, 2).' MB' : '—'); ?></td>
                <td class="font-mono text-xs"><?php echo e($b->sha256 ? substr($b->sha256, 0, 12).'…' : '—'); ?></td>
                <td><?php echo e($b->duration_ms ? round($b->duration_ms / 1000, 1).' s' : '—'); ?></td>
                <td class="text-xs"><?php $__empty_2 = true; $__currentLoopData = $b->telegram ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat => $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><div><?php echo e($chat); ?>: <span class="<?php echo e($res === 'ok' ? 'text-emerald-600' : 'text-brand-600'); ?>"><?php echo e($res === 'ok' ? 'yuborildi' : $res); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?> — <?php endif; ?></td>
                <td class="whitespace-nowrap text-right">
                    <?php if($b->status === 'ok' && $b->file): ?>
                        <form method="POST" action="<?php echo e(route('backups.resend', $b)); ?>" class="inline"><?php echo csrf_field(); ?><button class="text-xs font-semibold text-brand-600">Qayta yuborish</button></form>
                        <form method="POST" action="<?php echo e(route('backups.download', $b)); ?>" class="inline" onsubmit="this.password.value = prompt('Yuklab olish uchun hisob parolingizni kiriting') || ''; return this.password.value !== ''"><?php echo csrf_field(); ?><input type="hidden" name="password"><button class="ml-2 text-xs font-semibold text-brand-600">Yuklab olish</button></form>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('backups.destroy', $b)); ?>" class="ml-2 inline" data-confirm="Zaxira o'chirilsinmi?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="text-xs font-semibold text-ink-500">O'chirish</button></form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="py-10 text-center text-ink-500">Hali zaxira olinmagan.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div></div>
    <div class="mt-4"><?php echo e($backups->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/backups/index.blade.php ENDPATH**/ ?>