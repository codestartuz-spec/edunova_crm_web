<?php $__env->startSection('title', 'Kassa'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['icon' => 'banknote','title' => 'Kassa','subtitle' => 'Kunlik pul: talaba to\'lovlari shu yerga tushadi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'banknote','title' => 'Kassa','subtitle' => 'Kunlik pul: talaba to\'lovlari shu yerga tushadi']); ?>
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

    <?php $__errorArgs = ['payment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 p-4 text-sm text-brand-800 dark:border-brand-900 dark:bg-brand-950/40 dark:text-brand-200"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php if (isset($component)) { $__componentOriginal3b387acd2c997737a257e1ec014549fd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b387acd2c997737a257e1ec014549fd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['class' => 'money-card','label' => 'Kassada naqt','value' => $cash,'icon' => 'banknote','tone' => 'emerald','unit' => 'money']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'money-card','label' => 'Kassada naqt','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cash),'icon' => 'banknote','tone' => 'emerald','unit' => 'money']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['class' => 'money-card','label' => 'Kassada plastik','value' => $card,'icon' => 'wallet','tone' => 'blue','unit' => 'money']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'money-card','label' => 'Kassada plastik','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($card),'icon' => 'wallet','tone' => 'blue','unit' => 'money']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['class' => 'money-card','label' => 'Bugun tushgan (naqt)','value' => $today->cash,'icon' => 'sparkles','tone' => 'emerald','unit' => 'money']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'money-card','label' => 'Bugun tushgan (naqt)','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($today->cash),'icon' => 'sparkles','tone' => 'emerald','unit' => 'money']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat','data' => ['class' => 'money-card','label' => 'Bugun tushgan (plastik)','value' => $today->card,'icon' => 'sparkles','tone' => 'blue','unit' => 'money']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'money-card','label' => 'Bugun tushgan (plastik)','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($today->card),'icon' => 'sparkles','tone' => 'blue','unit' => 'money']); ?>
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

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <?php
            $canExpense = auth()->user()->can('cashbox.request');
            $canWithdraw = auth()->user()->can('cashbox.withdraw');
            $canRequest = $canExpense || $canWithdraw;
        ?>
        <?php if($canRequest): ?>
            <div x-data="confirmForm('So\'rovni tasdiqlang')" class="h-fit">
            <form method="POST" action="<?php echo e(route('cashbox.store')); ?>" x-data="{ kind: '<?php echo e(old('kind', $canExpense ? 'expense' : 'withdrawal')); ?>' }" class="card card-body action-card space-y-4">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.once','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('once'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0)): ?>
