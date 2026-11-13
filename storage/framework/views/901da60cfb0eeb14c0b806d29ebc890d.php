<?php $__env->startSection('title', $def['title']); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $def['title']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($def['title'])]); ?>
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
    <?php if($catalog !== 'courses'): ?> <?php echo $__env->make('partials.settings-open', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>

    <?php ($canManage = auth()->user()->can($def['permission'])); ?>
    <div class="grid gap-6 lg:grid-cols-3">
        <?php if($canManage): ?>
        <form method="POST" action="<?php echo e(route('catalog.store', $catalog)); ?>" class="card card-body h-fit space-y-4">
            <?php echo csrf_field(); ?>
            <h2 class="text-base font-semibold text-ink-900 dark:text-white">Yangi: <?php echo e(mb_strtolower($def['singular'])); ?></h2>
            <?php $__currentLoopData = $def['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => $name,'label' => $f['label'],'type' => $f['type'] === 'money' ? 'text' : $f['type'],'money' => $f['type'] === 'money','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f['label']),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f['type'] === 'money' ? 'text' : $f['type']),'money' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($f['type'] === 'money'),'required' => true]); ?>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <button class="btn-primary w-full"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Qo'shish</button>
        </form>
        <?php endif; ?>

        <div class="card overflow-hidden <?php echo e($canManage ? 'lg:col-span-2' : 'lg:col-span-3'); ?>">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        <?php $__currentLoopData = $def['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th><?php echo e($f['label']); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th>Holat</th><th class="text-right">Amallar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr x-data="{ edit: false }" class="<?php echo e($item->is_active ? '' : 'opacity-60'); ?>">
                            <?php $__currentLoopData = $def['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <span x-show="!edit">
                                        <?php if($f['type'] === 'money'): ?><?php echo e(\App\Support\Format::money($item->$name)); ?>

                                        <?php elseif($f['type'] === 'time'): ?><?php echo e(substr($item->$name, 0, 5)); ?>

                                        <?php elseif($f['type'] === 'date'): ?><?php echo e($item->$name->format('d.m.Y')); ?>

                                        <?php else: ?><span class="font-medium text-ink-900 dark:text-white"><?php echo e($item->$name); ?></span><?php endif; ?>
                                    </span>
                                    <?php if($canManage): ?>
                                    <input x-show="edit" x-cloak form="edit-<?php echo e($item->id); ?>" name="<?php echo e($name); ?>" type="<?php echo e($f['type'] === 'money' ? 'text' : $f['type']); ?>" <?php if($f['type'] === 'money'): ?> inputmode="numeric" data-money <?php endif; ?>
                                           value="<?php echo e($f['type'] === 'time' ? substr($item->$name, 0, 5) : ($f['type'] === 'date' ? $item->$name->format('Y-m-d') : ($f['type'] === 'money' ? number_format($item->$name, 0, '', ' ') : $item->$name))); ?>" class="input py-1.5" required>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td><span class="<?php echo e($item->is_active ? 'badge-green' : 'badge-gray'); ?>"><?php echo e($item->is_active ? 'Faol' : 'Faol emas'); ?></span></td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <?php if($catalog === 'courses'): ?><a href="<?php echo e(route('courses.show', $item->id)); ?>" class="btn-secondary btn-sm">Materiallar</a><?php endif; ?>
                                    <?php if($canManage): ?>
                                    <button type="button" x-show="!edit" class="btn-ghost btn-sm" @click="edit = true"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit','class' => 'h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit','class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></button>
                                    <button x-show="edit" x-cloak form="edit-<?php echo e($item->id); ?>" class="btn-primary btn-sm">Saqlash</button>
                                    <button type="button" x-show="edit" x-cloak class="btn-ghost btn-sm" @click="edit = false">Bekor</button>
                                    <button form="toggle-<?php echo e($item->id); ?>" class="btn-ghost btn-sm"><?php echo e($item->is_active ? 'Faolsizlantirish' : 'Faollashtirish'); ?></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="<?php echo e(count($def['fields']) + 2); ?>" class="py-10 text-center text-ink-500">Hali kiritilmagan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($canManage): ?>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <form id="edit-<?php echo e($item->id); ?>" method="POST" action="<?php echo e(route('catalog.update', [$catalog, $item->id])); ?>" class="hidden"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?></form>
                <form id="toggle-<?php echo e($item->id); ?>" method="POST" action="<?php echo e(route('catalog.toggle', [$catalog, $item->id])); ?>" class="hidden"><?php echo csrf_field(); ?></form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
<?php if($catalog !== 'courses'): ?> <?php echo $__env->make('partials.settings-close', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/catalog/index.blade.php ENDPATH**/ ?>