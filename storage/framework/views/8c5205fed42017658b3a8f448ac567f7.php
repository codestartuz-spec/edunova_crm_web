<?php $__env->startSection('title', $group->name); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $group->name,'subtitle' => 'Nom, kurs, o\'qituvchi, xona, dars vaqti va kunlari, boshlanish sanasi, darslar soni, narx va stavkalar tahrirlanadi.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group->name),'subtitle' => 'Nom, kurs, o\'qituvchi, xona, dars vaqti va kunlari, boshlanish sanasi, darslar soni, narx va stavkalar tahrirlanadi.']); ?>
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

    <?php if($groupStatus === \App\Models\Group::FINISHED): ?>
        <div class="mb-6 max-w-3xl rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
            Guruh <b>yakunlangan</b>: xona, dars vaqti, dars kunlari, boshlanish sanasi va darslar soni o'zgartirilmaydi. Nom, kurs, o'qituvchi, stavkalar va narx tahrirlanadi.
        </div>
    <?php elseif($lockedCount > 0): ?>
        <div class="mb-6 max-w-3xl rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
            O'tgan yoki davomad olingan <b><?php echo e($lockedCount); ?> ta dars</b> (<?php echo e($lockedUntil?->format('d.m.Y')); ?> gacha) o'zgarmaydi. Boshlanish sanasi o'zgarmaydi. Xona, vaqt yoki kunlar o'zgarsa, faqat <b>bugungi (davomad olinmagan) va kelgusi darslar</b> yangilanadi; o'tgan darslar eski vaqt va xonasida qoladi.
        </div>
    <?php else: ?>
        <div class="mb-6 max-w-3xl rounded-xl border border-ink-200 bg-ink-50 p-4 text-sm text-ink-600 dark:border-ink-800 dark:bg-ink-900 dark:text-ink-300">
            Dars hali boshlanmagan: jadvalning barcha parametrlarini o'zgartirish mumkin (boshlanish sanasi faqat bugun yoki keyingi kunlarga), darslar yangidan tuziladi. Xona va o'qituvchi bandligi tekshiriladi.
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('groups.update', $group)); ?>" class="max-w-3xl space-y-6">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="card card-body grid gap-5 sm:grid-cols-2">
            <?php echo $__env->make('groups._fields', ['withSchedule' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div class="flex gap-2">
            <button class="btn-primary">Saqlash</button>
            <a href="<?php echo e(route('groups.show', $group)); ?>" class="btn-secondary">Bekor qilish</a>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/groups/edit.blade.php ENDPATH**/ ?>