<?php $attributes = $__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0; ?>
<?php unset($__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0)): ?>
<?php $component = $__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0; ?>
<?php unset($__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0); ?>
<?php endif; ?>
                <h2 class="text-base font-semibold text-ink-900 dark:text-white">Chiqim yoki xarajat so'rovi</h2>
                <p class="text-sm text-ink-500">Pul kassadan darhol yechiladi. Admin tasdiqlaydi yoki bekor qiladi (pul qaytadi).</p>
                <?php if($canExpense && $canWithdraw): ?>
                    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'kind','label' => 'Turi','required' => true,'xModel' => 'kind']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'kind','label' => 'Turi','required' => true,'x-model' => 'kind']); ?>
                        <option value="expense">Xarajat (sarflandi)</option>
                        <option value="withdrawal">Chiqim (moliya balansiga o'tkazish)</option>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                <?php else: ?>
                    
                    <input type="hidden" name="kind" value="<?php echo e($canExpense ? 'expense' : 'withdrawal'); ?>">
                    <div class="rounded-xl bg-ink-50 px-3 py-2 text-sm text-ink-700 dark:bg-ink-800 dark:text-ink-200">
                        Turi: <b><?php echo e($canExpense ? 'Xarajat (sarflandi)' : "Chiqim (moliya balansiga o'tkazish)"); ?></b>
                    </div>
                <?php endif; ?>
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'method','label' => 'Qaysi kassadan','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'method','label' => 'Qaysi kassadan','required' => true]); ?>
                    <option value="cash">Naqt</option><option value="card">Plastik</option>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'amount','money' => true,'label' => 'Summa (so\'m)','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'amount','money' => true,'label' => 'Summa (so\'m)','required' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'description','label' => 'Izoh','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => 'Izoh','required' => true]); ?>
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
                <?php if($canExpense && $expenseCategories && $expenseCategories->isNotEmpty()): ?>
                    <div x-show="kind === 'expense'" x-cloak>
                        <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'category_id','label' => 'Xarajat turi (ixtiyoriy)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'category_id','label' => 'Xarajat turi (ixtiyoriy)']); ?>
                            <option value="">— tanlanmagan —</option>
                            <?php $__currentLoopData = $expenseCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" <?php if(old('category_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <button type="button" @click="review($event)" class="btn-primary w-full">So'rov yaratish</button>
            </form>
            <?php if (isset($component)) { $__componentOriginalc33aa68616ce26a241d042f315770849 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc33aa68616ce26a241d042f315770849 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.money-confirm','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('money-confirm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc33aa68616ce26a241d042f315770849)): ?>
<?php $attributes = $__attributesOriginalc33aa68616ce26a241d042f315770849; ?>
<?php unset($__attributesOriginalc33aa68616ce26a241d042f315770849); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc33aa68616ce26a241d042f315770849)): ?>
<?php $component = $__componentOriginalc33aa68616ce26a241d042f315770849; ?>
<?php unset($__componentOriginalc33aa68616ce26a241d042f315770849); ?>
<?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="space-y-6 <?php echo e((auth()->user()->can('cashbox.request') || auth()->user()->can('cashbox.withdraw')) ? 'lg:col-span-2' : 'lg:col-span-3'); ?>">
            <div class="card overflow-hidden">
                <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">Tasdiq kutayotgan so'rovlar</h2>
                <div class="table-wrap mt-3">
                    <table class="table table-anim">
                        <thead><tr><th>Sana</th><th>Turi</th><th class="text-right">Summa</th><th>Izoh</th><th>So'radi</th><th></th></tr></thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="whitespace-nowrap"><?php echo e($r->created_at->format('d.m.Y H:i')); ?></td>
                                <td><?php echo e($r->kindLabel()); ?> <span class="text-xs text-ink-400"><?php echo e($r->method->label()); ?><?php echo e($r->category ? ' · '.$r->category->name : ''); ?></span></td>
                                <td class="text-right font-semibold"><span data-countup="<?php echo e((int) $r->amount); ?>" data-unit="num"><?php echo e(\App\Support\Format::money($r->amount, false)); ?></span></td>
                                <td class="max-w-xs truncate" title="<?php echo e($r->description); ?>"><?php echo e($r->description); ?></td>
                                <td><?php echo e($r->requester?->name); ?></td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cashbox.approve')): ?>
                                            <form method="POST" action="<?php echo e(route('cashbox.approve', $r)); ?>"><?php echo csrf_field(); ?><button class="btn-primary btn-sm">Tasdiqlash</button></form>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->can('cashbox.approve') || (auth()->user()->can($r->permission()) && $r->requested_by === auth()->id())): ?>
                                            <form method="POST" action="<?php echo e(route('cashbox.cancel', $r)); ?>" onsubmit="return confirm('Bekor qilinsinmi? Pul kassaga qaytadi.')"><?php echo csrf_field(); ?><button class="btn-ghost btn-sm">Bekor</button></form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="py-8 text-center text-ink-500"><?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['icon' => 'check','title' => 'Kutayotgan so\'rov yo\'q']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'check','title' => 'Kutayotgan so\'rov yo\'q']); ?>
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
            </div>

            <?php if($refunds->isNotEmpty()): ?>
                <div class="card overflow-hidden">
                    <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">Tasdiqlanmagan qaytarishlar</h2>
                    <div class="table-wrap mt-3">
                        <table class="table table-anim">
                            <thead><tr><th>Sana</th><th>O'quvchi</th><th class="text-right">Summa</th><th>Sabab</th><th>Kassir</th><th></th></tr></thead>
                            <tbody>
                            <?php $__currentLoopData = $refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="whitespace-nowrap"><?php echo e($p->created_at->format('d.m.Y H:i')); ?></td>
                                    <td><?php echo e($p->student->name); ?></td>
                                    <td class="text-right font-semibold"><span data-countup="<?php echo e((int) $p->amount); ?>" data-unit="num"><?php echo e(\App\Support\Format::money($p->amount, false)); ?></span> <span class="text-xs text-ink-400"><?php echo e($p->method->label()); ?></span></td>
                                    <td class="max-w-xs truncate" title="<?php echo e($p->description); ?>"><?php echo e($p->description); ?></td><td><?php echo e($p->creator?->name); ?></td>
                                    <td class="text-right">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cashbox.approve')): ?>
                                            <div class="flex justify-end gap-1">
                                                <form method="POST" action="<?php echo e(route('cashbox.refund-confirm', $p)); ?>"><?php echo csrf_field(); ?><button class="btn-secondary btn-sm">Tasdiqlash</button></form>
                                                <details class="text-left">
                                                    <summary class="btn-ghost btn-sm inline-block cursor-pointer list-none">Rad etish</summary>
                                                    <form method="POST" action="<?php echo e(route('cashbox.refund-reject', $p)); ?>" class="mt-2 w-64 space-y-2 rounded-xl border border-ink-200 bg-ink-50 p-3 dark:border-ink-700 dark:bg-ink-800/60">
                                                        <?php echo csrf_field(); ?>
                                                        <label class="label text-xs" for="reject-reason-<?php echo e($p->id); ?>">Rad etish sababi</label>
                                                        <input id="reject-reason-<?php echo e($p->id); ?>" name="reason" type="text" required maxlength="255" placeholder="Nega rad etilmoqda?" class="input">
                                                        <button class="btn-primary btn-sm w-full">Rad etish va pulni qaytarish</button>
                                                    </form>
                                                </details>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($history !== null): ?>
            <div class="card overflow-hidden">
                <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">So'nggi 30 kun</h2>
                <div class="table-wrap mt-3">
                    <table class="table table-anim">
                        <thead><tr><th>Ko'rib chiqildi</th><th>Turi</th><th class="text-right">Summa</th><th>Izoh</th><th>Holat</th><th>Kim</th></tr></thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="whitespace-nowrap"><?php echo e($r->decided_at?->format('d.m.Y H:i')); ?></td>
                                <td><?php echo e($r->kindLabel()); ?> <span class="text-xs text-ink-400"><?php echo e($r->method->label()); ?><?php echo e($r->category ? ' · '.$r->category->name : ''); ?></span></td>
                                <td class="text-right"><span data-countup="<?php echo e((int) $r->amount); ?>" data-unit="num"><?php echo e(\App\Support\Format::money($r->amount, false)); ?></span></td>
                                <td class="max-w-xs truncate" title="<?php echo e($r->description); ?>"><?php echo e($r->description); ?></td>
                                <td><span class="<?php echo e($r->status === 'approved' ? 'badge-green' : 'badge-gray'); ?>"><?php echo e($r->status === 'approved' ? 'Tasdiqlangan' : 'Bekor qilingan'); ?></span></td>
                                <td class="text-xs text-ink-500"><?php echo e($r->requester?->name); ?> → <?php echo e($r->decider?->name); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="py-8 text-center text-ink-500"><?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['icon' => 'log','title' => 'Yozuv yo\'q']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'log','title' => 'Yozuv yo\'q']); ?>
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
            </div>
            <?php endif; ?>

            <?php if($closings !== null): ?>
            <div class="card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5 sm:px-6">
                    <h2 class="text-lg font-semibold text-ink-900 dark:text-white">Kassa smenasi</h2>
                    <details class="text-sm">
                        <summary class="btn-secondary btn-sm inline-block cursor-pointer list-none">Smenani yopish</summary>
                        <form method="POST" action="<?php echo e(route('cashbox.close')); ?>" onsubmit="return confirm('Smena yopilsinmi? Farq faqat yoziladi, kassa balansi o\'zgarmaydi.')"
                              class="mt-2 w-72 space-y-2 rounded-xl border border-ink-200 bg-ink-50 p-3 text-left dark:border-ink-700 dark:bg-ink-800/60">
                            <?php echo csrf_field(); ?>
                            <?php if (isset($component)) { $__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.once','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('once'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0)): ?>
