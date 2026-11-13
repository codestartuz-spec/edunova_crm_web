<?php $__env->startSection('title', $person->name); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $person->name,'subtitle' => $person->role->label().' · ish haqi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($person->name),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($person->role->label().' · ish haqi')]); ?>
         <?php $__env->slot('actions', null, []); ?> <a href="<?php echo e(route('payroll.index', ['tab' => $isTeacher ? 'teachers' : 'staff'])); ?>" class="btn-secondary">Orqaga</a> <?php $__env->endSlot(); ?>
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
    <?php echo $__env->make('staff._person-tabs', ['person' => $person, 'active' => 'payroll'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($isTeacher): ?>
        <?php
            $fmU = fn ($n) => \App\Support\Format::money($n, false);
            $missingHire = collect($accruals)->contains(fn ($a) => $a['usluga']['state'] === \App\Services\UslugaService::STATE_NO_HIRE_DATE);
        ?>
        <div class="mb-4 rounded-xl border border-ink-200 bg-white p-4 text-sm dark:border-ink-700 dark:bg-ink-900">
            <span class="text-ink-500">Ishga olingan sana:</span>
            <b><?php echo e($tenure['hired_at']?->format('d.m.Y') ?? 'kiritilmagan'); ?></b>
            <?php if($tenure['months'] !== null): ?>
                · <span class="text-ink-500">staji:</span> <b><?php echo e(intdiv($tenure['months'], 12)); ?> yil <?php echo e($tenure['months'] % 12); ?> oy</b>
                · <span class="text-ink-500">hozirgi usluga pog'onasi:</span> <b><?php echo e($tenure['tier_label']); ?></b>
            <?php endif; ?>
        </div>
        <?php if($missingHire): ?>
            <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">Bu o'qituvchining ishga olingan sanasi kiritilmagan, shuning uchun usluga hisoblanmayapti. Hodimni tahrirlash sahifasida «Ishga olingan sana»ni kiriting.</div>
        <?php endif; ?>
        <div class="card overflow-hidden">
            <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">Guruhlar bo'yicha hisob</h2>
            <p class="px-5 text-sm text-ink-500 sm:px-6">Hisoblangan = o'quvchilar × stavka + bonusli o'quvchilar × bonus. Usluga (o'quvchi) = o'quvchilar × o'quvchi usluga; usluga (bonus) = bonusli o'quvchilar × bonus usluga. Jami = hisoblangan + usluga. «Davomad bo'yicha» — o'tkazilgan darslarga mutanosib (usluga bilan birga).</p>
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead><tr><th>Guruh</th><th>O'quvchi</th><th>Bonusli</th><th>Darslar</th><th class="text-right">Hisoblangan</th><th class="text-right">Usluga (o'quvchi)</th><th class="text-right">Usluga (bonus)</th><th class="text-right">Jami</th><th class="text-right">Davomad bo'yicha</th><th class="text-right">To'langan</th><th class="text-right">Qoldiq</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $accruals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $u = $a['usluga']; $ok = $u['state'] === \App\Services\UslugaService::STATE_OK; ?>
                        <tr>
                            <td><a href="<?php echo e(route('groups.show', $a['group'])); ?>" class="font-medium hover:text-brand-600"><?php echo e($a['group']->name); ?></a><div class="text-xs text-ink-400"><?php echo e($a['group']->status_label); ?></div></td>
                            <td><?php echo e($a['students']); ?></td><td><?php echo e($a['bonus']); ?></td><td><?php echo e($a['held']); ?> / <?php echo e($a['group']->lesson_count); ?></td>
                            <td class="text-right"><?php echo e($fmU($a['accrued'])); ?></td>
                            <?php if($ok): ?>
                                <td class="text-right whitespace-nowrap"><?php echo e($a['students']); ?> × <?php echo e($fmU($u['student'])); ?> = <b><?php echo e($fmU($a['usluga_students'])); ?></b><div class="text-xs text-ink-400"><?php echo e($u['tier_label']); ?></div></td>
                                <td class="text-right whitespace-nowrap"><?php echo e($a['bonus']); ?> × <?php echo e($fmU($u['bonus'])); ?> = <b><?php echo e($fmU($a['usluga_bonus'])); ?></b></td>
                            <?php else: ?>
                                <td colspan="2" class="text-right text-xs text-ink-400">
                                    <?php if($u['state'] === \App\Services\UslugaService::STATE_NO_HIRE_DATE): ?><span class="text-amber-600">Ishga olingan sana yo'q</span>
                                    <?php elseif($u['state'] === \App\Services\UslugaService::STATE_NO_RATES): ?><span class="text-amber-600">Usluga summalari sozlanmagan</span>
                                    <?php else: ?> —<?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td class="text-right font-medium"><?php echo e($fmU($a['accrued_total'])); ?></td>
                            <td class="text-right"><?php echo e($fmU($a['accrued_by_attendance'])); ?></td>
                            <td class="text-right"><?php echo e($fmU($a['paid'])); ?></td>
                            <td class="text-right font-semibold <?php echo e($a['remaining'] > 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e($fmU($a['remaining'])); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="11" class="py-8 text-center text-ink-500">So'nggi 31 kunda guruh yo'q.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <?php if($activity): ?>
        <?php $fm = fn ($n) => \App\Support\Format::money($n, false); ?>
        <div class="card mb-6 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5 sm:px-6">
                <div>
                    <h2 class="text-lg font-semibold text-ink-900 dark:text-white">Faoliyat statistikasi</h2>
                    <p class="text-sm text-ink-500">Qabul qilgan to'lovlari (storno qilinganlarsiz), qo'shgan murojaatlari va o'quvchilari.</p>
                </div>
                <div class="flex gap-1 text-xs font-semibold">
                    <a href="<?php echo e(route('payroll.show', ['user' => $person, 'act' => 'day'])); ?>" class="rounded-lg px-3 py-1.5 <?php echo e($act === 'day' ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300'); ?>">Kunlik (30 kun)</a>
                    <a href="<?php echo e(route('payroll.show', ['user' => $person, 'act' => 'month'])); ?>" class="rounded-lg px-3 py-1.5 <?php echo e($act === 'month' ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300'); ?>">Oylik (12 oy)</a>
                </div>
            </div>
            <div class="mt-4 grid gap-3 px-5 sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
                <div class="rounded-xl bg-ink-50 p-3 dark:bg-ink-800/50"><div class="text-xs text-ink-500">Qabul qilgan summa</div><div class="text-lg font-bold"><?php echo e($fm($activity['totals']['total'])); ?></div></div>
                <div class="rounded-xl bg-ink-50 p-3 dark:bg-ink-800/50"><div class="text-xs text-ink-500">To'lovlar soni</div><div class="text-lg font-bold"><?php echo e($activity['totals']['payments']); ?></div></div>
                <div class="rounded-xl bg-ink-50 p-3 dark:bg-ink-800/50"><div class="text-xs text-ink-500">Murojaatlar (qabul qilingan)</div><div class="text-lg font-bold"><?php echo e($activity['totals']['leads']); ?> (<?php echo e($activity['totals']['converted']); ?>)</div></div>
                <div class="rounded-xl bg-ink-50 p-3 dark:bg-ink-800/50"><div class="text-xs text-ink-500">Yangi o'quvchilar</div><div class="text-lg font-bold"><?php echo e($activity['totals']['students']); ?></div></div>
                <div class="rounded-xl bg-ink-50 p-3 dark:bg-ink-800/50 sm:col-span-2"><div class="text-xs text-ink-500">Ulardan to'lov qilganlar</div><div class="text-lg font-bold"><?php echo e($activity['totals']['paid_students']); ?> ta · <?php echo e($fm($activity['totals']['paid_sum'])); ?></div></div>
            </div>
            <div class="table-wrap mt-4 max-h-96 overflow-y-auto">
                <table class="table">
                    <thead><tr><th><?php echo e($act === 'month' ? 'Oy' : 'Kun'); ?></th><th class="text-right">To'lovlar</th><th class="text-right">Naqt</th><th class="text-right">Plastik</th><th class="text-right">Jami</th><th class="text-right">Murojaat</th><th class="text-right">Qabul</th><th class="text-right">Izoh</th><th class="text-right">O'quvchi</th><th class="text-right">Shundan to'lagan</th><th class="text-right">To'lov summasi</th></tr></thead>
                    <tbody>
                    <?php $__currentLoopData = array_reverse($activity['rows']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($r['payments'] + $r['leads'] + $r['notes'] + $r['students'] === 0 ? 'text-ink-300' : ''); ?>">
                            <td class="whitespace-nowrap"><?php echo e($r['label']); ?></td><td class="text-right"><?php echo e($r['payments']); ?></td>
                            <td class="text-right"><?php echo e($fm($r['cash'])); ?></td><td class="text-right"><?php echo e($fm($r['card'])); ?></td><td class="text-right font-semibold"><?php echo e($fm($r['total'])); ?></td>
                            <td class="text-right"><?php echo e($r['leads']); ?></td><td class="text-right"><?php echo e($r['converted']); ?></td><td class="text-right"><?php echo e($r['notes']); ?></td><td class="text-right"><?php echo e($r['students']); ?></td>
                            <td class="text-right"><?php echo e($r['paid_students']); ?></td><td class="text-right"><?php echo e($fm($r['paid_sum'])); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <?php if($canPay): ?>
            <form method="POST" action="<?php echo e(route('payroll.pay', $person)); ?>" class="card card-body h-fit space-y-4">
                <?php echo csrf_field(); ?>
                <h2 class="text-base font-semibold text-ink-900 dark:text-white">Ish haqi to'lash</h2>
                <p class="text-sm text-ink-500">Moliya balansidan: naqt <?php echo e(\App\Support\Format::money($treasuryCash)); ?>, plastik <?php echo e(\App\Support\Format::money($treasuryCard)); ?></p>
                <?php if($isTeacher): ?>
                    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'group_id','label' => 'Guruh (ixtiyoriy)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'group_id','label' => 'Guruh (ixtiyoriy)']); ?>
                        <option value="">Guruhga bog'lanmagan</option>
                        <?php $__currentLoopData = $accruals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($a['group']->id); ?>"><?php echo e($a['group']->name); ?> (qoldiq <?php echo e(\App\Support\Format::money($a['remaining'], false)); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <?php endif; ?>
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'method','label' => 'To\'lov turi','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'method','label' => 'To\'lov turi','required' => true]); ?><option value="cash">Naqt</option><option value="card">Plastik</option> <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'description','label' => 'Izoh']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => 'Izoh']); ?>
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
                <p class="text-xs text-ink-400">Keyingi sahifada ma'lumotlarni tekshirib, tasdiqlaysiz — shundan keyin to'lov amalga oshadi.</p>
                <button class="btn-primary w-full">Tekshirishga o'tish</button>
            </form>
        <?php endif; ?>

        <div class="card overflow-hidden <?php echo e($canPay ? 'lg:col-span-2' : 'lg:col-span-3'); ?>">
            <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">To'lovlar tarixi</h2>
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead><tr><th>Sana</th><?php if($isTeacher): ?><th>Guruh</th><?php endif; ?><th>Turi</th><th class="text-right">Summa</th><th>Izoh</th><th>Kim to'ladi</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="whitespace-nowrap"><?php echo e($p->created_at->format('d.m.Y H:i')); ?></td>
                            <?php if($isTeacher): ?><td><?php echo e($p->group?->name ?? '—'); ?></td><?php endif; ?>
                            <td><?php echo e($p->method->label()); ?></td>
                            <td class="text-right font-semibold"><?php echo e(\App\Support\Format::money($p->amount, false)); ?></td>
                            <td><?php echo e($p->description); ?></td><td><?php echo e($p->creator?->name); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="py-8 text-center text-ink-500">Hali to'lov qilinmagan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/payroll/show.blade.php ENDPATH**/ ?>