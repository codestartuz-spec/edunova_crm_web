<?php $__env->startSection('title', 'Direktorlar ruxsatlari'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Direktorlar uchun umumiy ruxsatlar','subtitle' => 'Bu ruxsatlar BARCHA direktorlarga birdek beriladi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Direktorlar uchun umumiy ruxsatlar','subtitle' => 'Bu ruxsatlar BARCHA direktorlarga birdek beriladi']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('directors.index')); ?>" class="btn-secondary">Orqaga</a>
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

    <div class="mb-5 space-y-2 rounded-xl bg-ink-100 px-4 py-3 text-sm text-ink-600 dark:bg-ink-800 dark:text-ink-300">
        <p>Saqlaganingizda ruxsatlar hozirgi <?php echo e($directors->count()); ?> ta direktorga (<?php echo e($directors->pluck('name')->implode(', ') ?: '—'); ?>) va keyin qo'shiladigan barcha direktorlarga qo'llanadi.</p>
        <p>Direktorlar xodimlarga faqat O'ZLARIDA bor ruxsatlarni bera oladi. Bu yerdan olib tashlangan ruxsatni direktor xodimga bera olmaydi.</p>
        <?php if (! ($configured)): ?>
            <p class="font-medium text-brand-600">Umumiy ruxsatlar hali saqlanmagan. Hozir belgilanganlar hozirgi direktorlarning hammasida mavjud bo'lgan ruxsatlar; saqlaganingizda hamma direktor shunga tenglashadi.</p>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?php echo e(route('directors.permissions.update')); ?>" x-data="{ all(v) { this.$root.querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = v) }, onlyView() { this.$root.querySelectorAll('input[name=\'permissions[]\']').forEach(c => c.checked = /\.(view|stats|history)$/.test(c.value)) } }">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="mb-4 flex gap-2">
            <button type="button" class="btn-secondary btn-sm" @click="all(true)">Barchasini belgilash</button>
            <button type="button" class="btn-secondary btn-sm" @click="all(false)">Belgini olib tashlash</button>
            <button type="button" class="btn-secondary btn-sm" @click="onlyView()">Faqat ko'rish</button>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card card-body">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400"><?php echo e($group['label']); ?></h3>
                    <div class="space-y-2.5">
                        <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex cursor-pointer items-start gap-3 text-sm">
                                <input type="checkbox" name="permissions[]" value="<?php echo e($key); ?>" class="checkbox mt-0.5" <?php if(in_array($key, $current, true)): echo 'checked'; endif; ?>>
                                <span><?php echo e($item['label']); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6"><button class="btn-primary">Saqlash va barcha direktorlarga qo'llash</button></div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/directors/permissions.blade.php ENDPATH**/ ?>