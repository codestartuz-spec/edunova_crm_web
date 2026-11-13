<?php $__env->startSection('title', 'SMS'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="{ tab: '<?php echo e($errors->has('message') ? 'send' : (collect($errors->keys())->contains(fn ($k) => str_starts_with($k, 'templates.')) ? 'settings' : 'history')); ?>' }">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'SMS','subtitle' => 'USB modem va Eskiz orqali yuboriladigan xabarlar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'SMS','subtitle' => 'USB modem va Eskiz orqali yuboriladigan xabarlar']); ?>
        <?php if($branch || auth()->user()->can('sms.gateway')): ?>
             <?php $__env->slot('actions', null, []); ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sms.gateway')): ?><a href="<?php echo e(route('sms.gateway')); ?>" class="btn-secondary">USB modem shlyuzi</a><?php endif; ?> <?php if($branch): ?><span class="<?php echo e($branch->sms_enabled ? 'badge-green' : 'badge-gray'); ?>">SMS <?php echo e($branch->sms_enabled ? 'yoqilgan' : "o'chiq"); ?></span><?php endif; ?> <?php $__env->endSlot(); ?>
        <?php endif; ?>
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

    <?php if($branch && ! $branch->sms_enabled): ?>
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
            Bu filialda SMS yuborish <b>o'chirilgan</b>. Yoqish uchun «Sozlamalar» bo'limida belgilang (Eskiz akkaunti sozlangan bo'lishi kerak).
        </div>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-3">
        <?php $__currentLoopData = [['Yuborildi (30 kun)', $stats['sent'] ?? 0, 'text-emerald-600'], ['Navbatda', $stats['queued'] ?? 0, 'text-amber-600'], ['Xato', $stats['failed'] ?? 0, 'text-brand-600']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $v, $c]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-body"><div class="text-sm text-ink-500"><?php echo e($l); ?></div><div class="mt-1 text-2xl font-bold <?php echo e($c); ?>"><?php echo e($v); ?></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="mt-6 flex gap-1 overflow-x-auto rounded-xl bg-ink-100 p-1 dark:bg-ink-800">
        <?php $__currentLoopData = array_filter(['history' => 'Tarix', 'send' => $canSend ? 'Ommaviy yuborish' : null, 'settings' => $canManage ? 'Sozlamalar' : null]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" @click="tab = '<?php echo e($k); ?>'" :class="tab === '<?php echo e($k); ?>' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-600 dark:text-ink-300'" class="whitespace-nowrap rounded-lg px-3.5 py-2 text-sm font-medium"><?php echo e($label); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div x-show="tab === 'history'" class="card mt-4 overflow-hidden">
        <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 dark:border-ink-800 sm:grid-cols-3">
            <input name="q" value="<?php echo e(\App\Support\SafeInput::string(request('q'))); ?>" placeholder="Telefon raqami" class="input">
            <select name="status" class="input" onchange="this.form.submit()"><option value="">Barcha holatlar</option>
                <?php $__currentLoopData = ['sent' => 'Yuborilgan', 'queued' => 'Navbatda', 'failed' => 'Xato']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('status') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="btn-secondary">Qidirish</button>
        </form>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Sana</th><th>Qabul qiluvchi</th><th>Xabar</th><th>Holat</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="whitespace-nowrap"><?php echo e($m->created_at->format('d.m.Y H:i')); ?></td>
                    <td><?php echo e($m->recipient?->name ?? '—'); ?><div class="text-xs text-ink-400"><?php echo e(\App\Support\Format::prettyPhone($m->phone)); ?></div></td>
                    <td class="max-w-md text-sm"><?php echo e($m->message); ?></td>
                    <td><span class="<?php echo e(['sent' => 'badge-green', 'queued' => 'badge-amber', 'failed' => 'badge-red'][$m->status]); ?>"><?php echo e(['sent' => 'Yuborilgan', 'queued' => 'Navbatda', 'failed' => 'Xato'][$m->status]); ?></span>
                        <?php if($m->status === 'failed' && $m->provider_response): ?><div class="max-w-[14rem] truncate text-xs text-ink-400" title="<?php echo e($m->provider_response); ?>"><?php echo e($m->provider_response); ?></div><?php endif; ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="4" class="py-10 text-center text-ink-500">Xabar yo'q.</td></tr><?php endif; ?>
            </tbody></table></div>
        <?php echo e($messages->links()); ?>

    </div>

    <?php if($canSend): ?>
        <form x-show="tab === 'send'" x-cloak method="POST" action="<?php echo e(route('sms.bulk')); ?>" class="card card-body mt-4 max-w-2xl space-y-4" x-data="{ audience: 'debtors' }">
            <?php echo csrf_field(); ?>
            <h2 class="text-base font-semibold text-ink-900 dark:text-white">Ommaviy SMS</h2>
            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'audience','label' => 'Kimga','required' => true,'xModel' => 'audience']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'audience','label' => 'Kimga','required' => true,'x-model' => 'audience']); ?>
                <option value="debtors">Qarzdor o'quvchilarga</option><option value="group">Guruh o'quvchilariga</option><option value="all">Barcha faol o'quvchilarga</option>
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
            <div x-show="audience === 'group'" x-cloak>
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'group_id','label' => 'Guruh']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'group_id','label' => 'Guruh']); ?><option value="">Tanlang</option><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>"><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php echo $__env->renderComponent(); ?>
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
            <div>
                <label class="label" for="message">Xabar matni</label>
                <textarea id="message" name="message" rows="4" maxlength="500" class="input" required><?php echo e(\App\Support\SafeInput::string(old('message'), $templates['debt_reminder']['body'] ?? '', 500)); ?></textarea>
                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="hint">O'zgaruvchilar: <?php echo e(implode('  ', array_keys(\App\Support\SmsTemplates::PLACEHOLDERS))); ?> — har bir qabul qiluvchi uchun almashtiriladi. Bir xil raqamga bir marta yuboriladi.</p>
            </div>
            <button class="btn-primary">Tekshirishga o'tish</button>
        </form>
    <?php endif; ?>

    <?php if($canManage): ?>
        <form x-show="tab === 'settings'" x-cloak method="POST" action="<?php echo e(route('sms.settings')); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="card card-body">
                <label class="flex items-center gap-3 text-sm font-medium"><input type="checkbox" name="sms_enabled" value="1" class="checkbox" <?php if($branch->sms_enabled): echo 'checked'; endif; ?>> Bu filialda SMS yuborishni yoqish</label>
                <p class="hint">Eskiz akkaunti: <?php echo e($branch->hasOwnSmsAccount() ? "filialning o'z akkaunti" : 'umumiy akkaunt (.env)'); ?>. Filial akkauntini sAdmin «Filiallar» bo'limida kiritadi.</p>
            </div>
            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card card-body space-y-2">
                    <label class="flex items-center gap-3 text-sm font-semibold"><input type="checkbox" name="templates[<?php echo e($key); ?>][enabled]" value="1" class="checkbox" <?php if($t['enabled']): echo 'checked'; endif; ?>> <?php echo e($t['label']); ?></label>
                    <textarea name="templates[<?php echo e($key); ?>][body]" rows="2" maxlength="500" class="input" required><?php echo e($t['body']); ?></textarea>
                    <?php $__errorArgs = ["templates.$key.body"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if($key === 'debt_reminder'): ?>
                        <label class="flex items-center gap-3 border-t border-ink-100 pt-2 text-sm text-ink-600 dark:border-ink-800 dark:text-ink-300"><input type="checkbox" name="sms_auto_debt" value="1" class="checkbox" <?php if($branch->sms_auto_debt): echo 'checked'; endif; ?>> Har kuni avtomatik yuborish (soat 09:15, kuniga bir marta)</label>
                        <p class="hint">Ishlashi uchun shablon ham yoqilgan bo'lishi kerak (yuqoridagi katakcha).</p>
                    <?php elseif($key === 'absence_notice'): ?>
                        <label class="flex items-center gap-3 border-t border-ink-100 pt-2 text-sm text-ink-600 dark:border-ink-800 dark:text-ink-300"><input type="checkbox" name="sms_auto_absent" value="1" class="checkbox" <?php if($branch->sms_auto_absent): echo 'checked'; endif; ?>> Davomad olinganda avtomatik yuborish (o'quvchiga emas, «Qo'shimcha telefon» — ota-ona raqamiga)</label>
                        <p class="hint">Ishlashi uchun shablon ham yoqilgan bo'lishi kerak; o'quvchida qo'shimcha telefon ko'rsatilmagan bo'lsa, xabar yuborilmaydi.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <p class="hint">O'zgaruvchilar: <?php echo e(implode('  ', array_keys(\App\Support\SmsTemplates::PLACEHOLDERS))); ?>. Eskiz shablonlarni oldindan tasdiqlaydi — matnni Eskiz'da ruxsat etilgan ko'rinishda yozing.</p>
            <button class="btn-primary">Saqlash</button>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/sms/index.blade.php ENDPATH**/ ?>