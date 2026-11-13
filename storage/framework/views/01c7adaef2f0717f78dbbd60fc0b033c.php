<?php $__env->startSection('title', 'Usluga'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Usluga (staj ustamasi)','subtitle' => 'O\'qituvchining ish stajiga qarab har bir o\'quvchi uchun qat\'iy qo\'shimcha summa']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Usluga (staj ustamasi)','subtitle' => 'O\'qituvchining ish stajiga qarab har bir o\'quvchi uchun qat\'iy qo\'shimcha summa']); ?>
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
    <?php echo $__env->make('partials.settings-open', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="mb-6 rounded-xl border border-ink-200 bg-ink-50 p-4 text-sm leading-relaxed text-ink-700 dark:border-ink-700 dark:bg-ink-800/50 dark:text-ink-200">
        <b>Qanday hisoblanadi:</b> usluga (o'quvchi) = o'quvchilar soni × «O'quvchi usluga»; usluga (bonus) = bonusli o'quvchilar soni × «Bonus usluga».
        Pog'ona o'qituvchining <b>guruh boshlangan sanadagi</b> staji (ishga olingan sanadan) bo'yicha tanlanadi. Hisob davomadga mutanosib (hozirgi ish haqi kabi).
        Guruh boshlangach summa guruhga <b>qotiriladi</b>: keyin sozlamani o'zgartirsangiz, boshlangan guruhlar o'zgarmaydi. Mavjud hisob-kitob va to'langan ish haqiga tegilmaydi.
    </div>

    <?php if(! $canEdit): ?>
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">Sozlamalarni faqat sAdmin o'zgartira oladi. Siz ko'rish rejimidasiz.</div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('usluga-settings.update')); ?>" class="card card-body max-w-3xl space-y-6">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <fieldset <?php if(! $canEdit): echo 'disabled'; endif; ?> class="space-y-6">
            <div>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'usluga_from','type' => 'date','label' => 'Usluga qo\'llanadigan sana','value' => $branch->usluga_from?->format('Y-m-d'),'hint' => 'Shu sanada va undan keyin boshlangan guruhlarga qo\'llanadi (masalan, 01.10.2026). Bo\'sh qoldirsangiz usluga o\'chiq. Undan oldin boshlangan guruhlar o\'zgarmaydi.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'usluga_from','type' => 'date','label' => 'Usluga qo\'llanadigan sana','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->usluga_from?->format('Y-m-d')),'hint' => 'Shu sanada va undan keyin boshlangan guruhlarga qo\'llanadi (masalan, 01.10.2026). Bo\'sh qoldirsangiz usluga o\'chiq. Undan oldin boshlangan guruhlar o\'zgarmaydi.']); ?>
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
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Staj (guruh boshlanganda)</th><th>O'quvchi usluga (so'm)</th><th>Bonus usluga (so'm)</th></tr></thead>
                    <tbody>
                    <?php $__currentLoopData = $tiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-medium"><?php echo e($label); ?><?php if($i === 8): ?><div class="text-xs font-normal text-ink-400">eng yuqori pog'ona</div><?php endif; ?></td>
                            <td><?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'usluga_student_'.$i,'label' => '','money' => true,'value' => $rates[$i]['student'] ?? 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('usluga_student_'.$i),'label' => '','money' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rates[$i]['student'] ?? 0)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?></td>
                            <td><?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'usluga_bonus_'.$i,'label' => '','money' => true,'value' => $rates[$i]['bonus'] ?? 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('usluga_bonus_'.$i),'label' => '','money' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rates[$i]['bonus'] ?? 0)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <?php if($canEdit): ?><div><button class="btn-primary">Saqlash</button></div><?php endif; ?>
    </form>
<?php echo $__env->make('partials.settings-close', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/settings/usluga.blade.php ENDPATH**/ ?>