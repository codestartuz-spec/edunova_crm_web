<?php $__env->startSection('title', 'Huquqiy hujjatlar'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Huquqiy hujjatlar','subtitle' => 'Maxfiylik siyosati, foydalanish shartlari va cookie siyosati. Foydalanuvchilar ularni qabul qilishi shart.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Huquqiy hujjatlar','subtitle' => 'Maxfiylik siyosati, foydalanish shartlari va cookie siyosati. Foydalanuvchilar ularni qabul qilishi shart.']); ?>
         <?php $__env->slot('actions', null, []); ?> 
        <a href="<?php echo e(route('legal.journal')); ?>" class="btn-secondary btn-sm">Rozilik jurnali</a>
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

    <div class="mb-4 grid gap-3 sm:grid-cols-3">
        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('legal.admin', ['type' => $t])); ?>" class="card card-body block transition hover:border-brand-300 <?php echo e($type === $t ? 'ring-2 ring-brand-500' : ''); ?>">
                <div class="flex items-center justify-between gap-2">
                    <div class="text-sm font-semibold text-ink-900 dark:text-white"><?php echo e($label); ?></div>
                    <span class="badge-gray">v<?php echo e($docs[$t]->version); ?></span>
                </div>
                <div class="mt-2 flex items-center gap-3 text-xs text-ink-500">
                    <span class="text-emerald-600"><b><?php echo e($stats[$t]['accepted']); ?></b> qabul qilgan</span>
                    <span class="<?php echo e($stats[$t]['pending'] ? 'text-amber-600' : ''); ?>"><b><?php echo e($stats[$t]['pending']); ?></b> kutilmoqda</span>
                </div>
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800">
                    <div class="h-full rounded-full bg-emerald-500" style="width: <?php echo e($users ? round($stats[$t]['accepted'] * 100 / $users) : 0); ?>%"></div>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="POST" action="<?php echo e(route('legal.admin.update', $type)); ?>" class="card card-body space-y-4" x-data="{ bump: false }">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-semibold text-ink-900 dark:text-white"><?php echo e($types[$type]); ?></h2>
                <p class="text-xs text-ink-500">Joriy versiya: v<?php echo e($doc->version); ?> · e'lon qilingan: <?php echo e(($doc->published_at ?? $doc->created_at)->timezone('Asia/Tashkent')->format('d.m.Y H:i')); ?></p>
            </div>
            <a href="<?php echo e(route('legal.show', $type)); ?>" target="_blank" rel="noopener" class="btn-secondary btn-sm">Foydalanuvchi ko'radigan ko'rinish ↗</a>
        </div>

        <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'title','label' => 'Sarlavha','value' => old('title', $doc->title),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'title','label' => 'Sarlavha','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('title', $doc->title)),'required' => true]); ?>
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

        <div>
            <label class="label" for="body">Matn <span class="text-brand-600">*</span></label>
            <textarea id="body" name="body" rows="22" required class="input font-mono text-[13px] leading-relaxed <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('body', $doc->body)); ?></textarea>
            <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-brand-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <p class="mt-1.5 text-xs text-ink-400">Format: <code>## Sarlavha</code> — bo'lim sarlavhasi; <code>- </code> bilan boshlangan qatorlar — ro'yxat; bo'sh qator bilan ajratilgan matn — alohida xatboshi. Matn yuridik mutaxassis tomonidan ko'rib chiqilishi tavsiya etiladi.</p>
        </div>

        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-100 p-3.5 text-sm dark:border-ink-800" :class="bump ? 'border-amber-300 bg-amber-50/60 dark:bg-amber-950/20' : ''">
            <input type="checkbox" name="new_version" value="1" x-model="bump" class="checkbox mt-0.5">
            <span>
                <b class="text-ink-900 dark:text-white">Yangi versiya sifatida e'lon qilish</b>
                <span class="mt-0.5 block text-xs text-ink-500">Belgilansa, <b>barcha foydalanuvchilar</b> keyingi kirishda ushbu hujjatni qayta qabul qilishi shart (v<?php echo e($doc->version); ?> → v<?php echo e($doc->version + 1); ?>). Belgilanmasa, faqat matn tuzatiladi (imlo xatolari uchun).</span>
            </span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button class="btn-primary" x-text="bump ? 'Yangi versiyani e\'lon qilish' : 'Saqlash'">Saqlash</button>
        </div>
    </form>

    <form method="POST" action="<?php echo e(route('legal.admin.reset', $type)); ?>" class="mt-3 text-right" data-confirm="Matn standart (tayyor) variantga qaytariladi. Sizning o'zgartirishlaringiz o'chadi." data-confirm-title="Standart matnga qaytarilsinmi?" data-confirm-ok="Qaytarish">
        <?php echo csrf_field(); ?>
        <button class="text-xs font-semibold text-ink-500 hover:text-brand-600">Standart matnga qaytarish</button>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/legal/admin.blade.php ENDPATH**/ ?>