<?php $__env->startSection('title', 'Telegram bot'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Telegram bot','subtitle' => 'Parolni tiklash, zaxira va /status buyruqlari']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Telegram bot','subtitle' => 'Parolni tiklash, zaxira va /status buyruqlari']); ?>
         <?php $__env->slot('actions', null, []); ?> <a href="<?php echo e(route('backups.index')); ?>" class="btn-secondary">Zaxira nusxalar</a> <?php $__env->endSlot(); ?>
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

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card card-body space-y-4">
            <h2 class="text-base font-semibold">Bot</h2>
            <?php if($configured && $bot): ?>
                <div class="text-sm">Ulangan: <b><?php echo e('@'.($bot['username'] ?? '')); ?></b> <span class="badge-green">faol</span></div>
            <?php elseif($configured): ?>
                <div class="text-sm text-brand-600">Token saqlangan, lekin Telegramga ulanib bo'lmadi.</div>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('telegram.token')); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'token','type' => 'password','label' => 'Bot tokeni (@BotFather)','autocomplete' => 'off','hint' => 'Shifrlangan holda saqlanadi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'token','type' => 'password','label' => 'Bot tokeni (@BotFather)','autocomplete' => 'off','hint' => 'Shifrlangan holda saqlanadi']); ?>
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
                <button class="btn-primary">Tokenni saqlash</button>
            </form>

            <?php if($configured): ?>
                <div class="border-t border-ink-100 pt-4 dark:border-ink-800">
                    <div class="text-sm">Webhook: <b><?php echo e($hook['url'] ?? "o'rnatilmagan"); ?></b></div>
                    <?php if(! empty($hook['last_error_message'])): ?><div class="mt-1 text-xs text-brand-600">Oxirgi xato: <?php echo e($hook['last_error_message']); ?></div><?php endif; ?>
                    <div class="mt-1 text-xs text-ink-500">Kerakli manzil: <?php echo e($webhookUrl); ?></div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <form method="POST" action="<?php echo e(route('telegram.webhook.set')); ?>"><?php echo csrf_field(); ?><button class="btn-secondary">Webhook o'rnatish</button></form>
                        <form method="POST" action="<?php echo e(route('telegram.webhook.delete')); ?>" data-confirm="Webhook o'chirilsa, bot buyruqlari ishlamaydi."><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn-secondary">Webhook o'chirish</button></form>
                        <form method="POST" action="<?php echo e(route('telegram.test')); ?>"><?php echo csrf_field(); ?><button class="btn-secondary">Sinov xabari</button></form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card card-body space-y-4">
            <h2 class="text-base font-semibold">Admin ID lar</h2>
            <p class="text-xs text-ink-500">Faqat shu ID lar /status va /backup buyruqlarini ishlata oladi; zaxira va sAdmin parol kodi ham faqat ularga yuboriladi. O'z ID ngizni botdagi /id buyrug'i bilan bilib oling.</p>
            <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between text-sm"><span><b><?php echo e($a['id']); ?></b> <?php echo e($a['label']); ?></span>
                    <form method="POST" action="<?php echo e(route('telegram.admins.remove', $a['id'])); ?>" data-confirm="Admin ID o'chirilsinmi?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="text-xs font-semibold text-brand-600">O'chirish</button></form></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink-500">Hali Admin ID yo'q.</p>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('telegram.admins.add')); ?>" class="flex flex-wrap items-end gap-3">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'chat_id','label' => 'Telegram ID','class' => 'w-44','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chat_id','label' => 'Telegram ID','class' => 'w-44','required' => true]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'label','label' => 'Izoh','class' => 'w-44']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'label','label' => 'Izoh','class' => 'w-44']); ?>
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
                <button class="btn-secondary">Qo'shish</button>
            </form>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-ink-100 p-4 text-base font-semibold dark:border-ink-800">Telegramga ulangan foydalanuvchilar</div>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>F.I.O</th><th>Lavozim</th><th>Telegram</th><th>Ulangan</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $linked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr><td><?php echo e($u->name); ?></td><td><?php echo e($u->role->label()); ?></td><td><?php echo e($u->telegram_username ? '@'.$u->telegram_username : $u->telegram_chat_id); ?></td><td><?php echo e($u->telegram_linked_at?->format('d.m.Y H:i')); ?></td>
                    <td class="text-right"><form method="POST" action="<?php echo e(route('telegram.unlink', $u->id)); ?>" data-confirm="Telegram uzilsinmi?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="text-xs font-semibold text-brand-600">Uzish</button></form></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="py-8 text-center text-ink-500">Hali hech kim ulanmagan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table></div>
        <div class="p-3"><?php echo e($linked->links()); ?></div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-ink-100 p-4 text-base font-semibold dark:border-ink-800">Parol tiklash jurnali</div>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Vaqt</th><th>Foydalanuvchi</th><th>Kiritilgan raqam</th><th>Kanal</th><th>Hodisa</th><th>IP</th><th>Izoh</th></tr></thead>
            <tbody>
            <?php $labels = ['requested' => "So'rov", 'code_sent' => 'Kod yuborildi', 'failed_send' => 'Yuborilmadi', 'code_wrong' => "Noto'g'ri kod", 'reset_done' => 'Parol tiklandi', 'blocked' => 'Cheklov']; ?>
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="whitespace-nowrap"><?php echo e($l->created_at->format('d.m.Y H:i')); ?></td>
                    <td><?php echo e($l->user?->name ?? '—'); ?></td><td><?php echo e($l->phone_entered); ?></td><td><?php echo e($l->channel ?? '—'); ?></td>
                    <td><span class="<?php echo e(in_array($l->event, ['code_wrong', 'failed_send', 'blocked']) ? 'badge-red' : ($l->event === 'reset_done' ? 'badge-green' : 'badge-gray')); ?>"><?php echo e($labels[$l->event] ?? $l->event); ?></span></td>
                    <td class="text-xs"><?php echo e($l->ip); ?></td><td class="text-xs text-ink-500"><?php echo e($l->note); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="py-8 text-center text-ink-500">Hali urinish bo'lmagan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table></div>
        <div class="p-3"><?php echo e($logs->links()); ?></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/telegram/index.blade.php ENDPATH**/ ?>