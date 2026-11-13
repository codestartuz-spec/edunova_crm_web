<?php $__env->startSection('title', 'Statistika'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Statistika','subtitle' => 'Aniq ta\'riflar bilan hisoblangan asosiy ko\'rsatkichlar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Statistika','subtitle' => 'Aniq ta\'riflar bilan hisoblangan asosiy ko\'rsatkichlar']); ?>
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

    <form method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end">
        <div><label class="label">Boshlanishi</label><input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="input"></div>
        <div><label class="label">Tugashi</label><input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="input"></div>
        <button class="btn-secondary">Ko'rsatish</button>
        <div class="flex gap-1 sm:ml-2">
            <a class="btn-ghost btn-sm" href="<?php echo e(route('statistics.index', ['from' => today()->startOfMonth()->toDateString(), 'to' => today()->toDateString()])); ?>">Shu oy</a>
            <a class="btn-ghost btn-sm" href="<?php echo e(route('statistics.index', ['from' => today()->subMonthNoOverflow()->startOfMonth()->toDateString(), 'to' => today()->subMonthNoOverflow()->endOfMonth()->toDateString()])); ?>">O'tgan oy</a>
            <a class="btn-ghost btn-sm" href="<?php echo e(route('statistics.index', ['from' => today()->startOfYear()->toDateString(), 'to' => today()->toDateString()])); ?>">Shu yil</a>
        </div>
    </form>

    <div class="mb-3 flex items-center gap-3 text-sm text-ink-500">
        <span>Yangilandi: <b data-kpi-updated><?php echo e($updatedAt->format('H:i')); ?></b></span>
        <form method="POST" action="<?php echo e(route('statistics.refresh')); ?>"><?php echo csrf_field(); ?><button class="btn-ghost btn-sm">Hozir yangilash</button></form>
    </div>

    <?php $m = fn ($v) => \App\Support\Format::money($v); ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" data-kpi-feed="<?php echo e(route('statistics.data', ['from' => $from->toDateString(), 'to' => $to->toDateString()])); ?>">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $good = $k['delta'] !== null && (($k['delta'] >= 0) xor $k['invert']);
                $deltaText = $k['delta'] === null ? null : ($k['delta'] > 0 ? '+' : '').$k['delta'].($k['unit'] === 'percent' ? ' p.p.' : '%');
            ?>
            <div class="card card-body relative overflow-hidden" data-kpi="<?php echo e($k['key']); ?>">
                <div class="flex items-start justify-between gap-2">
                    <div class="text-sm text-ink-500"><?php echo e($k['label']); ?></div>
                    <?php if($deltaText !== null): ?>
                        <span class="shrink-0 whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-semibold <?php echo e($k['delta'] == 0 ? 'bg-ink-100 text-ink-500 dark:bg-ink-800' : ($good ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400')); ?>"
                              title="Oldingi shunday davrga nisbatan"><?php echo e($k['delta'] > 0 ? '▲' : ($k['delta'] < 0 ? '▼' : '•')); ?> <?php echo e($deltaText); ?></span>
                    <?php endif; ?>
                </div>
                <?php if($k['value'] === null): ?>
                    <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white">—</div>
                <?php else: ?>
                    <div class="mt-1 break-words text-2xl font-bold tabular-nums <?php echo e($k['color'] ?: 'text-ink-900 dark:text-white'); ?>"
                         data-countup="<?php echo e($k['value']); ?>" data-unit="<?php echo e($k['unit']); ?>"><?php echo e($k['unit'] === 'money' ? $m($k['value']) : ($k['unit'] === 'percent' ? $k['value'].'%' : $k['value'])); ?></div>
                <?php endif; ?>
                <div class="mt-1 pr-24 text-xs text-ink-400"><?php echo e($k['note']); ?></div>
                <?php if($k['spark']): ?>
                    <?php if (isset($component)) { $__componentOriginal5dc3a5260baaef287f132290a5527c6f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5dc3a5260baaef287f132290a5527c6f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.spark','data' => ['points' => $k['spark'],'class' => 'absolute bottom-3 right-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.spark'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['points' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($k['spark']),'class' => 'absolute bottom-3 right-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5dc3a5260baaef287f132290a5527c6f)): ?>
