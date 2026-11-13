<?php $__env->startSection('title', 'Shartnoma'); ?>

<?php $__env->startSection('content'); ?> 
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Shartnoma','subtitle' => 'O\'quvchi guruhga qo\'shilganda chop etiladigan shartnoma matni']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Shartnoma','subtitle' => 'O\'quvchi guruhga qo\'shilganda chop etiladigan shartnoma matni']); ?>
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

    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200 mb-6">
        Bu — avtomatik to'ldiriladigan <b>namuna</b> shartnoma. Imzolashdan oldin uni litsenziyalangan yuristga ko'rsatib, O'zbekiston qonunchiligiga moslashtirishingiz tavsiya etiladi.
    </div>

    <form method="POST" action="<?php echo e(route('contract-settings.update')); ?>" class="card card-body max-w-3xl space-y-5">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="grid gap-5 sm:grid-cols-3">
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'stir','label' => 'Filial STIR','value' => $branch->stir,'maxlength' => '30']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'stir','label' => 'Filial STIR','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->stir),'maxlength' => '30']); ?>
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
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'director_name','label' => 'Direktor F.I.O.','value' => $branch->director_name,'maxlength' => '120']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'director_name','label' => 'Direktor F.I.O.','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->director_name),'maxlength' => '120']); ?>
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
            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'director_title','label' => 'Direktor lavozimi','value' => $branch->director_title,'placeholder' => 'Direktor','maxlength' => '60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'director_title','label' => 'Direktor lavozimi','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->director_title),'placeholder' => 'Direktor','maxlength' => '60']); ?>
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

        <div>
            <label class="label" for="contract_template">Shartnoma matni</label>
            <textarea id="contract_template" name="contract_template" rows="32" maxlength="20000" class="input font-mono text-xs leading-relaxed"><?php echo e(old('contract_template', $template)); ?></textarea>
            <?php $__errorArgs = ['contract_template'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php if($isDefault): ?>
                <p class="hint">Hozir standart namuna ishlatilmoqda (yuqorida ko'rsatilgan). Uni o'zingizga moslab o'zgartirib «Saqlash»ni bossangiz, filialingiz uchun shu matn ishlatiladi; o'zgartirmasangiz ham standart namuna qo'llanaveradi.</p>
            <?php endif; ?>
        </div>

        <div class="rounded-xl bg-ink-100 p-4 text-xs leading-relaxed text-ink-600 dark:bg-ink-800 dark:text-ink-300">
            <b>O'rinbosarlar (avtomatik to'ldiriladi):</b>
            <div class="mt-2 grid gap-x-4 gap-y-1 sm:grid-cols-2">
                <?php $__currentLoopData = \App\Services\ContractService::PLACEHOLDERS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><code class="rounded bg-white px-1 dark:bg-ink-900"><?php echo e($token); ?></code> — <?php echo e($desc); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="btn-primary">Saqlash</button>
            <?php if (! ($isDefault)): ?>
                <button type="submit" formaction="<?php echo e(route('contract-settings.reset')); ?>" class="btn-ghost btn-sm" onclick="return confirm('Standart namunaga qaytarilsinmi? Yozgan matningiz o\'chadi.')">Standart namunaga qaytarish</button>
            <?php endif; ?>
        </div>
    </form>
<?php echo $__env->make('partials.settings-close', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/settings/contract.blade.php ENDPATH**/ ?>