<?php $attributes = $__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0; ?>
<?php unset($__attributesOriginalae8554ecbfd667a0d86b9c0e4411a6a0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0)): ?>
<?php $component = $__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0; ?>
<?php unset($__componentOriginalae8554ecbfd667a0d86b9c0e4411a6a0); ?>
<?php endif; ?>
                            <p class="text-xs text-ink-500">Kutilgan (jurnal bo'yicha) naqt: <b><?php echo e(\App\Support\Format::money($cash)); ?></b></p>
                            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'actual_cash','money' => true,'label' => 'Sanalgan haqiqiy naqt','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'actual_cash','money' => true,'label' => 'Sanalgan haqiqiy naqt','required' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'note','label' => 'Izoh (ixtiyoriy)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'note','label' => 'Izoh (ixtiyoriy)']); ?>
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
                            <button class="btn-primary btn-sm w-full">Yopish</button>
                        </form>
                    </details>
                </div>
                <p class="px-5 pb-2 text-xs text-ink-400 sm:px-6">Farq faqat shu ro'yxatga yoziladi - kassa balansi qo'lda o'zgartirilmaydi.</p>
                <div class="table-wrap mt-3">
                    <table class="table table-anim">
                        <thead><tr><th>Sana</th><th class="text-right">Kutilgan</th><th class="text-right">Haqiqiy</th><th class="text-right">Farq</th><th>Izoh</th><th>Kim</th></tr></thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $closings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="whitespace-nowrap"><?php echo e($c->created_at->format('d.m.Y H:i')); ?></td>
                                <td class="text-right"><span data-countup="<?php echo e((int) $c->expected_cash); ?>" data-unit="num"><?php echo e(\App\Support\Format::money($c->expected_cash, false)); ?></span></td>
                                <td class="text-right"><span data-countup="<?php echo e((int) $c->actual_cash); ?>" data-unit="num"><?php echo e(\App\Support\Format::money($c->actual_cash, false)); ?></span></td>
                                <td class="text-right font-semibold <?php echo e($c->difference === 0 ? '' : ($c->difference > 0 ? 'text-emerald-600' : 'text-brand-600')); ?>">
                                    <?php echo e($c->difference > 0 ? '+' : ''); ?><span data-countup="<?php echo e((int) $c->difference); ?>" data-unit="num"><?php echo e(\App\Support\Format::money($c->difference, false)); ?></span>
                                </td>
                                <td class="max-w-xs truncate" title="<?php echo e($c->note); ?>"><?php echo e($c->note); ?></td>
                                <td class="text-xs text-ink-500"><?php echo e($c->closer?->name); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="py-8 text-center text-ink-500"><?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['icon' => 'archive','title' => 'Hali smena yopilmagan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'archive','title' => 'Hali smena yopilmagan']); ?>
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
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/cashbox/index.blade.php ENDPATH**/ ?>