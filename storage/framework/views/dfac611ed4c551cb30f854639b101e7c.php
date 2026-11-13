<?php $__env->startSection('title', 'Davomad statistikasi'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Davomad statistikasi','subtitle' => 'Kunlik va oylik tahlil']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Davomad statistikasi','subtitle' => 'Kunlik va oylik tahlil']); ?>
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
        <div><label class="label">Kun</label><input type="date" name="date" value="<?php echo e($date); ?>" class="input" onchange="this.form.submit()"></div>
        <div><label class="label">Oy</label><input type="month" name="month" value="<?php echo e($month); ?>" class="input" onchange="this.form.submit()"></div>
    </form>

    <h2 class="mb-3 text-lg font-semibold text-ink-900 dark:text-white">Kunlik: <?php echo e(\Carbon\Carbon::parse($date)->format('d.m.Y')); ?></h2>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = [['Dars bo\'lgan guruhlar', $daily['totals']['groups']], ['Davomad olingan', $daily['totals']['taken'].' / '.$daily['totals']['groups']], ['Keldi', $daily['totals']['present']], ['Kelmadi', $daily['totals']['absent']]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $v]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-body"><div class="text-sm text-ink-500"><?php echo e($l); ?></div><div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($v); ?></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="mt-4 grid gap-6 lg:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Kelganlar va kelmaganlar','subtitle' => 'Shu kunda davomad olingan o\'quvchilar','spec' => $charts['day_split'],'height' => 'h-64']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kelganlar va kelmaganlar','subtitle' => 'Shu kunda davomad olingan o\'quvchilar','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['day_split']),'height' => 'h-64']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['class' => 'lg:col-span-2','title' => 'Guruhlar bo\'yicha davomad','subtitle' => 'Shu kundagi kelganlar foizi (davomad olingan guruhlar)','spec' => $charts['day_groups'],'height' => $charts['day_groups_h']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2','title' => 'Guruhlar bo\'yicha davomad','subtitle' => 'Shu kundagi kelganlar foizi (davomad olingan guruhlar)','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['day_groups']),'height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['day_groups_h'])]); ?>
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
    <div class="card mt-4 overflow-hidden">
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Guruh</th><th>O'qituvchi</th><th>Holat</th><th>Keldi</th><th>Kelmadi</th><th>Foiz</th></tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $daily['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><a href="<?php echo e(route('groups.show', $r['group'])); ?>" class="font-medium hover:text-brand-600"><?php echo e($r['group']->name); ?></a></td>
                        <td><?php echo e($r['group']->teacher->name); ?></td>
                        <td><span class="<?php echo e($r['taken'] ? 'badge-green' : 'badge-amber'); ?>"><?php echo e($r['taken'] ? 'Olingan' : 'Olinmagan'); ?></span></td>
                        <td><?php echo e($r['present']); ?></td><td><?php echo e($r['absent']); ?></td>
                        <td><?php echo e($r['rate'] === null ? '—' : $r['rate'].'%'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="py-8 text-center text-ink-500">Bu kunda dars bo'lgan guruh yo'q.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2 class="mb-3 mt-10 text-lg font-semibold text-ink-900 dark:text-white">Oylik: <?php echo e($month); ?></h2>
    <div class="grid gap-4 sm:grid-cols-3">
        <?php $__currentLoopData = [['Guruhlar', $monthly['totals']['groups']], ['Umumiy davomad', $monthly['totals']['rate'] === null ? '—' : $monthly['totals']['rate'].'%'], ['Kelmagan (jami)', $monthly['totals']['absent']]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $v]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-body"><div class="text-sm text-ink-500"><?php echo e($l); ?></div><div class="mt-1 text-2xl font-bold text-ink-900 dark:text-white"><?php echo e($v); ?></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($monthly['series']): ?>
        <div class="mt-4 grid gap-6 lg:grid-cols-2">
            <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Kunlar bo\'yicha davomad foizi','subtitle' => 'Har bir dars kunida kelganlar ulushi','spec' => $charts['month_rate'],'height' => 'h-64']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kunlar bo\'yicha davomad foizi','subtitle' => 'Har bir dars kunida kelganlar ulushi','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['month_rate']),'height' => 'h-64']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Kelganlar va kelmaganlar','subtitle' => 'Kunlar bo\'yicha o\'quvchilar soni','spec' => $charts['month_split'],'height' => 'h-64']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Kelganlar va kelmaganlar','subtitle' => 'Kunlar bo\'yicha o\'quvchilar soni','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['month_split']),'height' => 'h-64']); ?>
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
    <?php else: ?>
        <div class="card card-body mt-4 text-sm text-ink-500">Bu oyda davomad yozuvlari yo'q.</div>
    <?php endif; ?>

    <div class="mt-4 grid gap-6 lg:grid-cols-2">
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Guruhlar bo\'yicha davomad','subtitle' => 'Oy davomida kelganlar foizi (yuqoridan pastga: yaxshidan yomonga)','spec' => $charts['month_groups'],'height' => $charts['month_groups_h']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Guruhlar bo\'yicha davomad','subtitle' => 'Oy davomida kelganlar foizi (yuqoridan pastga: yaxshidan yomonga)','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['month_groups']),'height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['month_groups_h'])]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Davomad olinganmi?','subtitle' => 'Rejalangan dars kunlari va davomad olingan kunlar. Farq bo\'lsa, davomad olinmagan kunlar bor.','spec' => $charts['held'],'height' => $charts['held_h']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Davomad olinganmi?','subtitle' => 'Rejalangan dars kunlari va davomad olingan kunlar. Farq bo\'lsa, davomad olinmagan kunlar bor.','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['held']),'height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['held_h'])]); ?>
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
    <div class="mt-4">
        <?php if (isset($component)) { $__componentOriginal532065c9db293a8f0b2c861906b0fd39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal532065c9db293a8f0b2c861906b0fd39 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dash.card','data' => ['title' => 'Eng ko\'p qoldirayotgan o\'quvchilar','subtitle' => 'Kamida 3 ta yozuvi bor o\'quvchilar; foiz — kelganlar ulushi (past bo\'lsa, xavf yuqori)','spec' => $charts['worst'],'height' => $charts['worst_h']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dash.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Eng ko\'p qoldirayotgan o\'quvchilar','subtitle' => 'Kamida 3 ta yozuvi bor o\'quvchilar; foiz — kelganlar ulushi (past bo\'lsa, xavf yuqori)','spec' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['worst']),'height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($charts['worst_h'])]); ?>
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

    <h3 class="mb-2 mt-8 text-base font-semibold text-ink-900 dark:text-white">Guruhlar bo'yicha batafsil</h3>
    <div class="mt-2 grid gap-6">
        <div class="card overflow-hidden">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Guruh</th><th>Rejalangan kunlar</th><th>Davomad olingan</th><th>Keldi</th><th>Kelmadi</th><th>Foiz</th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $monthly['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><a href="<?php echo e(route('groups.show', $r['group'])); ?>" class="font-medium hover:text-brand-600"><?php echo e($r['group']->name); ?></a></td>
                            <td><?php echo e($r['scheduled']); ?></td>
                            <td class="<?php echo e($r['held'] < $r['scheduled'] ? 'text-amber-600 font-semibold' : ''); ?>"><?php echo e($r['held']); ?></td>
                            <td><?php echo e($r['present']); ?></td><td><?php echo e($r['absent']); ?></td>
                            <td><?php echo e($r['rate'] === null ? '—' : $r['rate'].'%'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="py-8 text-center text-ink-500">Bu oyda guruh yo'q.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->startPush('scripts'); ?><?php echo app('Illuminate\Foundation\Vite')('resources/js/charts.js'); ?><?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/attendance/stats.blade.php ENDPATH**/ ?>