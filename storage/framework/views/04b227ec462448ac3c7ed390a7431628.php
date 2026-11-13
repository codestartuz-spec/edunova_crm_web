<?php $__env->startSection('title', $lead->name); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $lead->name,'subtitle' => \App\Support\Format::prettyPhone($lead->phone)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($lead->name),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Support\Format::prettyPhone($lead->phone))]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <span class="<?php echo e(['new' => 'badge-blue', 'in_progress' => 'badge-amber', 'converted' => 'badge-green', 'cancelled' => 'badge-gray'][$lead->status]); ?>"><?php echo e($lead->status_label); ?></span>
            <a href="<?php echo e(route('leads.index')); ?>" class="btn-secondary">Orqaga</a>
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

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <?php $ai = $lead->ai_analysis; ?>
            <div class="card card-body space-y-3">
                <div class="flex items-center justify-between"><h3 class="text-base font-semibold text-ink-900 dark:text-white">AI fikri</h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('leads.manage')): ?><form method="POST" action="<?php echo e(route('leads.analyze', $lead)); ?>"><?php echo csrf_field(); ?><button class="btn-ghost btn-sm"><?php echo e($ai ? 'Qayta tahlil' : 'Tahlil qilish'); ?></button></form><?php endif; ?></div>
                <?php $__errorArgs = ['ai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php if($ai): ?>
                    <div class="flex items-center gap-2"><span class="<?php echo e(['yuqori' => 'badge-green', "o'rta" => 'badge-amber', 'past' => 'badge-gray'][$ai['priority']] ?? 'badge-gray'); ?>">Ustuvorlik: <?php echo e($ai['priority']); ?></span><span class="text-sm font-semibold"><?php echo e($ai['probability']); ?>% qabul ehtimoli</span></div>
                    <div class="h-2 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800"><div class="h-full bg-brand-500" style="width: <?php echo e($ai['probability']); ?>%"></div></div>
                    <p class="text-sm"><?php echo e($ai['summary']); ?></p>
                    <?php if($ai['next_step']): ?><p class="text-sm"><b>Keyingi qadam:</b> <?php echo e($ai['next_step']); ?></p><?php endif; ?>
                    <?php if($ai['talking_points']): ?><ul class="list-disc space-y-1 pl-5 text-sm"><?php $__currentLoopData = $ai['talking_points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($t); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul><?php endif; ?>
                    <?php if($ai['risks']): ?><p class="text-xs text-brand-600">Xavflar: <?php echo e(implode('; ', $ai['risks'])); ?></p><?php endif; ?>
                    <p class="text-xs text-ink-400"><?php echo e($lead->ai_analyzed_at?->format('d.m.Y H:i')); ?> · AI xulosasi tavsiya xarakterida</p>
                <?php else: ?>
                    <p class="text-sm text-ink-500">Hali tahlil qilinmagan. Murojaat tushganda va izoh yozilganda AI avtomatik tahlil qiladi (navbat ishlab turgan bo'lsa), yoki «Tahlil qilish» tugmasini bosing.</p>
                <?php endif; ?>
            </div>
            <div class="card card-body">
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-ink-500">Telefon</dt><dd class="font-medium"><?php if (isset($component)) { $__componentOriginalb6152700bb2bce174521585210331945 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb6152700bb2bce174521585210331945 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.phone-copy','data' => ['value' => $lead->phone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('phone-copy'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($lead->phone)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb6152700bb2bce174521585210331945)): ?>
<?php $attributes = $__attributesOriginalb6152700bb2bce174521585210331945; ?>
<?php unset($__attributesOriginalb6152700bb2bce174521585210331945); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb6152700bb2bce174521585210331945)): ?>
<?php $component = $__componentOriginalb6152700bb2bce174521585210331945; ?>
<?php unset($__componentOriginalb6152700bb2bce174521585210331945); ?>
<?php endif; ?></dd></div>
                    <div><dt class="text-ink-500">Manba</dt><dd class="font-medium"><?php echo e($lead->source?->name ?? '—'); ?></dd></div>
                    <div><dt class="text-ink-500">Qo'shimcha telefon</dt><dd class="font-medium"><?php echo e($lead->phone2 ?? '—'); ?></dd></div>
                    <div><dt class="text-ink-500">Manzil</dt><dd class="font-medium"><?php echo e($lead->address ?? '—'); ?></dd></div>
                    <div><dt class="text-ink-500">Kelgan vaqti</dt><dd class="font-medium"><?php echo e($lead->created_at->format('d.m.Y H:i')); ?></dd></div>
                    <?php if($lead->student): ?><div><dt class="text-ink-500">O'quvchi</dt><dd><a class="font-semibold text-brand-600" href="<?php echo e(route('students.show', $lead->student)); ?>"><?php echo e($lead->student->name); ?></a></dd></div><?php endif; ?>
                </dl>
            </div>

            <?php if($lead->isOpen()): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('leads.manage')): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('students.create')): ?>
                        <form method="POST" action="<?php echo e(route('leads.convert', $lead)); ?>" class="card card-body space-y-4">
                            <?php echo csrf_field(); ?>
                            <h3 class="text-base font-semibold text-ink-900 dark:text-white">O'quvchi sifatida ro'yxatga olish</h3>
                            <?php if($existing): ?>
                                <div class="rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
                                    Bu raqam bilan o'quvchi bor: <a class="font-semibold underline" href="<?php echo e(route('students.show', $existing)); ?>"><?php echo e($existing->name); ?></a>.
                                    Bir filialda bir telefon raqami bitta o'quvchiga tegishli, shuning uchun murojaat shu o'quvchiga bog'lanadi.
                                </div>
                            <?php endif; ?>
                            <div><label class="label" for="about">Eslatma</label><textarea id="about" name="about" rows="2" class="input"></textarea></div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('groups.members')): ?>
                                <?php if($groups->isNotEmpty()): ?>
                                    <div><label class="label" for="group_id">Guruhga qo'shish (ixtiyoriy)</label>
                                        <select id="group_id" name="group_id" class="input">
                                            <option value="">Qo'shmaslik</option>
                                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>"><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <button class="btn-primary w-full"><?php echo e($existing ? "Mavjud o'quvchiga bog'lash" : "Ro'yxatga olish"); ?></button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('leads.cancel', $lead)); ?>" class="card card-body space-y-3" onsubmit="return confirm('Murojaat bekor qilinsinmi?')">
                        <?php echo csrf_field(); ?>
                        <input name="reason" placeholder="Bekor qilish sababi" class="input">
                        <button class="btn-secondary w-full">Bekor qilish</button>
                    </form>
                <?php endif; ?>
            <?php elseif($lead->status === 'cancelled'): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('leads.manage')): ?><form method="POST" action="<?php echo e(route('leads.reopen', $lead)); ?>"><?php echo csrf_field(); ?><button class="btn-secondary w-full">Qayta ochish</button></form><?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="card card-body lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold text-ink-900 dark:text-white">Izohlar tarixi</h2>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('leads.manage')): ?>
                <form method="POST" action="<?php echo e(route('leads.note', $lead)); ?>" class="mb-5 flex flex-col gap-2 sm:flex-row">
                    <?php echo csrf_field(); ?>
                    <input name="body" placeholder="Suhbat natijasi, keyingi qadam..." class="input" required>
                    <button class="btn-primary">Qo'shish</button>
                </form>
                <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text -mt-3 mb-3"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php endif; ?>
            <ol class="space-y-3 border-l border-ink-200 pl-4 dark:border-ink-700">
                <?php $__currentLoopData = $lead->notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="relative text-sm <?php echo e($n->type === 'ai' ? 'rounded-xl bg-brand-50 p-3 dark:bg-brand-950/30' : ''); ?>">
                        <span class="absolute -left-[21px] top-1.5 h-2.5 w-2.5 rounded-full <?php echo e($n->type === 'ai' ? 'bg-amber-500' : 'bg-brand-500'); ?>"></span>
                        <div><?php echo e($n->body); ?></div>
                        <div class="text-xs text-ink-400"><?php echo e($n->created_at->format('d.m.Y H:i')); ?> · <?php echo e($n->type === 'ai' ? 'AI' : ($n->user?->name ?? 'Sayt')); ?></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/leads/show.blade.php ENDPATH**/ ?>