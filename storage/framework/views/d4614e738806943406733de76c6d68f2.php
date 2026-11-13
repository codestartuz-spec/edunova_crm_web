<?php $__env->startSection('title', 'Varonka'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Varonka','subtitle' => 'Murojaatlar: yangi → ko\'rib chiqilmoqda → qabul qilindi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Varonka','subtitle' => 'Murojaatlar: yangi → ko\'rib chiqilmoqda → qabul qilindi']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('leads.manage')): ?><a href="<?php echo e(route('leads.create')); ?>" class="btn-primary"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'h-4 w-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'h-4 w-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Murojaat qo'shish</a><?php endif; ?>
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

    
    <div class="card card-body mb-6" x-data="{ view: 'funnel' }">
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-ink-900 dark:text-white">Murojaatlar voronkasi</h3>
                <p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Murojaat → ko'rib chiqilgan → o'quvchi bo'lgan. Har bosqichda umumiy ulush va oldingi bosqichdan o'tish foizi.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="<?php echo e($status); ?>">
                    <select name="days" class="input py-1.5 text-sm" onchange="this.form.submit()" aria-label="Davr">
                        <option value="0" <?php if(! $days): echo 'selected'; endif; ?>>Hammasi</option>
                        <?php $__currentLoopData = [30 => 'Oxirgi 30 kun', 90 => 'Oxirgi 90 kun', 365 => 'Oxirgi 1 yil']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($d); ?>" <?php if($days === $d): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
                <div class="flex rounded-lg bg-ink-100 p-0.5 text-xs font-semibold dark:bg-ink-800" role="tablist">
                    <button type="button" @click="view = 'funnel'" :class="view === 'funnel' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-500'" class="rounded-md px-2.5 py-1">Voronka</button>
                    <button type="button" @click="view = 'table'" :class="view === 'table' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-500'" class="rounded-md px-2.5 py-1">Jadval</button>
                </div>
            </div>
        </div>
        <div x-show="view === 'funnel'" class="grid items-center gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <?php if(($funnel[0]['value'] ?? 0) > 0): ?>
                    <?php if (isset($component)) { $__componentOriginalec297bf2307c22bf6d7fc76cc19145a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.funnel','data' => ['stages' => $funnel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.funnel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($funnel)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3)): ?>
<?php $attributes = $__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3; ?>
<?php unset($__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalec297bf2307c22bf6d7fc76cc19145a3)): ?>
<?php $component = $__componentOriginalec297bf2307c22bf6d7fc76cc19145a3; ?>
<?php unset($__componentOriginalec297bf2307c22bf6d7fc76cc19145a3); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="flex h-48 items-center justify-center rounded-xl bg-ink-50 text-sm text-ink-400 dark:bg-ink-800/40">Bu davrda murojaat yo'q</div>
                <?php endif; ?>
            </div>
            <dl class="grid grid-cols-2 gap-3 text-sm lg:grid-cols-1">
                <div class="rounded-xl bg-ink-50 p-3 dark:bg-ink-800/40"><dt class="text-ink-500">Hali ko'rilmagan (yangi)</dt><dd class="text-xl font-bold text-ink-900 dark:text-white"><?php echo e($funnelNew); ?></dd></div>
                <div class="rounded-xl bg-ink-50 p-3 dark:bg-ink-800/40"><dt class="text-ink-500">Bekor qilingan</dt><dd class="text-xl font-bold text-ink-900 dark:text-white"><?php echo e($funnelCancelled); ?></dd></div>
            </dl>
        </div>
        <div x-show="view === 'table'" x-cloak>
            <div class="table-wrap rounded-xl border border-ink-100 dark:border-ink-800">
                <table class="table">
                    <thead><tr><th>Bosqich</th><th class="text-right">Soni</th><th class="text-right">Umumiy ulush</th><th class="text-right">Oldingi bosqichdan</th></tr></thead>
                    <tbody>
                    <?php $__currentLoopData = $funnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-medium text-ink-900 dark:text-white"><?php echo e($st['label']); ?></td>
                            <td class="text-right tabular-nums"><?php echo e($st['value']); ?></td>
                            <td class="text-right tabular-nums"><?php echo e($funnel[0]['value'] ? round($st['value'] * 100 / $funnel[0]['value']) . '%' : '—'); ?></td>
                            <td class="text-right tabular-nums"><?php echo e($i > 0 && $funnel[$i - 1]['value'] ? round($st['value'] * 100 / $funnel[$i - 1]['value']) . '%' : '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="text-ink-500"><td>Yangi (ko'rilmagan)</td><td class="text-right tabular-nums"><?php echo e($funnelNew); ?></td><td class="text-right tabular-nums"><?php echo e($funnel[0]['value'] ? round($funnelNew * 100 / $funnel[0]['value']) . '%' : '—'); ?></td><td></td></tr>
                    <tr class="text-ink-500"><td>Bekor qilingan</td><td class="text-right tabular-nums"><?php echo e($funnelCancelled); ?></td><td class="text-right tabular-nums"><?php echo e($funnel[0]['value'] ? round($funnelCancelled * 100 / $funnel[0]['value']) . '%' : '—'); ?></td><td></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <?php $__currentLoopData = \App\Models\Lead::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('leads.index', ['status' => $k])); ?>" class="card card-body transition hover:border-brand-400 <?php echo e($status === $k ? 'ring-2 ring-brand-500' : ''); ?>">
                <div class="text-sm text-ink-500"><?php echo e($label); ?></div>
                <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($counts[$k] ?? 0); ?></div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="card card-body"><div class="text-sm text-ink-500">Qabul foizi</div><div class="mt-1 text-2xl font-bold text-emerald-600"><?php echo e($rate); ?>%</div><div class="text-xs text-ink-400">jami <?php echo e($total); ?> murojaatdan</div></div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 dark:border-ink-800 sm:grid-cols-3">
                <input type="hidden" name="status" value="<?php echo e($status); ?>">
                <input name="q" value="<?php echo e(\App\Support\SafeInput::string(request('q'))); ?>" placeholder="Ism yoki telefon" class="input">
                <select name="source" class="input" onchange="this.form.submit()"><option value="">Barcha manbalar</option>
                    <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('source') == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn-secondary">Qidirish</button>
            </form>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Ism</th><th>Telefon</th><th>Manba</th><th>AI</th><th>Sana</th><th></th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-href="<?php echo e(route('leads.show', $l)); ?>">
                            <td>
                                <a href="<?php echo e(route('leads.show', $l)); ?>" class="font-medium text-ink-900 hover:text-brand-600 dark:text-white"><?php echo e($l->name); ?></a>
                                <?php if($l->is_repeat): ?><span class="badge-amber ml-1" title="Bu raqam o'quvchi sifatida ro'yxatda bor">Takror</span><?php endif; ?>
                            </td>
                            <td><?php if (isset($component)) { $__componentOriginalb6152700bb2bce174521585210331945 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb6152700bb2bce174521585210331945 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.phone-copy','data' => ['value' => $l->phone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('phone-copy'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($l->phone)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb6152700bb2bce174521585210331945)): ?>
<?php $attributes = $__attributesOriginalb6152700bb2bce174521585210331945; ?>
<?php unset($__attributesOriginalb6152700bb2bce174521585210331945); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb6152700bb2bce174521585210331945)): ?>
<?php $component = $__componentOriginalb6152700bb2bce174521585210331945; ?>
<?php unset($__componentOriginalb6152700bb2bce174521585210331945); ?>
<?php endif; ?></td>
                            <td><?php echo e($l->source?->name ?? '—'); ?></td>
                            <td><?php if($l->ai_analysis): ?><span class="<?php echo e(['yuqori' => 'badge-green', "o'rta" => 'badge-amber', 'past' => 'badge-gray'][$l->ai_analysis['priority']] ?? 'badge-gray'); ?>" title="Qabul ehtimoli"><?php echo e($l->ai_analysis['priority']); ?> · <?php echo e($l->ai_analysis['probability']); ?>%</span><?php else: ?><span class="text-ink-300">—</span><?php endif; ?></td>
                            <td class="whitespace-nowrap"><?php echo e($l->created_at->format('d.m.Y H:i')); ?></td>
                            <td class="text-right"><a href="<?php echo e(route('leads.show', $l)); ?>" class="btn-ghost btn-sm">Ochish</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="py-12 text-center text-ink-500">Murojaat yo'q.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($leads->links()); ?>

        </div>

        <div class="space-y-6">
            <div class="card card-body">
                <h3 class="mb-3 text-base font-semibold text-ink-900 dark:text-white">Manbalar bo'yicha</h3>
                <ul class="space-y-2 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $bySource; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex justify-between gap-2"><span><?php echo e($r->source); ?></span><span class="font-semibold"><?php echo e($r->total); ?> <span class="font-normal text-emerald-600">(<?php echo e((int) $r->converted); ?> qabul)</span></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><li class="text-ink-500">Ma'lumot yo'q.</li><?php endif; ?>
                </ul>
            </div>
            <?php if($formBranch): ?>
                <?php $url = route('apply.show', $formBranch->code); $iframe = '<iframe src="'.route('apply.show', [$formBranch->code, 'embed' => 1]).'" width="100%" height="560" style="border:0" loading="lazy" title="Murojaat qoldirish"></iframe>'; ?>
                <div class="card card-body space-y-3" x-data="{ copied: '' , copy(t, k) { navigator.clipboard.writeText(t); this.copied = k; setTimeout(() => this.copied = '', 1500) } }">
                    <h3 class="text-base font-semibold text-ink-900 dark:text-white">Murojaat formasi: <?php echo e($formBranch->name); ?></h3>
                    <p class="text-xs text-ink-500">Bu filial uchun forma. Murojaatlar avtomatik shu Varonkaga tushadi.</p>
                    <div><label class="label">Havola</label>
                        <div class="flex gap-2"><input readonly value="<?php echo e($url); ?>" class="input text-xs" onclick="this.select()"><button type="button" class="btn-secondary btn-sm shrink-0" @click="copy(<?php echo \Illuminate\Support\Js::from($url)->toHtml() ?>, 'url')" x-text="copied === 'url' ? 'Nusxalandi' : 'Nusxalash'"></button></div>
                        <a href="<?php echo e($url); ?>" target="_blank" class="mt-1 inline-block text-xs font-semibold text-brand-600">Formani ochish</a></div>
                    <div><label class="label">Saytga qo'yish kodi (iframe)</label>
                        <textarea readonly rows="3" class="input text-xs" onclick="this.select()"><?php echo e($iframe); ?></textarea>
                        <button type="button" class="btn-secondary btn-sm mt-1" @click="copy(<?php echo \Illuminate\Support\Js::from($iframe)->toHtml() ?>, 'code')" x-text="copied === 'code' ? 'Nusxalandi' : 'Kodni nusxalash'"></button></div>
                </div>
            <?php else: ?>
                <div class="card card-body text-sm text-ink-500">Murojaat formasi havolasi va saytga qo'yish kodi uchun yuqoridan <b>filialni tanlang</b>.</div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/leads/index.blade.php ENDPATH**/ ?>