<?php $__env->startSection('title', $group->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $me = auth()->user();
    $todayRow = collect($matrix['rows'])->mapWithKeys(fn ($r) => [$r['student']->id => $r['cells'][today()->toDateString()] ?? null]);
    $canTake = $me->can('takeAttendance', $group) && $lessonToday;
?>
<div x-data="{ tab: '<?php echo e(($canTake || $errors->has('attendance')) ? 'attendance' : 'students'); ?>' }">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $group->name,'subtitle' => $group->course->name.' · '.$group->teacher->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group->name),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group->course->name.' · '.$group->teacher->name)]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <span class="<?php echo e(['new' => 'badge-blue', 'active' => 'badge-green', 'finished' => 'badge-gray'][$group->status]); ?>"><?php echo e($group->status_label); ?></span>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $group)): ?><a href="<?php echo e(route('groups.edit', $group)); ?>" class="btn-secondary"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Tahrirlash</a><?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('certificates.issue')): ?>
                <?php if($members->isNotEmpty() && $group->status !== \App\Models\Group::NEW): ?>
                    <a href="<?php echo e(route('certificates.create-group', $group)); ?>" class="btn-secondary">🎓 Sertifikat berish</a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($group->status === \App\Models\Group::NEW && $members->isEmpty()): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $group)): ?>
                    <button type="button" @click="$dispatch('open-archive')" class="btn-secondary text-red-600">O'chirish</button>
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

    <?php if($group->status === \App\Models\Group::NEW && $members->isEmpty()): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $group)): ?>
            <div x-data="{ open: <?php echo e($errors->has('reason') || $errors->has('group') ? 'true' : 'false'); ?> }" @open-archive.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/50 p-4">
                <form method="POST" action="<?php echo e(route('groups.archive', $group)); ?>" @click.outside="open = false" class="card card-body w-full max-w-md space-y-4">
                    <?php echo csrf_field(); ?>
                    <h3 class="text-lg font-semibold text-ink-900 dark:text-white">Guruhni o'chirish</h3>
                    <p class="text-sm text-ink-500">Guruh arxivlanadi: ro'yxatdan yo'qoladi, dars kunlari bo'shatiladi. Keyingi bosqichda parolingiz so'raladi.</p>
                    <?php $__errorArgs = ['group'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'reason','label' => 'Sabab','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'reason','label' => 'Sabab','required' => true]); ?>
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
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="open = false" class="btn-secondary">Bekor qilish</button>
                        <button class="btn-primary">Davom etish</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card card-body"><div class="text-sm text-ink-500">Dars vaqti</div><div class="mt-1 font-semibold"><?php echo e($group->lessonTime->label); ?></div><div class="text-xs text-ink-400"><?php echo e($group->room->name); ?> · <?php echo e($group->schedule->label()); ?></div></div>
        <div class="card card-body"><div class="text-sm text-ink-500">Muddat</div><div class="mt-1 font-semibold"><?php echo e($group->starts_on->format('d.m.Y')); ?> — <?php echo e($group->ends_on->format('d.m.Y')); ?></div><div class="text-xs text-ink-400"><?php echo e($group->lesson_count); ?> ta dars</div></div>
        <div class="card card-body"><div class="text-sm text-ink-500">O'quvchilar</div><div class="mt-1 text-2xl font-bold"><?php echo e($members->count()); ?></div></div>
        <?php if (! ($isTeacher)): ?>
            <div class="card card-body"><div class="text-sm text-ink-500">Narxi</div><div class="mt-1 font-semibold"><?php echo e(\App\Support\Format::money($group->price)); ?></div>
                <?php if($group->early_discount): ?><div class="text-xs text-ink-400">Oldindan to'lovga chegirma: <?php echo e(\App\Support\Format::money($group->early_discount)); ?></div><?php endif; ?></div>
        <?php endif; ?>
    </div>

    <div class="mt-6 flex gap-1 overflow-x-auto rounded-xl bg-ink-100 p-1 dark:bg-ink-800">
        <?php $__currentLoopData = ['students' => "O'quvchilar", 'attendance' => 'Davomad', 'days' => 'Dars kunlari'] + (($continueData && $group->status !== 'new') ? ['continue' => 'Davom ettirish'] : []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" @click="tab = '<?php echo e($k); ?>'" :class="tab === '<?php echo e($k); ?>' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-600 dark:text-ink-300'" class="whitespace-nowrap rounded-lg px-3.5 py-2 text-sm font-medium"><?php echo e($label); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div x-show="tab === 'attendance'" class="mt-4 space-y-6">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editPastAttendance', $group)): ?>
            <div class="flex justify-end">
                <a href="<?php echo e(route('attendance.past.show', $group)); ?>" class="btn-ghost btn-sm">O'tgan kunni tuzatish</a>
            </div>
        <?php endif; ?>
        <?php if($lessonToday): ?>
            <?php if($canTake): ?>
                <form method="POST" action="<?php echo e(route('attendance.take', $group)); ?>" class="card card-body">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-ink-900 dark:text-white">Bugungi davomad — <?php echo e(today()->format('d.m.Y')); ?></h2>
                            <p class="text-sm text-ink-500"><?php echo e($takenToday ? "Davomad olingan. Bugun uchun tahrirlashingiz mumkin." : 'Kelgan o\'quvchilarni belgilang.'); ?></p>
                        </div>
                        <span class="<?php echo e($takenToday ? 'badge-green' : 'badge-amber'); ?>"><?php echo e($takenToday ? 'Olingan' : 'Olinmagan'); ?></span>
                    </div>
                    <?php $__errorArgs = ['attendance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text mb-3"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ink-200 px-3.5 py-2.5 text-sm hover:bg-ink-50 dark:border-ink-700 dark:hover:bg-ink-800">
                                <input type="checkbox" name="present[]" value="<?php echo e($m->student_id); ?>" class="checkbox" <?php if($takenToday ? ($todayRow[$m->student_id] ?? false) : true): echo 'checked'; endif; ?>>
                                <span class="font-medium"><?php echo e($loop->iteration); ?>. <?php echo e($m->student->name); ?></span>
                                <?php if (! ($isTeacher)): ?>
                                    <span class="ml-auto whitespace-nowrap text-xs font-semibold <?php echo e($m->student->balance < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e(\App\Support\Format::money($m->student->balance)); ?></span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-ink-500">Guruhda faol o'quvchi yo'q.</p>
                        <?php endif; ?>
                    </div>
                    <?php if($members->isNotEmpty()): ?><div class="mt-4"><button class="btn-primary">Davomadni saqlash</button></div><?php endif; ?>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <div class="rounded-xl bg-ink-100 px-4 py-3 text-sm text-ink-600 dark:bg-ink-800 dark:text-ink-300">Bugun bu guruhda dars kuni emas. Davomad faqat dars kunlarida olinadi.</div>
        <?php endif; ?>

        <div class="card overflow-hidden"><?php echo $__env->make('groups._matrix', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
    </div>

    
    <div x-show="tab === 'students'" x-cloak class="mt-4 space-y-4">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageMembers', $group)): ?>
            <form method="POST" action="<?php echo e(route('groups.students.store', $group)); ?>" class="card card-body"
                  x-data="{ q: '', found: [], picked: null, allowDebt: false, canDebt: <?php echo \Illuminate\Support\Js::from(auth()->user()->can('groups.enroll_debtor'))->toHtml() ?>, async find() { if (this.q.length < 2) { this.found = []; return } const r = await fetch('<?php echo e(route('students.search')); ?>?q=' + encodeURIComponent(this.q), { headers: { Accept: 'application/json' } }); this.found = await r.json() } }">
                <?php echo csrf_field(); ?>
                <h2 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">O'quvchi qo'shish</h2>
                <?php $__errorArgs = ['student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text mb-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text mb-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <input type="hidden" name="student_id" :value="picked?.id">
                <div class="relative">
                    <input x-model="q" @input.debounce.300ms="find(); picked = null" placeholder="Ism yoki telefon bo'yicha qidiring..." class="input" autocomplete="off">
                    <ul x-show="found.length && !picked" x-cloak class="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border border-ink-200 bg-white shadow-pop dark:border-ink-700 dark:bg-ink-900">
                        <template x-for="s in found" :key="s.id">
                            <li><button type="button" :disabled="s.balance < 0 && !canDebt" :class="s.balance < 0 && !canDebt ? 'cursor-not-allowed opacity-60' : ''" class="flex w-full items-center justify-between gap-3 px-3.5 py-2 text-left text-sm hover:bg-ink-50 dark:hover:bg-ink-800" @click="picked = s; allowDebt = false; q = s.name; found = []">
                                <span><span x-text="s.name"></span> <span class="text-ink-400" x-text="s.phone"></span></span>
                                <span class="whitespace-nowrap text-xs font-semibold" :class="s.balance < 0 ? 'text-brand-600' : 'text-emerald-600'" x-text="new Intl.NumberFormat('ru-RU').format(s.balance) + (s.balance < 0 ? ' · qarzi bor' : '')"></span></button></li>
                        </template>
                    </ul>
                </div>
                <template x-if="picked && picked.balance < 0 && canDebt">
                    <label class="mt-3 flex items-start gap-3 rounded-xl border border-brand-200 bg-brand-50 p-3 text-sm text-brand-800 dark:border-brand-900 dark:bg-brand-950/40 dark:text-brand-200">
                        <input type="checkbox" name="allow_debt" value="1" x-model="allowDebt" class="checkbox mt-0.5">
                        <span><b>Qarzga qo'shish (istisno).</b> Bu o'quvchining qarzi <b x-text="new Intl.NumberFormat('ru-RU').format(-picked.balance) + ' so\'m'"></b>. Guruh narxi yechilgach qarz yanada oshadi. Tasdiqlayman.</span>
                    </label>
                </template>
                <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                    <input name="note" placeholder="Izoh (ixtiyoriy)" class="input">
                    <button class="btn-primary" :disabled="!picked || (picked.balance < 0 && !allowDebt)">Qo'shish (<?php echo e(\App\Support\Format::money($group->price)); ?>)</button>
                </div>
            </form>
        <?php endif; ?>

        <div class="card overflow-hidden">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th class="w-10">№</th><th>F.I.O</th><?php if (! ($isTeacher)): ?><th>Telefon</th><th class="text-right">Balans</th><th>Qo'shilgan</th><th></th><?php endif; ?></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr <?php if (! ($isTeacher)): ?> data-href="<?php echo e(route('students.show', $m->student)); ?>" <?php endif; ?>>
                            <td class="text-ink-400"><?php echo e($loop->iteration); ?></td>
                            <?php if($isTeacher): ?>
                                <td class="font-medium text-ink-900 dark:text-white"><?php echo e($m->student->name); ?></td>
                            <?php else: ?>
                                <td><a href="<?php echo e(route('students.show', $m->student)); ?>" class="font-medium text-ink-900 hover:text-brand-600 dark:text-white"><?php echo e($m->student->name); ?></a></td>
                                <td><?php echo e($m->student->phone); ?></td>
                                <td class="text-right font-semibold <?php echo e($m->student->balance < 0 ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e(\App\Support\Format::money($m->student->balance)); ?></td>
                                <td><?php echo e($m->created_at->format('d.m.Y')); ?></td>
                                <td class="text-right space-x-1">
                                    <a href="<?php echo e(route('students.show', $m->student)); ?>" class="btn-ghost btn-sm">Ochish</a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageMembers', $group)): ?>
                                        <a href="<?php echo e(route('groups.students.contract', [$group, $m->student])); ?>" target="_blank" class="btn-ghost btn-sm">Shartnoma</a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="py-10 text-center text-ink-500">Guruhda o'quvchi yo'q.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div x-show="tab === 'days'" x-cloak class="card card-body mt-4">
        <div class="grid gap-2 sm:grid-cols-3 lg:grid-cols-6">
            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border px-3 py-2 text-sm <?php echo e($d->date->isToday() ? 'border-brand-500 bg-brand-50 dark:bg-brand-950/40' : 'border-ink-200 dark:border-ink-700'); ?>">
                    <div class="text-xs text-ink-400"><?php echo e($i + 1); ?>-dars</div>
                    <div class="font-semibold"><?php echo e($d->date->format('d.m.Y')); ?></div>
                    <div class="text-xs text-ink-500"><?php echo e($d->date->translatedFormat('l')); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($group->nextGroup): ?><p class="mt-4 text-sm text-ink-500">Davomi: <a class="font-semibold text-brand-600" href="<?php echo e(route('groups.show', $group->nextGroup)); ?>"><?php echo e($group->nextGroup->name); ?></a></p><?php endif; ?>
    </div>

    
    <?php if($continueData && $group->status !== 'new'): ?>
        <form x-show="tab === 'continue'" x-cloak method="POST" action="<?php echo e(route('groups.continue', $group)); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
            <div class="card card-body">
                <h2 class="text-lg font-semibold text-ink-900 dark:text-white">Guruhni davom ettirish</h2>
                <p class="mb-5 mt-1 text-sm text-ink-500">Yangi guruh ochiladi va tanlangan o'quvchilar unga qo'shiladi (narx balansdan yechiladi).</p>
                <div class="grid gap-5 sm:grid-cols-2">
                    <?php echo $__env->make('groups._fields', ['withSchedule' => true] + $continueData + ['group' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <div class="card card-body">
                <h3 class="mb-1 text-base font-semibold text-ink-900 dark:text-white">Qaysi o'quvchilar o'tadi?</h3>
                <?php if($canEnrollDebtor): ?>
                    <p class="mb-3 text-sm text-ink-500">Qarzi bor (balansi manfiy) o'quvchilarni ham belgilab o'tkazishingiz mumkin (istisno) — guruh narxi baribir balansdan yechiladi, qarz oshadi.</p>
                <?php else: ?>
                    <p class="mb-3 text-sm text-ink-500">Qarzi bor (balansi manfiy) o'quvchilarni keyingi guruhga qo'shib bo'lmaydi — avval to'lov qabul qiling.</p>
                <?php endif; ?>
                <?php $__errorArgs = ['students'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text mb-3"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $debt = $m->student->balance < 0; $locked = $debt && ! $canEnrollDebtor; ?>
                        <label class="flex items-center justify-between gap-3 rounded-xl border px-3.5 py-2.5 text-sm <?php echo e($debt ? 'border-brand-200 bg-brand-50/50 dark:border-brand-900 dark:bg-brand-950/30' : 'border-ink-200 dark:border-ink-700'); ?>">
                            <span class="flex items-center gap-3"><input type="checkbox" name="students[]" value="<?php echo e($m->student_id); ?>" class="checkbox" <?php if($locked): echo 'disabled'; endif; ?> <?php if(! $debt): echo 'checked'; endif; ?>> <?php echo e($m->student->name); ?></span>
                            <span class="whitespace-nowrap text-xs font-semibold <?php echo e($debt ? 'text-brand-600' : 'text-emerald-600'); ?>"><?php echo e(\App\Support\Format::money($m->student->balance, false)); ?><?php if($debt): ?> · qarz<?php echo e($canEnrollDebtor ? ' (istisno)' : ''); ?> <?php endif; ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <button class="btn-primary">Davom ettirish</button>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/groups/show.blade.php ENDPATH**/ ?>