<?php $attributes = $__attributesOriginal5dc3a5260baaef287f132290a5527c6f; ?>
<?php unset($__attributesOriginal5dc3a5260baaef287f132290a5527c6f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5dc3a5260baaef287f132290a5527c6f)): ?>
<?php $component = $__componentOriginal5dc3a5260baaef287f132290a5527c6f; ?>
<?php unset($__componentOriginal5dc3a5260baaef287f132290a5527c6f); ?>
<?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($showMoney): ?>
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <?php $__currentLoopData = ['today' => 'Bugun', 'week' => 'Shu hafta', 'month' => 'Shu oy']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $sn = $snapshot[$k]; ?>
                <div class="card card-body">
                    <div class="text-sm text-ink-500"><?php echo e($lbl); ?> — sof tushum</div>
                    <div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($m($sn['net'])); ?></div>
                    <div class="mt-1 text-xs text-ink-400">
                        <?php echo e($sn['count']); ?> ta to'lov · avvalgi shu muddat: <?php echo e($m($sn['previous'])); ?>

                        <?php if($sn['change'] !== null): ?>
                            <span class="<?php echo e($sn['change'] >= 0 ? 'text-emerald-600' : 'text-brand-600'); ?> font-semibold"><?php echo e($sn['change'] > 0 ? '+' : ''); ?><?php echo e($sn['change']); ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6">
            <div class="mb-3 flex items-center gap-1 text-xs font-semibold">
                <?php $__currentLoopData = ['day' => 'Kunlar (30 kun)', 'week' => 'Haftalar (12 hafta)', 'month' => 'Oylar (12 oy)']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(request()->fullUrlWithQuery(['dyn' => $k])); ?>" class="rounded-lg px-3 py-1.5 <?php echo e($dyn === $k ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300'); ?>"><?php echo e($lbl); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Tushum dinamikasi (aniq)','subtitle' => 'O\'rtacha sof tushum: '.$m($charts['dynamics_average']).' · davr tanlovidan mustaqil','spec' => $charts['dynamics'],'table' => $charts['dynamics_table']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tushum dinamikasi (aniq)','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('O\'rtacha sof tushum: '.$m($charts['dynamics_average']).' · davr tanlovidan mustaqil'),'spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['dynamics']),'table' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['dynamics_table'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['class' => 'lg:col-span-2','title' => 'Tushum dinamikasi','subtitle' => ($charts['income_unit'] === 'day' ? 'Kunlar' : 'Oylar').' bo\'yicha: naqt va plastik ('.$from->format('d.m.Y').' – '.$to->format('d.m.Y').')','spec' => $charts['income']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2','title' => 'Tushum dinamikasi','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($charts['income_unit'] === 'day' ? 'Kunlar' : 'Oylar').' bo\'yicha: naqt va plastik ('.$from->format('d.m.Y').' – '.$to->format('d.m.Y').')'),'spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['income'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'To\'lov usuli','subtitle' => 'Tanlangan davr tushumi: naqt va plastik ulushi','spec' => $charts['method']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'To\'lov usuli','subtitle' => 'Tanlangan davr tushumi: naqt va plastik ulushi','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['method'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
        </div>
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Guruhlar bo\'yicha tushum','subtitle' => 'Guruh ko\'rsatilgan to\'lovlar (qaytarishlar ayirilgan), eng ko\'pi yuqorida','spec' => $charts['top_groups']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Guruhlar bo\'yicha tushum','subtitle' => 'Guruh ko\'rsatilgan to\'lovlar (qaytarishlar ayirilgan), eng ko\'pi yuqorida','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['top_groups'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => $showFinance ? 'Tushum, xarajat va ish haqi (12 oy)' : 'Oylik sof tushum (12 oy)','subtitle' => 'Oylar bo\'yicha solishtirish','spec' => $charts['flow']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showFinance ? 'Tushum, xarajat va ish haqi (12 oy)' : 'Oylik sof tushum (12 oy)'),'subtitle' => 'Oylar bo\'yicha solishtirish','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['flow'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if($showFinance): ?>
        <div class="mt-6">
            <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Foyda dinamikasi (12 oy)','subtitle' => 'Sof tushum − xarajat − ish haqi (taxminiy; manfiy bo\'lsa zarar)','spec' => $charts['profit'],'height' => 'h-64']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Foyda dinamikasi (12 oy)','subtitle' => 'Sof tushum − xarajat − ish haqi (taxminiy; manfiy bo\'lsa zarar)','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['profit']),'height' => 'h-64']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['class' => 'lg:col-span-2','title' => 'O\'quvchi va murojaatlar (12 oy)','subtitle' => 'Yangi o\'quvchilar, kelgan murojaatlar va qabul qilinganlar','spec' => $charts['people']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2','title' => 'O\'quvchi va murojaatlar (12 oy)','subtitle' => 'Yangi o\'quvchilar, kelgan murojaatlar va qabul qilinganlar','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['people'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
        <div class="card card-body" x-data="{ view: 'funnel' }">
            <?php
                $f = $charts['funnel'];
                $stages = [['label' => 'Murojaat', 'value' => $f['total']], ['label' => "Ko'rib chiqilgan", 'value' => $f['in_progress'] + $f['converted']], ['label' => 'Qabul qilingan', 'value' => $f['converted']]];
            ?>
            <div class="mb-4 flex items-start justify-between gap-3">
                <div><h3 class="text-base font-semibold text-ink-900 dark:text-white">Varonka</h3><p class="mt-0.5 text-xs text-ink-500 dark:text-ink-400">Tanlangan davrdagi murojaatlar</p></div>
                <div class="flex shrink-0 rounded-lg bg-ink-100 p-0.5 text-xs font-semibold dark:bg-ink-800">
                    <button type="button" @click="view = 'funnel'" :class="view === 'funnel' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-500'" class="rounded-md px-2.5 py-1">Grafik</button>
                    <button type="button" @click="view = 'table'" :class="view === 'table' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-500'" class="rounded-md px-2.5 py-1">Jadval</button>
                </div>
            </div>
            <div x-show="view === 'funnel'">
                <?php if($f['total'] > 0): ?><?php if (isset($component)) { $__componentOriginalec297bf2307c22bf6d7fc76cc19145a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.funnel','data' => ['stages' => $stages,'height' => 60,'width' => 300]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.funnel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stages),'height' => 60,'width' => 300]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3)): ?>
<?php $attributes = $__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3; ?>
<?php unset($__attributesOriginalec297bf2307c22bf6d7fc76cc19145a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalec297bf2307c22bf6d7fc76cc19145a3)): ?>
<?php $component = $__componentOriginalec297bf2307c22bf6d7fc76cc19145a3; ?>
<?php unset($__componentOriginalec297bf2307c22bf6d7fc76cc19145a3); ?>
<?php endif; ?><?php else: ?><div class="flex h-56 items-center justify-center rounded-xl bg-ink-50 text-sm text-ink-400 dark:bg-ink-800/40">Bu davrda ma'lumot yo'q</div><?php endif; ?>
            </div>
            <div x-show="view === 'table'" x-cloak class="table-wrap rounded-xl border border-ink-100 dark:border-ink-800">
                <table class="table"><thead><tr><th>Holat</th><th class="text-right">Soni</th></tr></thead><tbody>
                    <tr><td>Jami murojaat</td><td class="text-right tabular-nums"><?php echo e($f['total']); ?></td></tr>
                    <tr><td>Yangi (ko'rilmagan)</td><td class="text-right tabular-nums"><?php echo e($f['new']); ?></td></tr>
                    <tr><td>Ko'rib chiqilmoqda</td><td class="text-right tabular-nums"><?php echo e($f['in_progress']); ?></td></tr>
                    <tr><td>Qabul qilingan</td><td class="text-right tabular-nums"><?php echo e($f['converted']); ?></td></tr>
                    <tr><td>Bekor qilingan</td><td class="text-right tabular-nums"><?php echo e($f['cancelled']); ?></td></tr>
                </tbody></table>
            </div>
            <a href="<?php echo e(route('leads.index')); ?>" class="mt-3 text-xs font-semibold text-brand-600 hover:underline">Varonka sahifasiga o'tish →</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Kurslar bo\'yicha faol o\'quvchilar','subtitle' => 'Hozir guruhlarda o\'qiyotganlar (davrga bog\'liq emas)','spec' => $charts['courses']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kurslar bo\'yicha faol o\'quvchilar','subtitle' => 'Hozir guruhlarda o\'qiyotganlar (davrga bog\'liq emas)','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['courses'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Qarzdorlar','subtitle' => 'Qarz miqdori bo\'yicha taqsimot (hozirgi holat)','spec' => $charts['debt'],'table' => $charts['debt_table']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Qarzdorlar','subtitle' => 'Qarz miqdori bo\'yicha taqsimot (hozirgi holat)','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['debt']),'table' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['debt_table'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Davomad (6 oy)','subtitle' => 'Oylar bo\'yicha kelganlar ulushi','spec' => $charts['attendance'],'height' => 'h-60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Davomad (6 oy)','subtitle' => 'Oylar bo\'yicha kelganlar ulushi','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['attendance']),'height' => 'h-60']); ?>
            <a href="<?php echo e(route('attendance.stats')); ?>" class="mt-3 text-xs font-semibold text-brand-600 hover:underline">Batafsil davomad statistikasi →</a>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Murojaat manbalari','subtitle' => 'Tanlangan davrdagi murojaatlar qaerdan kelgan','spec' => $charts['sources'],'height' => 'h-60']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Murojaat manbalari','subtitle' => 'Tanlangan davrdagi murojaatlar qaerdan kelgan','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['sources']),'height' => 'h-60']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
    </div>

    <?php if($comparison): ?>
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <?php if(isset($charts['branches'])): ?><?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Filiallar: sof tushum','subtitle' => 'Tanlangan davr','spec' => $charts['branches']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Filiallar: sof tushum','subtitle' => 'Tanlangan davr','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['branches'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?><?php endif; ?>
            <?php if(isset($charts['branch_students'])): ?><?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Filiallar: o\'quvchilar','subtitle' => 'Faol va davrda yangi qo\'shilgan','spec' => $charts['branch_students']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Filiallar: o\'quvchilar','subtitle' => 'Faol va davrda yangi qo\'shilgan','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['branch_students'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $attributes = $__attributesOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__attributesOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal532065c9db293a8f0b2c861906b0fd39)): ?>
<?php $component = $__componentOriginal532065c9db293a8f0b2c861906b0fd39; ?>
<?php unset($__componentOriginal532065c9db293a8f0b2c861906b0fd39); ?>
<?php endif; ?><?php endif; ?>
        </div>
        <div class="card mt-6 overflow-hidden">
            <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">Filiallar solishtiruvi</h2>
            <div class="table-wrap mt-3"><table class="table">
                <thead><tr><th>Filial</th><th class="text-right">Sof tushum</th><th class="text-right">Yangi o'quvchi</th><th class="text-right">Faol o'quvchi</th><th class="text-right">Qarz</th></tr></thead>
                <tbody>
                <?php $__currentLoopData = $comparison; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr><td class="font-medium"><?php echo e($c['branch']); ?> <?php if($c['archived']): ?><span class="badge-gray ml-1">arxiv</span><?php endif; ?></td>
                        <td class="text-right"><?php echo e($m($c['net_income'])); ?></td><td class="text-right"><?php echo e($c['new_students']); ?></td><td class="text-right"><?php echo e($c['students']); ?></td><td class="text-right text-brand-600"><?php echo e($m($c['debt'])); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody></table></div>
        </div>
    <?php endif; ?>

    <div class="mt-6 space-y-1 rounded-xl bg-ink-100 p-4 text-xs leading-relaxed text-ink-600 dark:bg-ink-800 dark:text-ink-300">
        <b>Ta'riflar.</b>
        <?php if($showMoney): ?><p>Tushum — «to'lov» turidagi yozuvlar (naqt + plastik). Sof tushum = tushum − qaytarilgan.</p><?php endif; ?>
        <?php if($showFinance): ?><p>Xarajat — tasdiqlangan kassa xarajatlari + moliya balansidan xarajatlar. Foyda pul harakati asosida hisoblanadi (o'quvchi qarzlari va hisoblangan, lekin to'lanmagan ish haqi kirmaydi).</p><?php endif; ?>
        <p>Qarzdorlar va faol o'quvchilar — hozirgi holat, davrga bog'liq emas. Davomad — tanlangan davrdagi barcha yozuvlar bo'yicha kelganlar ulushi.</p>
    </div>
<?php $__env->startPush('scripts'); ?><?php echo app('Illuminate\Foundation\Vite')('resources/js/charts.js'); ?><?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/statistics/index.blade.php ENDPATH**/ ?>