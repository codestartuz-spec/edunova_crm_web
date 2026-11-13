<?php $__env->startSection('title', "O'tgan kun davomati — ".$group->name); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'O\'tgan kun davomatini tuzatish','subtitle' => $group->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'O\'tgan kun davomatini tuzatish','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group->name)]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('groups.show', $group)); ?>" class="btn-secondary">Guruhga qaytish</a>
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

    <div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'alert','class' => 'mt-0.5 h-5 w-5 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert','class' => 'mt-0.5 h-5 w-5 shrink-0']); ?>
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
        <div>Bu bo'lim faqat <b>o'tgan</b> dars kunlari uchun (bugungi davomad guruh sahifasidan olinadi). O'zgartirish harakatlar jurnaliga yoziladi. Kelmagan o'quvchilarga SMS <b>yuborilmaydi</b> (avtomatik xabar matni "bugun" deb yozilgan, o'tgan kun uchun noto'g'ri bo'lardi).</div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="card card-body lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-ink-900 dark:text-white">Sanani tanlang</h2>
            <form method="GET" action="<?php echo e(route('attendance.past.show', $group)); ?>" class="mb-4 flex gap-2">
                <input type="date" name="date" value="<?php echo e($date); ?>" max="<?php echo e(today()->subDay()->toDateString()); ?>" class="input">
                <button class="btn-secondary shrink-0">Ko'rish</button>
            </form>
            <?php if($pastDays->isEmpty()): ?>
                <p class="text-sm text-ink-500">Bu guruhda hali o'tgan dars kuni yo'q.</p>
            <?php else: ?>
                <div class="max-h-96 space-y-1 overflow-y-auto">
                    <?php $__currentLoopData = $pastDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('attendance.past.show', ['group' => $group, 'date' => $d['date']])); ?>"
                           class="flex items-center justify-between rounded-lg px-3 py-2 text-sm <?php echo e($d['date'] === $date ? 'bg-brand-50 text-brand-700 dark:bg-brand-950/40 dark:text-brand-300' : 'hover:bg-ink-50 dark:hover:bg-ink-800'); ?>">
                            <span><?php echo e(\Carbon\Carbon::parse($d['date'])->format('d.m.Y, D')); ?></span>
                            <span class="<?php echo e($d['state'] === 'missed' ? 'badge-amber' : 'badge-green'); ?>"><?php echo e($d['state'] === 'missed' ? 'Olinmagan' : 'Olingan'); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card card-body lg:col-span-2">
            <?php if(! $isPast): ?>
                <p class="text-sm text-ink-500"><?php echo e(\Carbon\Carbon::parse($date)->format('d.m.Y')); ?> — bu sana bugun yoki kelajakda. Faqat o'tgan kunlar tuzatiladi.</p>
            <?php elseif(! $isLessonDay): ?>
                <p class="text-sm text-ink-500"><?php echo e(\Carbon\Carbon::parse($date)->format('d.m.Y')); ?> kuni bu guruhda dars bo'lmagan.</p>
            <?php else: ?>
                <form method="POST" action="<?php echo e(route('attendance.past.store', $group)); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="date" value="<?php echo e($date); ?>">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-ink-900 dark:text-white"><?php echo e(\Carbon\Carbon::parse($date)->format('d.m.Y')); ?></h2>
                        <span class="<?php echo e($wasTaken ? 'badge-green' : 'badge-amber'); ?>"><?php echo e($wasTaken ? 'Oldin olingan' : 'Olinmagan edi'); ?></span>
                    </div>
                    <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text mb-3"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['attendance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text mb-3"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ink-200 px-3.5 py-2.5 text-sm hover:bg-ink-50 dark:border-ink-700 dark:hover:bg-ink-800">
                                <input type="checkbox" name="present[]" value="<?php echo e($m->student_id); ?>" class="checkbox" <?php if($existing[$m->student_id] ?? false): echo 'checked'; endif; ?>>
                                <span class="font-medium"><?php echo e($loop->iteration); ?>. <?php echo e($m->student->name); ?></span>
                                <?php if (! (auth()->user()->role === \App\Enums\Role::Teacher)): ?>
                                    <span class="ml-auto whitespace-nowrap text-xs font-semibold <?php echo e($m->student->balance < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e(\App\Support\Format::money($m->student->balance)); ?></span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-ink-500">Guruhda faol o'quvchi yo'q.</p>
                        <?php endif; ?>
                    </div>
                    <?php if($members->isNotEmpty()): ?><div class="mt-4"><button class="btn-primary">Saqlash</button></div><?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/groups/attendance-past.blade.php ENDPATH**/ ?>