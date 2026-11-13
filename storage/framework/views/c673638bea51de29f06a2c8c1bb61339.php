<?php $__env->startSection('title', 'Bosh sahifa'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['icon' => 'home','title' => 'Salom, '.auth()->user()->name,'subtitle' => $currentBranch ? $currentBranch->name.' filiali' : (auth()->user()->isGlobal() ? 'Barcha filiallar bo\'yicha umumiy ko\'rinish' : '')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'home','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Salom, '.auth()->user()->name),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currentBranch ? $currentBranch->name.' filiali' : (auth()->user()->isGlobal() ? 'Barcha filiallar bo\'yicha umumiy ko\'rinish' : ''))]); ?>
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

    <?php if(auth()->user()->needsPasswordReminder()): ?>
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
            Parolingiz <?php echo e(auth()->user()->password_changed_at->diffInDays(now())); ?> kundan beri o'zgarmagan. Xavfsizlik uchun vaqti-vaqti bilan yangilab turishni tavsiya qilamiz -
            <a href="<?php echo e(route('profile.edit')); ?>" class="font-semibold underline">profil sahifasida</a> o'zgartirishingiz mumkin.
        </div>
    <?php endif; ?>

    <?php if($todo): ?>
        <div class="mb-6 grid gap-4 lg:grid-cols-2">
            <?php $__currentLoopData = $todo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card card-hover card-body">
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="stat-icon !h-9 !w-9 !rounded-lg"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $group['icon'],'class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group['icon']),'class' => 'h-5 w-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
                        <h3 class="text-sm font-semibold text-ink-900 dark:text-white"><?php echo e($group['title']); ?></h3>
                        <span class="badge-gray ml-auto"><?php echo e(count($group['items'])); ?></span>
                    </div>
                    <div class="space-y-2 <?php echo e(in_array($group['key'], ['debtors', 'leads'], true) ? 'max-h-72 overflow-y-auto pr-1' : ''); ?>">
                        <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="todo-row flex items-center justify-between gap-3 rounded-lg bg-ink-50 px-3 py-2 text-sm dark:bg-ink-800/60">
                                <a href="<?php echo e($item['url']); ?>" class="min-w-0 flex-1">
                                    <div class="truncate font-medium text-ink-900 dark:text-white"><?php echo e($item['label']); ?></div>
                                    <?php if($item['meta']): ?><div class="truncate text-xs text-ink-500"><?php echo e($item['meta']); ?></div><?php endif; ?>
                                </a>
                                <form method="POST" action="<?php echo e(route('tasks.dismiss')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="task_key" value="<?php echo e($item['key']); ?>">
                                    <button class="btn-ghost btn-sm shrink-0" title="Bajardim (bugunga yashirish)">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check','class' => 'h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php if(! empty($weeklyActivity)): ?>
        <div class="card card-body mb-6">
            <h3 class="mb-3 text-sm font-semibold text-ink-900 dark:text-white">Xodimlar faolligi (so'nggi 7 kun, bajarilgan vazifalar)</h3>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $weeklyActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge-gray"><?php echo e($row['name']); ?>: <?php echo e($row['count']); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php
            $cards = [
                ['Adminlar', $staffCounts['admin'] ?? 0, 'shield'],
                ['Menejerlar', $staffCounts['manager'] ?? 0, 'users'],
                ["O'qituvchilar", $staffCounts['teacher'] ?? 0, 'user'],
                ['Operatorlar', $staffCounts['operator'] ?? 0, 'phone'],
            ];
            if (auth()->user()->isGlobal()) {
                $cards[] = ['Faol filiallar', $branches->where('status', \App\Enums\BranchStatus::Active)->count(), 'building'];
            }
        ?>
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginal3b387acd2c997737a257e1ec014549fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b387acd2c997737a257e1ec014549fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['label' => $label,'value' => $value,'icon' => $icon]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon)]); ?>
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

    <?php if($money): ?>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <?php $__currentLoopData = [["Bugungi tushum", $money['income'], 'banknote', 'emerald', 'money'], ['Bugun qaytarilgan', $money['refunds'], 'wallet', 'amber', 'money'], ['Qarzdorlar soni', $money['debtors'], 'users', 'ink', 'int'], ['Umumiy qarz', $money['debt'], 'alert', 'red', 'money']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $v, $ic, $tn, $un]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginal3b387acd2c997737a257e1ec014549fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b387acd2c997737a257e1ec014549fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['label' => $l,'value' => $v,'icon' => $ic,'tone' => $tn,'unit' => $un]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($l),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($v),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ic),'tone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tn),'unit' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($un)]); ?>
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

    <?php if($groupStats): ?>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <?php if (isset($component)) { $__componentOriginal3b387acd2c997737a257e1ec014549fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b387acd2c997737a257e1ec014549fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['label' => 'Faol guruhlar','value' => $groupStats['active']['groups'],'icon' => 'users','tone' => 'emerald','sub' => number_format($groupStats['active']['students'], 0, '', ' ').' o\'quvchi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Faol guruhlar','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupStats['active']['groups']),'icon' => 'users','tone' => 'emerald','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($groupStats['active']['students'], 0, '', ' ').' o\'quvchi')]); ?>
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
            <?php if (isset($component)) { $__componentOriginal3b387acd2c997737a257e1ec014549fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b387acd2c997737a257e1ec014549fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['label' => 'Boshlanishi kutilayotgan guruhlar','value' => $groupStats['upcoming']['groups'],'icon' => 'calendar','tone' => 'amber','sub' => number_format($groupStats['upcoming']['students'], 0, '', ' ').' o\'quvchi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Boshlanishi kutilayotgan guruhlar','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupStats['upcoming']['groups']),'icon' => 'calendar','tone' => 'amber','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($groupStats['upcoming']['students'], 0, '', ' ').' o\'quvchi')]); ?>
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
        </div>
    <?php endif; ?>

    <?php if($calendar): ?>
        <?php echo $__env->make('dashboard._calendar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
        <div class="card card-body mt-8 flex items-center gap-4 text-sm text-ink-500">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950 dark:text-brand-300"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'building','class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building','class' => 'h-5 w-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
            <p><b class="text-ink-800 dark:text-ink-100">Dars kalendari.</b> Kalendarni ko'rish uchun yuqoridagi ro'yxatdan <b>filialni tanlang</b>: shu filialda qaysi kuni, qaysi vaqtda, qaysi xonada qaysi guruhning darsi borligi ko'rinadi.</p>
        </div>
    <?php endif; ?>

    <?php if(auth()->user()->isGlobal()): ?>
        <div class="card mt-8 overflow-hidden">
            <div class="flex items-center justify-between px-5 pt-5 sm:px-6">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-ink-900 dark:text-white"><span class="page-accent !h-5"></span>Filiallar</h2>
                <a href="<?php echo e(route('branches.index')); ?>" class="btn-ghost btn-sm">Barchasi</a>
            </div>
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead><tr><th>Filial</th><th>Hodimlar</th><th>O'quvchilar</th><th>Holat</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('branches.show', $b)); ?>" class="flex items-center gap-2.5 font-medium text-ink-900 hover:text-brand-600 dark:text-white"><span class="avatar-sm" style="<?php echo e(\App\Support\BrandTheme::valid($b->brand_color ?? '') ? 'background:'.$b->brand_color.'22;color:'.$b->brand_color : ''); ?>"><?php echo e(mb_strtoupper(mb_substr($b->name, 0, 1))); ?></span><?php echo e($b->name); ?></a></td>
                            <td><?php echo e($b->staff_count); ?></td>
                            <td><?php echo e($b->students_count); ?></td>
                            <td><span class="<?php echo e($b->isActive() ? 'badge-green' : 'badge-gray'); ?>"><?php echo e($b->status->label()); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="py-10 text-center text-ink-500">Hali filial ochilmagan. <a class="font-semibold text-brand-600" href="<?php echo e(route('branches.create')); ?>">Birinchi filialni oching</a>.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/dashboard/index.blade.php ENDPATH**/ ?>