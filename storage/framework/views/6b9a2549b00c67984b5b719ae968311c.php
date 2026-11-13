<?php $__env->startSection('title', 'Ruxsatlar'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $staff->name.' — ruxsatlar','subtitle' => $staff->role->label().' · '.($staff->branch?->name ?? '')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($staff->name.' — ruxsatlar'),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($staff->role->label().' · '.($staff->branch?->name ?? ''))]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e($back ?? route('staff.index')); ?>" class="btn-secondary">Orqaga</a>
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
    <?php echo $__env->make('staff._person-tabs', ['person' => $staff, 'active' => 'permissions'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if (! (auth()->user()->isSuperAdmin())): ?>
        <p class="mb-5 rounded-xl bg-ink-100 px-4 py-3 text-sm text-ink-600 dark:bg-ink-800 dark:text-ink-300">
            Siz faqat o'zingizda mavjud ruxsatlarni bera olasiz. Boshqa ruxsatlar o'chirilgan holda ko'rsatiladi.
        </p>
    <?php endif; ?>

    <form method="POST" action="<?php echo e($action ?? route('staff.permissions.update', $staff)); ?>" x-data="{ all(v) { this.$root.querySelectorAll('input[name=\'permissions[]\']:not(:disabled)').forEach(c => c.checked = v) }, onlyView() { this.$root.querySelectorAll('input[name=\'permissions[]\']:not(:disabled)').forEach(c => c.checked = /\.(view|stats|history)$/.test(c.value)) } }">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="mb-4 flex gap-2">
            <button type="button" class="btn-secondary btn-sm" @click="all(true)">Barchasini belgilash</button>
            <button type="button" class="btn-secondary btn-sm" @click="all(false)">Belgini olib tashlash</button>
            <?php if(! empty($presets)): ?>
                <button type="button" class="btn-secondary btn-sm" @click="onlyView()">Faqat ko'rish</button>
            <?php endif; ?>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card card-body">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-ink-500 dark:text-ink-400"><?php echo e($group['label']); ?></h3>
                    <div class="space-y-2.5">
                        <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $can = in_array($key, $grantable, true); ?>
                            <label class="flex items-start gap-3 text-sm <?php echo e($can ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'); ?>">
                                <input type="checkbox" name="permissions[]" value="<?php echo e($key); ?>" class="checkbox mt-0.5"
                                       <?php if(in_array($key, $current, true)): echo 'checked'; endif; ?> <?php if(! $can): echo 'disabled'; endif; ?>>
                                <span><?php echo e($item['label']); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6"><button class="btn-primary">Saqlash</button></div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/staff/permissions.blade.php ENDPATH**/ ?>