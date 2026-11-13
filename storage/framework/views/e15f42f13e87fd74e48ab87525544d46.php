<?php $__env->startSection('title', 'Import: '.$batch->filename); ?>

<?php $__env->startSection('content'); ?>
    <?php $s = $batch->summary; ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $batch->filename,'subtitle' => ['pending' => 'Tekshiruv natijasi — tasdiqlashdan oldin ko\'rib chiqing', 'done' => 'Import bajarilgan', 'cancelled' => 'Bekor qilingan'][$batch->status]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($batch->filename),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['pending' => 'Tekshiruv natijasi — tasdiqlashdan oldin ko\'rib chiqing', 'done' => 'Import bajarilgan', 'cancelled' => 'Bekor qilingan'][$batch->status])]); ?>
         <?php $__env->slot('actions', null, []); ?> <a href="<?php echo e(route('imports.index')); ?>" class="btn-secondary">Orqaga</a> <?php $__env->endSlot(); ?>
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

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = [['Jami qator', $s['total'], ''], ["Qo'shiladi", $s['ok'], 'text-emerald-600'], ["O'tkazib yuboriladi (takror)", $s['duplicates'], 'text-amber-600'], ['Xato', $s['errors'], 'text-brand-600']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $v, $c]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-body"><div class="text-sm text-ink-500"><?php echo e($l); ?></div><div class="mt-1 text-2xl font-bold <?php echo e($c ?: 'text-ink-900 dark:text-white'); ?>"><?php echo e($v); ?></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if(! empty($s['new_branches'])): ?>
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
            <b>Yangi filiallar ochiladi:</b>
            <ul class="mt-1 list-disc pl-5"><?php $__currentLoopData = $s['new_branches']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li>Yangi filial: <b><?php echo e($name); ?></b> — <?php echo e($count); ?> ta o'quvchi</li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <?php if(! empty($s['by_branch'])): ?>
        <div class="card card-body mt-4 text-sm">
            <b class="text-ink-900 dark:text-white">Filiallar bo'yicha:</b>
            <span class="text-ink-600 dark:text-ink-300"><?php $__currentLoopData = $s['by_branch']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($name); ?>: <?php echo e($count); ?> ta <?php if(! $loop->last): ?>, <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></span>
            <?php if($s['opening_balance'] !== 0): ?><div class="mt-1 text-ink-500">Boshlang'ich balanslar yig'indisi: <?php echo e(\App\Support\Format::money($s['opening_balance'])); ?></div><?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($batch->status === 'pending'): ?>
        <div class="mt-4 flex gap-2">
            <?php if($s['ok'] > 0): ?>
                <form method="POST" action="<?php echo e(route('imports.confirm', $batch)); ?>" onsubmit="return confirm('<?php echo e($s['ok']); ?> ta o\'quvchi qo\'shilsinmi?<?php echo e(! empty($s['new_branches']) ? ' Yangi filiallar ham ochiladi.' : ''); ?>')"><?php echo csrf_field(); ?><button class="btn-primary">Tasdiqlash va import qilish (<?php echo e($s['ok']); ?>)</button></form>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('imports.cancel', $batch)); ?>"><?php echo csrf_field(); ?><button class="btn-secondary">Bekor qilish</button></form>
        </div>
    <?php endif; ?>

    <?php if($batch->status === 'done'): ?>
        <div class="mt-4">
            <?php if($batch->result_path): ?>
                <a href="<?php echo e(route('imports.result', $batch)); ?>" class="btn-primary">Login va parollar faylini yuklab olish</a>
                <p class="hint">Fayl faqat bir marta yuklab olinadi. Parollarni o'quvchilarga bering va faylni o'chiring.</p>
            <?php else: ?>
                <p class="text-sm text-ink-500">Login/parollar fayli yuklab olingan (yoki yaratilmagan).</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($problems->isNotEmpty()): ?>
        <div class="card mt-6 overflow-hidden">
            <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">Muammoli qatorlar</h2>
            <div class="table-wrap mt-3"><table class="table">
                <thead><tr><th>Qator</th><th>F.I.O</th><th>Telefon</th><th>Holat</th><th>Sabab</th></tr></thead>
                <tbody><?php $__currentLoopData = $problems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr><td><?php echo e($r['row']); ?></td><td><?php echo e($r['name']); ?></td><td><?php echo e($r['phone']); ?></td>
                        <td><span class="<?php echo e($r['status'] === 'error' ? 'badge-red' : 'badge-amber'); ?>"><?php echo e($r['status'] === 'error' ? 'Xato' : 'Takror'); ?></span></td><td><?php echo e($r['message']); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody></table></div>
        </div>
    <?php endif; ?>

    <div class="card mt-6 overflow-hidden">
        <h2 class="px-5 pt-5 text-lg font-semibold text-ink-900 dark:text-white sm:px-6">Ko'rib chiqish (dastlabki <?php echo e(count($preview)); ?> qator)</h2>
        <div class="table-wrap mt-3"><table class="table">
            <thead><tr><th>Qator</th><th>Filial</th><th>F.I.O</th><th>Telefon</th><th>Tug'ilgan sana</th><th>Manba</th><th class="text-right">Balans</th><th>Holat</th></tr></thead>
            <tbody><?php $__currentLoopData = $preview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><td><?php echo e($r['row']); ?></td><td><?php echo e($r['branch_name']); ?><?php if($r['is_new_branch']): ?><span class="badge-amber ml-1">yangi</span><?php endif; ?></td><td><?php echo e($r['name']); ?></td><td><?php echo e($r['phone']); ?></td><td><?php echo e($r['birthday']); ?></td><td><?php echo e($r['source']); ?></td>
                    <td class="text-right"><?php echo e(number_format($r['balance'], 0, '', ' ')); ?></td>
                    <td><span class="<?php echo e(['ok' => 'badge-green', 'duplicate' => 'badge-amber', 'error' => 'badge-red'][$r['status']]); ?>"><?php echo e(['ok' => 'OK', 'duplicate' => 'Takror', 'error' => 'Xato'][$r['status']]); ?></span></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody></table></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/imports/show.blade.php ENDPATH**/ ?>