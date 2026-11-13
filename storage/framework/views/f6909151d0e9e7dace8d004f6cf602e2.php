<?php $__env->startSection('title', 'Direktorlar'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Direktorlar','subtitle' => 'Barcha filiallarni ko\'ra oladigan, lekin faqat siz belgilagan ruxsatlar doirasida ishlaydigan foydalanuvchilar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Direktorlar','subtitle' => 'Barcha filiallarni ko\'ra oladigan, lekin faqat siz belgilagan ruxsatlar doirasida ishlaydigan foydalanuvchilar']); ?>
         <?php $__env->slot('actions', null, []); ?> <a href="<?php echo e(route('directors.create')); ?>" class="btn-primary"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Direktor qo'shish</a> <?php $__env->endSlot(); ?>
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
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>F.I.O</th><th>Login</th><th>Telefon</th><th>Ruxsatlar</th><th>Holat</th><th>Oxirgi kirish</th><th class="text-right">Amallar</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $directors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="font-medium text-ink-900 dark:text-white"><?php echo e($d->name); ?></td>
                        <td><?php echo e($d->username); ?></td>
                        <td><?php echo e($d->phone ?? '—'); ?></td>
                        <td>
                            <a href="<?php echo e(route('directors.permissions.edit', $d->id)); ?>" class="badge-blue"><?php echo e($counts[$d->id] ?? 0); ?> / <?php echo e($total); ?></a>
                        </td>
                        <td><span class="<?php echo e($d->isActive() ? 'badge-green' : 'badge-gray'); ?>"><?php echo e($d->status->label()); ?></span></td>
                        <td class="text-xs text-ink-500"><?php echo e($d->last_login_at?->format('d.m.Y H:i') ?? '—'); ?></td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <a href="<?php echo e(route('directors.edit', $d->id)); ?>" class="btn-ghost btn-sm">Tahrirlash</a>
                                <a href="<?php echo e(route('directors.permissions.edit', $d->id)); ?>" class="btn-ghost btn-sm">Ruxsatlar</a>
                                <form method="POST" action="<?php echo e(route('directors.toggle', $d->id)); ?>"><?php echo csrf_field(); ?>
                                    <button class="btn-ghost btn-sm"><?php echo e($d->isActive() ? 'Bloklash' : 'Faollashtirish'); ?></button>
                                </form>
                                <form method="POST" action="<?php echo e(route('directors.destroy', $d->id)); ?>" onsubmit="return confirm('Bu direktor olib tashlansinmi? U tizimga kira olmaydi.')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn-ghost btn-sm text-brand-600">Olib tashlash</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="py-10 text-center text-ink-500">Hali direktor qo'shilmagan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <p class="mt-4 text-xs text-ink-500">Tizimda yagona sAdmin bor: u barcha ruxsatlarga ega. Direktorga faqat «Ruxsatlar» sahifasida belgilangan imkoniyatlar ochiladi. Olib tashlangan direktorning tarixi (jurnal, to'lovlar) saqlanib qoladi.</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/directors/index.blade.php ENDPATH**/ ?>