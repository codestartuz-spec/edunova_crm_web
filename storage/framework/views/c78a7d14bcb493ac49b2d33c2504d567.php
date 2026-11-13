<?php $__env->startSection('title', "To'lovlar"); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['icon' => 'wallet','title' => 'To\'lovlar','subtitle' => 'To\'lov qabul qilish o\'quvchi sahifasidan amalga oshiriladi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'wallet','title' => 'To\'lovlar','subtitle' => 'To\'lov qabul qilish o\'quvchi sahifasidan amalga oshiriladi']); ?>
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

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payments.view_totals')): ?>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = [["Naqt", $totals->cash, 'banknote', 'emerald'], ["Plastik", $totals->card, 'wallet', 'blue'], ["Chegirma va bonus", $totals->discounts, 'sparkles', 'amber'], ["Qaytarilgan", $totals->refunds, 'alert', 'red']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $v, $ic, $tn]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal3b387acd2c997737a257e1ec014549fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b387acd2c997737a257e1ec014549fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['label' => $l,'value' => $v,'icon' => $ic,'tone' => $tn,'unit' => 'money']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($l),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($v),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ic),'tone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tn),'unit' => 'money']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b387acd2c997737a257e1ec014549fd)): ?>
<?php $attributes = $__attributesOriginal3b387acd2c997737a257e1ec014549fd; ?>
<?php unset($__attributesOriginal3b387acd2c997737a257e1ec014549fd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b387acd2c997737a257e1ec014549fd)): ?>
<?php $component = $__componentOriginal3b387acd2c997737a257e1ec014549fd; ?>
<?php unset($__componentOriginal3b387acd2c997737a257e1ec014549fd); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <div class="card mt-6 overflow-hidden">
        <form method="GET" class="grid gap-3 filter-bar border-b border-ink-100 p-4 dark:border-ink-800 md:grid-cols-6">
            <input type="date" name="from" value="<?php echo e($from); ?>" class="input">
            <input type="date" name="to" value="<?php echo e($to); ?>" class="input">
            <select name="type" class="input"><option value="">Barcha turlar</option>
                <?php $__currentLoopData = ['payment' => "To'lov", 'discount' => 'Chegirma', 'campaign_bonus' => 'Aksiya bonusi', 'refund' => 'Qaytarildi', 'reversal' => 'Storno']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('type') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="method" class="input"><option value="">Naqt va plastik</option>
                <option value="cash" <?php if(request('method') === 'cash'): echo 'selected'; endif; ?>>Naqt</option><option value="card" <?php if(request('method') === 'card'): echo 'selected'; endif; ?>>Plastik</option>
            </select>
            <select name="created_by" class="input"><option value="">Barcha kassirlar</option>
                <?php $__currentLoopData = $cashiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('created_by') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="flex gap-2"><input name="q" value="<?php echo e(\App\Support\SafeInput::string(request('q'))); ?>" placeholder="O'quvchi" class="input"><button class="btn-secondary">Saralash</button></div>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Sana</th><th>O'quvchi</th><th>Tur</th><th>Guruh</th><th class="text-right">Summa</th><th>Kassir</th><th>Izoh</th><th></th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr data-href="<?php echo e(route('students.show', $p->student)); ?>">
                        <td class="whitespace-nowrap"><?php echo e($p->created_at->format('d.m.Y H:i')); ?></td>
                        <td><a href="<?php echo e(route('students.show', $p->student)); ?>" class="font-medium text-ink-900 hover:text-brand-600 dark:text-white"><?php echo e($p->student->name); ?></a></td>
                        <td>
                            <span class="<?php echo e(['payment' => 'badge-green', 'discount' => 'badge-amber', 'campaign_bonus' => 'badge-amber', 'refund' => 'badge-red', 'reversal' => 'badge-gray'][$p->type] ?? 'badge-gray'); ?>"><?php echo e($p->typeLabel()); ?></span>
                            <?php if($p->method): ?><span class="text-xs text-ink-400"><?php echo e($p->method->label()); ?></span><?php endif; ?>
                            <?php if($p->reversed_at): ?><span class="badge-gray text-xs">Stornolangan</span><?php endif; ?>
                            <?php if($p->refund_rejected_at): ?><span class="badge-gray text-xs">Rad etilgan</span><?php endif; ?>
                        </td>
                        <td><?php echo e($p->group?->name ?? '—'); ?></td>
                        <td class="text-right font-semibold"><?php echo e($p->type === 'refund' ? '−' : ''); ?><?php echo e(\App\Support\Format::money($p->amount, false)); ?></td>
                        <td><?php echo e($p->creator?->name); ?></td>
                        <td class="max-w-xs truncate text-ink-500" title="<?php echo e($p->description); ?>"><?php echo e($p->description); ?></td>
                        <td class="text-right"><a href="<?php echo e(route('payments.receipt', $p)); ?>" target="_blank" class="btn-ghost btn-sm">Chek</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8"><?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['icon' => 'wallet','title' => 'Bu davrda to\'lov yo\'q']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'wallet','title' => 'Bu davrda to\'lov yo\'q']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $attributes = $__attributesOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__attributesOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $component = $__componentOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__componentOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($payments->links()); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/payments/index.blade.php ENDPATH**/ ?>