<?php $__env->startSection('title', $branch->name); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $svg = fn (string $p, string $cls = 'h-4 w-4') => '<svg class="'.$cls.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$p.'</svg>';
        $ic = [
            'phone' => '<path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2A16 16 0 0 1 4.5 5.5a2 2 0 0 1 2-2z"/>',
            'pin' => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
            'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8"/><path d="M18 14.3c2.2.6 3.5 2.4 3.5 5.7"/>',
            'cap' => '<path d="M2.5 9 12 4l9.5 5-9.5 5L2.5 9z"/><path d="M6 11.5V16c0 1.4 2.7 3 6 3s6-1.6 6-3v-4.5"/>',
            'chip' => '<rect x="7" y="2.5" width="10" height="19" rx="2"/><path d="M11 18.5h2"/>',
            'wallet' => '<path d="M4 7a2 2 0 0 1 2-2h11v4"/><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 14h2"/>',
            'cog' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
            'back' => '<path d="M19 12H5"/><path d="m11 6-6 6 6 6"/>',
            'plus' => '<path d="M12 5v14M5 12h14"/>',
            'archive' => '<rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V9"/><path d="M10 13h4"/>',
            'reopen' => '<path d="M20 12a8 8 0 1 1-2.5-5.8"/><path d="M20 4v5h-5"/>',
            'trash' => '<path d="M4 7h16M10 11v6M14 11v6"/><path d="M6 7l1 13h10l1-13"/><path d="M9 7V4h6v3"/>',
            'cash' => '<rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 9.5v.01M18 14.5v.01"/>',
            'card' => '<rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M6 15h4"/>',
            'send' => '<path d="M21 3 10 14"/><path d="m21 3-7 18-4-7-7-4 18-7z"/>',
            'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
            'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'alert' => '<path d="M12 3.5 2.5 20h19L12 3.5z"/><path d="M12 10v4.5M12 17.5h.01"/>',
            'save' => '<path d="M5 4h11l3 3v13H5z"/><path d="M8 4v5h7V4M8 20v-6h8v6"/>',
        ];
        $limit = (int) $branch->sms_modem_daily_limit;
        $pct = $limit > 0 ? min(100, (int) round($usedToday / $limit * 100)) : 0;
        $maxDay = max(1, collect($chart)->max('count'));
        $monthName = ['01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel', '05' => 'May', '06' => 'Iyun', '07' => 'Iyul', '08' => 'Avgust', '09' => 'Sentabr', '10' => 'Oktabr', '11' => 'Noyabr', '12' => 'Dekabr'];
        $tz = 'Asia/Tashkent';
        $tabs = ['settings' => ['Sozlamalar', 'cog'], 'sms' => ['SMS', 'chip']] + ($isSadmin ? ['payments' => ["To'lovlar", 'wallet']] : []);
        $canDelete = auth()->user()->can('branches.delete');
        $payErr = $errors->has('amount') || $errors->has('method') || $errors->has('note');
    ?>

    <style>
        .bs { --bc: <?php echo e($branch->effectiveBrandColor()); ?>; }
        @keyframes bs-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
        @keyframes bs-grow { from { transform: scaleY(0); } to { transform: scaleY(1); } }
        @keyframes bs-fill { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        @keyframes bs-pop { from { opacity: 0; transform: translateY(18px) scale(.97); } to { opacity: 1; transform: none; } }
        @keyframes bs-pulse { 0%, 100% { box-shadow: 0 0 0 0 color-mix(in srgb, var(--bc) 40%, transparent); } 50% { box-shadow: 0 0 0 10px transparent; } }
        .bs-in { animation: bs-in .5s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 60ms); }
        .bs-tile { background: color-mix(in srgb, var(--bc) 14%, transparent); color: var(--bc); }
        .bs-hero { position: relative; overflow: hidden; }
        .bs-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(700px 200px at 0% 0%, color-mix(in srgb, var(--bc) 16%, transparent), transparent 70%); pointer-events: none; }
        .bs-avatar { animation: bs-pulse 3s ease-in-out infinite; }
        .bs-tab { position: relative; transition: color .2s, background-color .2s; }
        .bs-tab[aria-selected="true"] { background: #fff; color: var(--bc); box-shadow: 0 1px 3px rgba(15,23,42,.12); }
        .dark .bs-tab[aria-selected="true"] { background: rgba(255,255,255,.1); color: #fff; }
        .bs-panel { animation: bs-in .35s cubic-bezier(.2,.8,.2,1) both; }
        .bs-meter > i { display: block; height: 100%; border-radius: 9999px; background: var(--bc); transform-origin: left; animation: bs-fill 1s cubic-bezier(.2,.8,.2,1) both; }
        .bs-bar { transform-origin: bottom; animation: bs-grow .6s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 30ms); background: var(--bc); transition: filter .15s; }
        .bs-barwrap:hover .bs-bar { filter: brightness(1.15); }
        .bs-tip { opacity: 0; transform: translateY(4px); pointer-events: none; transition: opacity .15s, transform .15s; }
        .bs-barwrap:hover .bs-tip { opacity: 1; transform: none; }
        .bs-modal { animation: bs-pop .28s cubic-bezier(.2,.8,.2,1) both; }
        .bs-method { transition: border-color .15s, background-color .15s, transform .15s; }
        .bs-method:active { transform: scale(.98); }
        .bs-row { transition: background-color .15s; }
        .bs-row:hover { background: rgba(148,163,184,.08); }
        @media (prefers-reduced-motion: reduce) { .bs-in, .bs-bar, .bs-meter > i, .bs-avatar, .bs-panel, .bs-modal { animation: none !important; } }
    </style>

    <div class="bs" x-data="{
            tab: <?php echo \Illuminate\Support\Js::from($tab)->toHtml() ?>, pay: <?php echo \Illuminate\Support\Js::from($payErr)->toHtml() ?>, closing: false, deleting: false, typed: '', method: <?php echo \Illuminate\Support\Js::from(old('method', 'cash'))->toHtml() ?>, amount: <?php echo \Illuminate\Support\Js::from((string) old('amount', ''))->toHtml() ?>,
            fmt(v) { return String(v).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); },
            setTab(t) { this.tab = t; const u = new URL(location.href); u.searchParams.set('tab', t); u.searchParams.delete('page'); history.replaceState(null, '', u); }
        }" @keydown.escape.window="pay = false; closing = false; deleting = false">

        <a href="<?php echo e(route('branches.index')); ?>" class="mb-3 inline-flex items-center gap-1.5 text-sm text-ink-500 transition hover:text-ink-900 dark:hover:text-white"><?php echo $svg($ic['back']); ?> Filiallar</a>

        
        <div class="card bs-hero bs-in mb-4 p-5">
            <div class="relative z-10 flex flex-wrap items-center gap-4">
                <span class="bs-avatar bs-tile flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl text-3xl font-bold"><?php echo e(mb_strtoupper(mb_substr($branch->name, 0, 1))); ?></span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($branch->name); ?></h1>
                        <span class="<?php echo e($branch->isActive() ? 'badge-green' : 'badge-gray'); ?>"><?php echo e($branch->status->label()); ?></span>
                    </div>
                    <div class="mt-0.5 text-xs text-ink-400"><?php echo e($branch->code); ?><?php if (! ($branch->isActive())): ?> · Yopilgan: <?php echo e($branch->closed_at?->format('d.m.Y')); ?><?php if($branch->closed_reason): ?> — <?php echo e($branch->closed_reason); ?><?php endif; ?> <?php endif; ?></div>
                    <div class="mt-3 flex flex-wrap gap-2 text-xs text-ink-600 dark:text-ink-300">
                        <?php if($branch->phone): ?><span class="inline-flex items-center gap-1.5 rounded-full bg-ink-100 px-2.5 py-1 dark:bg-ink-800"><?php echo $svg($ic['phone'], 'h-3.5 w-3.5'); ?><?php echo e($branch->phone); ?></span><?php endif; ?>
                        <?php if($branch->address): ?><span class="inline-flex items-center gap-1.5 rounded-full bg-ink-100 px-2.5 py-1 dark:bg-ink-800"><?php echo $svg($ic['pin'], 'h-3.5 w-3.5'); ?><?php echo e($branch->address); ?></span><?php endif; ?>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-100 px-2.5 py-1 dark:bg-ink-800"><?php echo $svg($ic['users'], 'h-3.5 w-3.5'); ?><?php echo e($branch->staff_count); ?> hodim</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-100 px-2.5 py-1 dark:bg-ink-800"><?php echo $svg($ic['cap'], 'h-3.5 w-3.5'); ?><?php echo e($branch->students_count); ?> o'quvchi</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <?php if($isSadmin): ?>
                        <button type="button" class="btn-primary" @click="pay = true"><?php echo $svg($ic['plus']); ?> To'lov qabul qilish</button>
                    <?php endif; ?>
                    <?php if($branch->isActive()): ?>
                        <button type="button" class="btn-secondary" @click="closing = true"><?php echo $svg($ic['archive']); ?> Yopish</button>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('branches.reopen', $branch)); ?>"><?php echo csrf_field(); ?>
                            <button class="btn-secondary"><?php echo $svg($ic['reopen']); ?> Qayta ochish</button></form>
                    <?php endif; ?>
                    <?php if($canDelete): ?>
                        <button type="button" class="btn-danger" @click="deleting = true"><?php echo $svg($ic['trash']); ?> Butunlay o'chirish</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="mb-4 grid gap-3 sm:grid-cols-2 <?php echo e($isSadmin ? 'xl:grid-cols-4' : 'xl:grid-cols-2'); ?>">
            <div class="card bs-in p-4" style="--i: 1">
                <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wide text-ink-500">Modem SMS · bugun <span class="bs-tile flex h-8 w-8 items-center justify-center rounded-lg"><?php echo $svg($ic['chip']); ?></span></div>
                <div class="mt-2 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($usedToday); ?><?php if($limit > 0): ?><span class="text-base font-medium text-ink-400"> / <?php echo e($limit); ?></span><?php endif; ?></div>
                <?php if($limit > 0): ?><div class="bs-meter mt-2 h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-700"><i style="width: <?php echo e($pct); ?>%"></i></div><?php else: ?><div class="mt-2 text-xs text-ink-500">Modem o'chiq — SMS Eskiz orqali</div><?php endif; ?>
            </div>
            <div class="card bs-in p-4" style="--i: 2">
                <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wide text-ink-500">Joriy oy (modem) <span class="bs-tile flex h-8 w-8 items-center justify-center rounded-lg"><?php echo $svg($ic['chart']); ?></span></div>
                <div class="mt-2 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($monthly[$months[0]] ?? 0); ?></div>
                <div class="mt-2 text-xs text-ink-500"><?php echo e($monthName[substr($months[0], 5, 2)]); ?> <?php echo e(substr($months[0], 0, 4)); ?></div>
            </div>
            <?php if($isSadmin): ?>
                <div class="card bs-in p-4" style="--i: 3">
                    <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wide text-ink-500">Jami to'langan <span class="bs-tile flex h-8 w-8 items-center justify-center rounded-lg"><?php echo $svg($ic['wallet']); ?></span></div>
                    <div class="mt-2 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e(\App\Support\Format::money($paidTotal)); ?></div>
                    <div class="mt-2 text-xs text-ink-500"><?php echo e($payments->total()); ?> ta to'lov</div>
                </div>
                <div class="card bs-in p-4" style="--i: 4">
                    <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wide text-ink-500">Oxirgi to'lov <span class="bs-tile flex h-8 w-8 items-center justify-center rounded-lg"><?php echo $svg($ic['clock']); ?></span></div>
                    <div class="mt-2 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($lastPayment ? \App\Support\Format::money($lastPayment->amount) : '—'); ?></div>
                    <div class="mt-2 text-xs text-ink-500"><?php echo e($lastPayment ? $lastPayment->created_at->timezone($tz)->format('d.m.Y H:i').' · '.$lastPayment->methodLabel() : "Hali to'lov qabul qilinmagan"); ?></div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="mb-4 inline-flex rounded-2xl bg-ink-100 p-1 text-sm font-medium dark:bg-ink-800" role="tablist">
            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" role="tab" class="bs-tab inline-flex items-center gap-2 rounded-xl px-4 py-2 text-ink-600 dark:text-ink-300" :aria-selected="tab === '<?php echo e($key); ?>'" @click="setTab('<?php echo e($key); ?>')">
                    <?php echo $svg($ic[$icon]); ?> <?php echo e($label); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div x-show="tab === 'settings'" x-cloak class="bs-panel">
            <form method="POST" action="<?php echo e(route('branches.update', $branch)); ?>" class="max-w-3xl space-y-5">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="card card-body grid gap-5 sm:grid-cols-2">
                    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'name','label' => 'Filial nomi','value' => $branch->name,'required' => true,'class' => 'sm:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Filial nomi','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->name),'required' => true,'class' => 'sm:col-span-2']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'phone','phone' => true,'label' => 'Telefon','value' => $branch->phone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'phone','phone' => true,'label' => 'Telefon','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->phone)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'address','label' => 'Manzil','value' => $branch->address]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'address','label' => 'Manzil','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->address)]); ?>
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

                <div class="card card-body">
                    <h2 class="text-base font-semibold text-ink-900 dark:text-white">Filial rangi va ochiq murojaat sahifasi</h2>
                    <p class="mb-5 mt-1 text-sm text-ink-500 dark:text-ink-400">Tanlangan rang shu filialning butun panelida (menyu, tugmalar, belgilar), o'quvchi portalida, ochiq murojaat sahifasida va sertifikatlarda ishlatiladi. Juda och ranglar matn o'qilishi uchun avtomatik to'qroq qilinadi.</p>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div x-data="{ color: '<?php echo e(old('brand_color', $branch->brand_color ?? \App\Models\Branch::DEFAULT_BRAND_COLOR)); ?>' }">
                            <label class="label" for="brand_color">Rang</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="brand_color" name="brand_color" x-model="color" class="color-swatch">
                                <span class="input flex-1 cursor-default select-all uppercase" x-text="color"></span>
                            </div>
                            <?php $__errorArgs = ['brand_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="public_about">Filial haqida (ixtiyoriy)</label>
                            <textarea id="public_about" name="public_about" rows="3" maxlength="1000" class="input"><?php echo e(old('public_about', $branch->public_about)); ?></textarea>
                            <?php $__errorArgs = ['public_about'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
                <button class="btn-primary"><?php echo $svg($ic['save']); ?> Saqlash</button>
            </form>
        </div>

        
        <div x-show="tab === 'sms'" x-cloak class="bs-panel">
            <div class="grid gap-4 xl:grid-cols-5">
                <form method="POST" action="<?php echo e(route('branches.sms', $branch)); ?>" class="card card-body space-y-5 xl:col-span-2">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div>
                        <h2 class="flex items-center gap-2 text-base font-semibold text-ink-900 dark:text-white"><span class="bs-tile flex h-8 w-8 items-center justify-center rounded-lg"><?php echo $svg($ic['chip']); ?></span> USB modem limiti</h2>
                        <p class="mt-2 text-sm text-ink-500 dark:text-ink-400">Filial bir kunda modem orqali yuborishi mumkin bo'lgan SMS soni (Toshkent sanasi bo'yicha). <b>0</b> — filial modemdan foydalanmaydi, hamma SMS Eskiz orqali ketadi. Limit tugasa ham SMS Eskizga o'tadi.</p>
                    </div>
                    <div>
                        <label class="label" for="sms_modem_daily_limit">Kunlik limit</label>
                        <input id="sms_modem_daily_limit" type="number" min="0" max="100000" name="sms_modem_daily_limit" value="<?php echo e(old('sms_modem_daily_limit', $limit)); ?>" class="input" <?php if(! $canLimit): echo 'disabled'; endif; ?>>
                        <?php $__errorArgs = ['sms_modem_daily_limit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php if (! ($canLimit)): ?><p class="hint">Limitni o'zgartirish uchun «SMS shlyuzi» ruxsati kerak.</p><?php endif; ?>
                    </div>

                    <div class="border-t border-ink-100 pt-5 dark:border-ink-800">
                        <h2 class="flex items-center gap-2 text-base font-semibold text-ink-900 dark:text-white"><span class="bs-tile flex h-8 w-8 items-center justify-center rounded-lg"><?php echo $svg($ic['send']); ?></span> Eskiz akkaunti</h2>
                        <p class="mb-4 mt-2 text-sm text-ink-500 dark:text-ink-400">Bo'sh qoldirilsa, filial umumiy Eskiz akkauntidan foydalanadi. Hozir: <b><?php echo e($branch->hasOwnSmsAccount() ? "filialning o'z akkaunti" : 'umumiy akkaunt'); ?></b>.</p>
                        <div class="grid gap-4">
                            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'eskiz_email','type' => 'email','label' => 'Eskiz email','value' => $branch->eskiz_email]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'eskiz_email','type' => 'email','label' => 'Eskiz email','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->eskiz_email)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'eskiz_password','type' => 'password','label' => 'Eskiz paroli','hint' => $branch->hasOwnSmsAccount() ? 'O\'zgartirmaslik uchun bo\'sh qoldiring.' : null,'autocomplete' => 'new-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'eskiz_password','type' => 'password','label' => 'Eskiz paroli','hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->hasOwnSmsAccount() ? 'O\'zgartirmaslik uchun bo\'sh qoldiring.' : null),'autocomplete' => 'new-password']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['name' => 'eskiz_from','label' => 'Yuboruvchi nomi','value' => $branch->eskiz_from]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'eskiz_from','label' => 'Yuboruvchi nomi','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branch->eskiz_from)]); ?>
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
                    </div>
                    <button class="btn-primary self-start"><?php echo $svg($ic['save']); ?> SMS sozlamalarini saqlash</button>
                </form>

                <div class="space-y-4 xl:col-span-3">
                    <div class="card card-body">
                        <h2 class="flex items-center gap-2 text-base font-semibold text-ink-900 dark:text-white"><span class="bs-tile flex h-8 w-8 items-center justify-center rounded-lg"><?php echo $svg($ic['chart']); ?></span> Modem orqali yuborilgan SMS</h2>
                        <p class="mt-1 text-xs text-ink-500">Faqat USB modem hisoblanadi. Eskiz orqali yuborilganlar bu yerda sanalmaydi.</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rounded-2xl border border-ink-100 p-3 dark:border-ink-700 <?php echo e($i === 0 ? 'bs-tile' : ''); ?>">
                                    <div class="text-xs <?php echo e($i === 0 ? '' : 'text-ink-500'); ?>"><?php echo e($monthName[substr($m, 5, 2)]); ?> <?php echo e(substr($m, 0, 4)); ?><?php echo e($i === 0 ? ' (joriy)' : ''); ?></div>
                                    <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($monthly[$m] ?? 0); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="card card-body">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold text-ink-900 dark:text-white">Oxirgi 14 kun</div>
                            <div class="text-xs text-ink-500">Bugun: <b class="text-ink-800 dark:text-ink-100"><?php echo e($usedToday); ?></b><?php if($limit > 0): ?> / <?php echo e($limit); ?><?php endif; ?></div>
                        </div>
                        <div class="mt-4 flex h-32 items-end gap-1.5">
                            <?php $__currentLoopData = $chart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bs-barwrap relative flex h-full flex-1 items-end">
                                    <div class="bs-bar w-full rounded-t-md" style="--i: <?php echo e($i); ?>; height: <?php echo e(max(4, (int) round($d['count'] / $maxDay * 100))); ?>%; <?php echo e($d['count'] === 0 ? 'opacity:.25' : ''); ?>"></div>
                                    <div class="bs-tip absolute bottom-full left-1/2 z-10 mb-1.5 -translate-x-1/2 whitespace-nowrap rounded-lg bg-ink-900 px-2 py-1 text-xs text-white shadow-lg"><?php echo e($d['date']->format('d.m')); ?> · <?php echo e($d['count']); ?> ta</div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="mt-1.5 flex justify-between text-[11px] text-ink-400"><span><?php echo e($chart[0]['date']->format('d.m')); ?></span><span>Bugun</span></div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if($isSadmin): ?>
            <div x-show="tab === 'payments'" x-cloak class="bs-panel">
                <div class="card overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-100 px-4 py-3 dark:border-ink-700">
                        <div>
                            <div class="text-sm font-semibold text-ink-900 dark:text-white">CRM obuna to'lovlari tarixi</div>
                            <div class="text-xs text-ink-500">Filial tizimdan foydalangani uchun qilgan to'lovlari. Bu ro'yxat tizimning boshqa qismlariga bog'liq emas.</div>
                        </div>
                        <button type="button" class="btn-primary btn-sm" @click="pay = true"><?php echo $svg($ic['plus']); ?> To'lov qabul qilish</button>
                    </div>
                    <?php if($payments->isEmpty()): ?>
                        <div class="flex flex-col items-center gap-3 px-4 py-12 text-center text-sm text-ink-500">
                            <span class="bs-tile flex h-14 w-14 items-center justify-center rounded-2xl"><?php echo $svg($ic['wallet'], 'h-7 w-7'); ?></span>
                            Hali to'lov qabul qilinmagan.
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead><tr><th>Sana</th><th class="text-right">Summa</th><th>Tur</th><th>Izoh</th><th>Kim kiritgan</th></tr></thead>
                                <tbody>
                                <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="bs-row bs-in" style="--i: <?php echo e($loop->index); ?>">
                                        <td class="whitespace-nowrap text-sm"><?php echo e($p->created_at->timezone($tz)->format('d.m.Y H:i')); ?></td>
                                        <td class="whitespace-nowrap text-right font-semibold text-ink-900 dark:text-white"><?php echo e(\App\Support\Format::money($p->amount)); ?></td>
                                        <td><span class="<?php echo e($p->method === 'cash' ? 'badge-green' : 'badge-blue'); ?> inline-flex items-center gap-1"><?php echo $svg($p->method === 'cash' ? $ic['cash'] : $ic['card'], 'h-3.5 w-3.5'); ?> <?php echo e($p->methodLabel()); ?></span></td>
                                        <td class="max-w-xs text-sm text-ink-600 dark:text-ink-300"><?php echo e($p->note ?: '—'); ?></td>
                                        <td class="whitespace-nowrap text-sm"><?php echo e($p->creator?->name ?? '—'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if($payments->hasPages()): ?><div class="border-t border-ink-100 px-4 py-3 dark:border-ink-700"><?php echo e($payments->links()); ?></div><?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            
            <div x-show="pay" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/60 p-4 backdrop-blur-sm">
                <form method="POST" action="<?php echo e(route('branches.payments.store', $branch)); ?>" class="bs-modal card w-full max-w-md overflow-hidden shadow-pop" @click.outside="pay = false">
                    <?php echo csrf_field(); ?>
                    <div class="bs-hero relative flex items-center gap-3 border-b border-ink-100 px-5 py-4 dark:border-ink-700">
                        <span class="bs-tile relative z-10 flex h-11 w-11 items-center justify-center rounded-xl"><?php echo $svg($ic['wallet'], 'h-6 w-6'); ?></span>
                        <div class="relative z-10">
                            <h3 class="text-lg font-semibold text-ink-900 dark:text-white">To'lov qabul qilish</h3>
                            <p class="text-xs text-ink-500"><?php echo e($branch->name); ?> · CRM obunasi</p>
                        </div>
                    </div>
                    <div class="space-y-4 px-5 py-4">
                        <div>
                            <label class="label" for="pay_amount">To'lov summasi (so'm)</label>
                            <input id="pay_amount" type="text" inputmode="numeric" autocomplete="off" placeholder="0" class="input text-lg font-semibold"
                                   :value="fmt(amount)" @input="amount = $event.target.value.replace(/\D/g, '').slice(0, 12); $event.target.value = fmt(amount)" x-init="$watch('pay', v => v && $nextTick(() => $el.focus()))">
                            <input type="hidden" name="amount" :value="amount">
                            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <div class="label">To'lov turi</div>
                            <input type="hidden" name="method" :value="method">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" class="bs-method flex items-center justify-center gap-2 rounded-xl border-2 px-3 py-3 text-sm font-semibold"
                                        :class="method === 'cash' ? 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'border-ink-200 text-ink-600 dark:border-ink-700 dark:text-ink-300'" @click="method = 'cash'">
                                    <?php echo $svg($ic['cash'], 'h-5 w-5'); ?> Naqd
                                </button>
                                <button type="button" class="bs-method flex items-center justify-center gap-2 rounded-xl border-2 px-3 py-3 text-sm font-semibold"
                                        :class="method === 'card' ? 'border-sky-500 bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300' : 'border-ink-200 text-ink-600 dark:border-ink-700 dark:text-ink-300'" @click="method = 'card'">
                                    <?php echo $svg($ic['card'], 'h-5 w-5'); ?> Karta
                                </button>
                            </div>
                            <?php $__errorArgs = ['method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="label" for="pay_note">To'lov haqida (ixtiyoriy)</label>
                            <textarea id="pay_note" name="note" rows="3" maxlength="500" class="input" placeholder="Masalan: noyabr oyi uchun obuna"><?php echo e(old('note')); ?></textarea>
                            <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <p class="text-xs text-ink-400">To'lov saqlangandan keyin o'zgartirib yoki o'chirib bo'lmaydi.</p>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-ink-100 px-5 py-3 dark:border-ink-700">
                        <button type="button" class="btn-secondary" @click="pay = false">Bekor qilish</button>
                        <button class="btn-primary" :disabled="!amount || Number(amount) < 1"><?php echo $svg($ic['check']); ?> To'lovni saqlash</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        
        <div x-show="closing" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/60 p-4 backdrop-blur-sm">
            <div class="bs-modal card card-body w-full max-w-md shadow-pop" @click.outside="closing = false">
                <h3 class="text-lg font-semibold text-ink-900 dark:text-white">Filialni yopish</h3>
                <p class="mt-2 text-sm text-ink-600 dark:text-ink-300"><b><?php echo e($branch->name); ?></b> yopiladi. Ma'lumotlar o'chmaydi, arxivda saqlanadi. Filial xodimlari tizimga kira olmaydi. Keyin qayta ochishingiz mumkin.</p>
                <form method="POST" action="<?php echo e(route('branches.close', $branch)); ?>" class="mt-4 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="label" for="closed_reason">Sabab (ixtiyoriy)</label>
                        <input id="closed_reason" name="closed_reason" maxlength="255" class="input">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn-secondary" @click="closing = false">Bekor qilish</button>
                        <button class="btn-primary">Yopish</button>
                    </div>
                </form>
            </div>
        </div>

        
        <?php if($canDelete): ?>
            <div x-show="deleting" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/60 p-4 backdrop-blur-sm" x-effect="if (!deleting) typed = ''">
                <div class="bs-modal card card-body w-full max-w-md shadow-pop" @click.outside="deleting = false">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/15"><?php echo $svg($ic['alert'], 'h-5 w-5'); ?></span>
                        <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">Filialni butunlay o'chirish</h3>
                    </div>
                    <p class="mt-3 text-sm text-ink-600 dark:text-ink-300"><b><?php echo e($branch->name); ?></b> va unga tegishli <b>BARCHA</b> ma'lumot (hodimlar, o'quvchilar, guruhlar, to'lovlar, kassa, lidlar, SMS, AI suhbatlar, obuna to'lovlari) <b>QAYTARIB BO'LMAYDIGAN</b> tarzda o'chiriladi.</p>
                    <form method="POST" action="<?php echo e(route('branches.destroy', $branch)); ?>" class="mt-4 space-y-4">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <div>
                            <label class="label" for="confirm_name">Tasdiqlash uchun filial nomini aniq yozing: <b><?php echo e($branch->name); ?></b></label>
                            <input id="confirm_name" name="confirm_name" x-model="typed" autocomplete="off" class="input" required>
                            <?php $__errorArgs = ['confirm_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="btn-secondary" @click="deleting = false">Bekor qilish</button>
                            <button class="btn-danger" :disabled="typed !== <?php echo \Illuminate\Support\Js::from($branch->name)->toHtml() ?>">Butunlay o'chirish</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/branches/show.blade.php ENDPATH**/ ?>