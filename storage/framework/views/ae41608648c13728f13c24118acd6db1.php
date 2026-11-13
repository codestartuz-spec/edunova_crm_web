<?php $__env->startSection('title', $student->name); ?>

<?php $__env->startSection('content'); ?>
<div x-data="{ removing: null }">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $student->name,'subtitle' => 'Login: '.$student->username]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($student->name),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Login: '.$student->username)]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('students.update')): ?>
                <a href="<?php echo e(route('students.edit', $student)); ?>" class="btn-secondary"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit','class' => 'h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit','class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Tahrirlash</a>
                <form method="POST" action="<?php echo e(route('students.reset-password', $student)); ?>" onsubmit="return confirm('Yangi parol yaratilsinmi? Eski parol ishlamay qoladi.')"><?php echo csrf_field(); ?>
                    <button class="btn-secondary"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'key','class' => 'h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'key','class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Parolni yangilash</button>
                </form>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('certificates.issue')): ?>
                <a href="<?php echo e(route('certificates.create-student', $student->id)); ?>" class="btn-secondary">🎓 Sertifikat berish</a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('students.archive')): ?>
                <?php if($student->archived_at): ?>
                    <form method="POST" action="<?php echo e(route('students.restore', $student)); ?>"><?php echo csrf_field(); ?><button class="btn-secondary">Arxivdan qaytarish</button></form>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('students.archive', $student)); ?>" onsubmit="return confirm('Arxivga o\'tkazilsinmi?')"><?php echo csrf_field(); ?><button class="btn-ghost">Arxivga</button></form>
                <?php endif; ?>
            <?php endif; ?>
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

    <?php if(session('credentials')): ?>
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
            <b>Mobil ilova uchun kirish ma'lumotlari</b> (parol faqat hozir ko'rsatiladi, uni o'quvchiga bering):
            <div class="mt-2 font-mono text-base">Login: <b><?php echo e(session('credentials')['login']); ?></b> &nbsp; Parol: <b><?php echo e(session('credentials')['password']); ?></b></div>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="card card-body">
            <div class="text-sm text-ink-500">Balans</div>
            <div class="mt-1 text-2xl font-bold <?php echo e($student->balance < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e(\App\Support\Format::money($student->balance)); ?></div>
            <div class="mt-1 text-xs text-ink-400"><?php echo e($student->balance < 0 ? 'Qarzdorlik' : "Mavjud mablag'"); ?></div>
        </div>
        <div class="card card-body md:col-span-2">
            <dl class="grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                <div><dt class="text-ink-500">Telefon</dt><dd class="font-medium"><?php echo e($student->phone); ?></dd></div>
                <div><dt class="text-ink-500">Qo'shimcha telefon</dt><dd class="font-medium"><?php echo e($student->phone2 ?? '—'); ?></dd></div>
                <div><dt class="text-ink-500">Tug'ilgan sana</dt><dd class="font-medium"><?php echo e($student->birthday?->format('d.m.Y') ?? '—'); ?></dd></div>
                <div><dt class="text-ink-500">Manba</dt><dd class="font-medium"><?php echo e($student->leadSource?->name ?? '—'); ?></dd></div>
                <div><dt class="text-ink-500">Ro'yxatga olgan</dt><dd class="font-medium"><?php echo e($student->registeredByLabel()); ?><div class="text-xs font-normal text-ink-400"><?php echo e($student->created_at->format('d.m.Y H:i')); ?></div></dd></div>
                <div class="sm:col-span-2"><dt class="text-ink-500">Manzil</dt><dd class="font-medium"><?php echo e($student->address ?? '—'); ?></dd></div>
                <?php if($student->about): ?><div class="sm:col-span-2"><dt class="text-ink-500">Eslatma</dt><dd><?php echo e($student->about); ?></dd></div><?php endif; ?>
            </dl>
        </div>
    </div>

    <?php if(! $student->archived_at && auth()->user()->canany(['payments.create', 'payments.discount', 'payments.refund']) || auth()->user()->isSuperAdmin()): ?>
        <?php echo $__env->make('students._money', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <div class="card mt-6 overflow-hidden">
        <div class="flex flex-col gap-3 px-5 pt-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <h2 class="text-lg font-semibold text-ink-900 dark:text-white">Guruhlar</h2>
            <?php if($availableGroups->isNotEmpty() && ! $student->archived_at && $student->balance < 0 && ! auth()->user()->can('groups.enroll_debtor')): ?>
                <p class="rounded-xl bg-brand-50 px-3.5 py-2 text-sm text-brand-700 dark:bg-brand-950/40 dark:text-brand-300">Qarzi bor o'quvchini guruhga qo'shib bo'lmaydi. Avval to'lov qabul qiling.</p>
            <?php elseif($availableGroups->isNotEmpty() && ! $student->archived_at): ?>
                <form method="POST" action="#" class="flex flex-col gap-2 sm:flex-row sm:flex-wrap" x-data="{ g: '', allowDebt: false }" :action="g ? '<?php echo e(url('groups')); ?>/' + g + '/students' : '#'">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
                    <select x-model="g" required class="input sm:w-64">
                        <option value="">Guruhga qo'shish...</option>
                        <?php $__currentLoopData = $availableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($g->id); ?>"><?php echo e($g->name); ?> — <?php echo e(\App\Support\Format::money($g->price)); ?> (<?php echo e($g->starts_on->format('d.m')); ?>–<?php echo e($g->ends_on->format('d.m.Y')); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input name="note" placeholder="Izoh" class="input sm:w-48">
                    <?php if($student->balance < 0): ?>
                        <label class="flex w-full items-start gap-2 rounded-xl border border-brand-200 bg-brand-50 p-3 text-sm text-brand-800 dark:border-brand-900 dark:bg-brand-950/40 dark:text-brand-200">
                            <input type="checkbox" name="allow_debt" value="1" x-model="allowDebt" class="checkbox mt-0.5">
                            <span><b>Qarzga qo'shish (istisno).</b> O'quvchining qarzi <?php echo e(\App\Support\Format::money(-$student->balance)); ?>. Guruh narxi yechilgach qarz oshadi. Tasdiqlayman.</span>
                        </label>
                    <?php endif; ?>
                    <button class="btn-primary" <?php if($student->balance < 0): ?> :disabled="!allowDebt" <?php endif; ?>>Qo'shish</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="table-wrap mt-3">
            <table class="table">
                <thead><tr><th>Guruh</th><th>O'qituvchi</th><th>Qo'shildi</th><th>Holat</th><th></th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><a href="<?php echo e(route('groups.show', $m->group)); ?>" class="font-medium text-ink-900 hover:text-brand-600 dark:text-white"><?php echo e($m->group->name); ?></a></td>
                        <td><?php echo e($m->group->teacher->name); ?></td>
                        <td><?php echo e($m->created_at->format('d.m.Y')); ?><?php if($m->addedBy): ?><div class="text-xs text-ink-400"><?php echo e($m->addedBy->name); ?></div><?php endif; ?></td>
                        <td>
                            <?php if($m->is_active): ?><span class="badge-green">Faol</span>
                            <?php else: ?><span class="badge-gray">Chiqarilgan <?php echo e($m->left_at?->format('d.m.Y')); ?></span><?php if($m->fine > 0): ?><div class="text-xs text-brand-600">Jarima: <?php echo e(\App\Support\Format::money($m->fine)); ?></div><?php endif; ?> <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?php if($m->is_active): ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('groups.members')): ?>
                                <button class="btn-ghost btn-sm" @click="removing = { id: <?php echo e($m->group_id); ?>, name: <?php echo \Illuminate\Support\Js::from($m->group->name)->toHtml() ?>, price: <?php echo e($m->group->price); ?> }">Chiqarish</button>
                            <?php endif; ?> <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="py-8 text-center text-ink-500">Hali guruhga qo'shilmagan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="card mt-6 overflow-hidden" x-data="{ f: 'all' }">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5 sm:px-6">
            <div>
                <h2 class="text-lg font-semibold text-ink-900 dark:text-white">Balans harakatlari va tarix</h2>
                <p class="text-xs text-ink-500">To'lovlar naqt yoki plastik ekani bilan; guruhga qo'shilish, chiqish va boshqa hodisalar ham shu yerda.</p>
            </div>
            <div class="flex rounded-lg bg-ink-100 p-0.5 text-xs font-semibold dark:bg-ink-800" role="tablist">
                <?php $__currentLoopData = ['all' => 'Hammasi', 'money' => 'Pul harakati', 'event' => 'Hodisalar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" @click="f = '<?php echo e($k); ?>'" :class="f === '<?php echo e($k); ?>' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-500'" class="rounded-md px-2.5 py-1"><?php echo e($l); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="table-wrap mt-3">
            <table class="table">
                <thead><tr><th>Sana</th><th>Turi</th><th>To'lov usuli</th><th class="text-right">Summa</th><th class="text-right">Qoldiq</th><th>Kim</th><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payments.reverse')): ?><th>Storno</th><?php endif; ?></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr x-show="f === 'all' || f === '<?php echo e($r['kind']); ?>'">
                        <td class="whitespace-nowrap"><?php echo e($r['at']->format('d.m.Y H:i')); ?></td>
                        <td>
                            <?php if($r['kind'] === 'money'): ?>
                                <span class="badge-<?php echo e(['green' => 'green', 'amber' => 'amber', 'red' => 'red'][$r['tone']] ?? 'gray'); ?>"><?php echo e($r['label']); ?></span>
                                <?php if($r['detail']): ?><div class="mt-0.5 text-xs text-ink-400"><?php echo e($r['detail']); ?></div><?php endif; ?>
                            <?php else: ?>
                                <span class="text-ink-700 dark:text-ink-200"><?php echo e($r['label']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($r['method']): ?><span class="<?php echo e($r['method'] === 'Naqt' ? 'badge-green' : 'badge-blue'); ?>"><?php echo e($r['method']); ?></span><?php else: ?><span class="text-ink-300">—</span><?php endif; ?>
                        </td>
                        <td class="text-right font-medium tabular-nums <?php echo e(($r['amount'] ?? 0) < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>">
                            <?php if($r['amount'] !== null): ?><?php echo e($r['amount'] > 0 ? '+' : ''); ?><?php echo e(\App\Support\Format::money($r['amount'], false)); ?><?php else: ?><span class="text-ink-300">—</span><?php endif; ?>
                        </td>
                        <td class="text-right tabular-nums"><?php if($r['balance'] !== null): ?><?php echo e(\App\Support\Format::money($r['balance'], false)); ?><?php else: ?><span class="text-ink-300">—</span><?php endif; ?></td>
                        <td class="text-xs text-ink-500">
                            <?php echo e($r['by'] ?? '—'); ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payments.view')): ?>
                                <?php if($r['kind'] === 'money' && ($r['payment_id'] ?? null)): ?>
                                    <a href="<?php echo e(route('payments.receipt', $r['payment_id'])); ?>" target="_blank" class="ml-1 text-brand-600 hover:underline">Chek</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payments.reverse')): ?>
                            <td>
                                <?php if($r['kind'] === 'money' && ($r['reversed'] ?? false)): ?>
                                    <span class="badge-gray text-xs">Stornolangan</span>
                                <?php elseif($r['kind'] === 'money' && ($r['reversible'] ?? false)): ?>
                                    <details>
                                        <summary class="btn-ghost btn-sm inline-block cursor-pointer list-none">Storno</summary>
                                        <form method="POST" action="<?php echo e(route('payments.reverse', $r['payment_id'])); ?>" class="mt-2 w-64 space-y-2 rounded-xl border border-ink-200 bg-ink-50 p-3 dark:border-ink-700 dark:bg-ink-800/60">
                                            <?php echo csrf_field(); ?>
                                            <label class="label text-xs" for="reverse-reason-<?php echo e($r['payment_id']); ?>">Sabab</label>
                                            <input id="reverse-reason-<?php echo e($r['payment_id']); ?>" name="reason" type="text" required maxlength="255" placeholder="Nega storno qilinmoqda?" class="input">
                                            <button class="btn-primary btn-sm w-full">Tekshiruvga o'tish</button>
                                        </form>
                                    </details>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="<?php echo e(auth()->user()->can('payments.reverse') ? 7 : 6); ?>" class="py-8 text-center text-ink-500">Harakat yo'q.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('students.notes')): ?>
    <div class="card card-body mt-6">
        <h2 class="mb-1 text-lg font-semibold text-ink-900 dark:text-white">Ichki eslatmalar</h2>
        <p class="mb-4 text-xs text-ink-500">Faqat xodimlarga ko'rinadi, o'quvchi ko'rmaydi. Yuqoridagi "Eslatma" maydonidan farqli — bir nechta yozuv, muallif va sana bilan saqlanadi.</p>
        <form method="POST" action="<?php echo e(route('students.notes.store', $student)); ?>" class="mb-5 flex flex-col gap-2 sm:flex-row">
            <?php echo csrf_field(); ?>
            <textarea name="body" rows="2" maxlength="2000" placeholder="Eslatma matni..." class="input" required></textarea>
            <button class="btn-primary sm:self-start">Qo'shish</button>
        </form>
        <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text -mt-3 mb-3"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php if($notes->isNotEmpty()): ?>
            <ol class="space-y-3 border-l border-ink-200 pl-4 dark:border-ink-700">
                <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="relative text-sm">
                        <span class="absolute -left-[21px] top-1.5 h-2.5 w-2.5 rounded-full bg-brand-500"></span>
                        <div class="flex items-start justify-between gap-3">
                            <div class="whitespace-pre-wrap <?php echo e($n->isClosed() ? 'text-ink-400 line-through' : 'text-ink-700 dark:text-ink-200'); ?>"><?php echo e($n->body); ?></div>
                            <div class="flex shrink-0 items-center gap-3">
                                <form method="POST" action="<?php echo e($n->isClosed() ? route('notes.reopen', $n) : route('notes.close', $n)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="text-xs text-ink-400 hover:text-brand-600"><?php echo e($n->isClosed() ? 'Qayta faollashtirish' : 'Faolsizlantirish'); ?></button>
                                </form>
                                <form method="POST" action="<?php echo e(route('students.notes.destroy', [$student, $n])); ?>" onsubmit="return confirm('Eslatma o\'chirilsinmi?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="text-xs text-ink-400 hover:text-brand-600">O'chirish</button>
                                </form>
                            </div>
                        </div>
                        <div class="text-xs text-ink-400"><?php echo e($n->created_at->format('d.m.Y H:i')); ?> · <?php echo e($n->user?->name ?? '—'); ?><?php if($n->isClosed()): ?> · <span class="badge-gray">Yopilgan: <?php echo e($n->closed_at->format('d.m.Y H:i')); ?><?php echo e($n->closer ? ', '.$n->closer->name : ''); ?></span><?php endif; ?></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        <?php else: ?>
            <p class="text-sm text-ink-500">Hali eslatma yo'q.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <div x-show="removing" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/60 p-4" @keydown.escape.window="removing = null">
        <div class="card card-body w-full max-w-md shadow-pop" @click.outside="removing = null">
            <h3 class="text-lg font-semibold text-ink-900 dark:text-white">Guruhdan chiqarish</h3>
            <p class="mt-2 text-sm text-ink-600 dark:text-ink-300"><b x-text="removing?.name"></b> guruhi narxi balansga qaytariladi, jarima kiritilsa balansdan yechiladi.</p>
            <form method="POST" :action="removing ? '<?php echo e(url('groups')); ?>/' + removing.id + '/students/<?php echo e($student->id); ?>' : '#'" class="mt-4 space-y-4">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <div>
                    <label class="label" for="fine">Jarima (so'm)</label>
                    <input id="fine" name="fine" type="text" inputmode="numeric" data-money value="0" class="input">
                </div>
                <div>
                    <label class="label" for="rnote">Sabab</label>
                    <input id="rnote" name="note" maxlength="500" class="input">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-secondary" @click="removing = null">Bekor qilish</button>
                    <button class="btn-primary">Chiqarish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/students/show.blade.php ENDPATH**/ ?>