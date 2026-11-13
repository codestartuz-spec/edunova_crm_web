<?php $__env->startSection('title', "Ko'chirishni tasdiqlash"); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6">
    <h1 class="text-xl font-semibold">Fayl tekshiruvi</h1>
    <?php if(session('error')): ?><div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>
    <?php if($errors->any()): ?><div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>
    <?php $__currentLoopData = $info['errors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm"><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $info['warnings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm"><?php echo e($w); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="rounded-xl border bg-white p-5 grid grid-cols-2 gap-x-6 gap-y-1 text-sm">
        <div>O'quvchilar: <b><?php echo e(number_format($info['totals']['students'], 0, '', ' ')); ?></b></div>
        <div>Guruhlar: <b><?php echo e($info['totals']['groups']); ?></b></div>
        <div>To'lovlar: <b><?php echo e($info['totals']['payments_count']); ?></b> (<?php echo e(number_format($info['totals']['payments_sum'], 0, '', ' ')); ?> so'm)</div>
        <div>Guruh a'zoliklari: <b><?php echo e($info['totals']['group_students']); ?></b></div>
        <div>Kassa yig'indisi: <b><?php echo e(number_format($info['totals']['wallets_sum'], 0, '', ' ')); ?></b></div>
        <div>Maosh to'lovlari: <b><?php echo e(number_format($info['totals']['payouts_sum'], 0, '', ' ')); ?></b></div>
    </div>

    <details class="rounded-xl border bg-white p-5 text-sm">
        <summary class="cursor-pointer font-medium">Jadvallar (<?php echo e(count($info['tables'])); ?>)</summary>
        <div class="grid grid-cols-2 gap-x-6 mt-3">
            <?php $__currentLoopData = $info['tables']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t => $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="flex justify-between"><span><?php echo e($t); ?></span><span class="text-gray-500"><?php echo e($n); ?></span></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($info['skipped_tables']): ?><p class="mt-3 text-xs text-gray-500">Ko'chirilmaydi: <?php echo e(implode(', ', $info['skipped_tables'])); ?></p><?php endif; ?>
    </details>

    <?php if(!$info['errors']): ?>
    <form method="POST" action="<?php echo e(route('legacy.run')); ?>" class="rounded-xl border border-red-200 bg-white p-5 space-y-4">
        <?php echo csrf_field(); ?>
        <div class="text-sm text-red-700 font-medium">Diqqat: hozirgi tizimdagi barcha ma'lumotlar (sAdmin hisobingiz va sozlamalardan tashqari) o'chiriladi va fayldagi ma'lumotlar bilan almashtiriladi.</div>
        <?php if($info['sadmins']): ?>
        <div>
            <label class="block text-sm font-medium mb-1">Fayldagi qaysi sAdmin sizning hisobingiz bo'ladi?</label>
            <select name="primary" class="border rounded-lg px-3 py-2 text-sm w-full">
                <?php $__currentLoopData = $info['sadmins']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s['id']); ?>" <?php if($loop->first): echo 'selected'; endif; ?>><?php echo e($s['name']); ?> (<?php echo e($s['username']); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-gray-500 mt-1">Qolgan sAdminlar direktorga aylantiriladi va ularga «Direktorlar → Umumiy ruxsatlar» shabloni qo'llanadi. Hodimlarning ruxsatlari ko'chirilmaydi (ruxsatsiz qoladi). Hozirgi login va parolingiz o'zgarmaydi.</p>
        </div>
        <?php endif; ?>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="backup" value="1" checked> Avval hozirgi bazaning zaxirasini olish</label>
        <div class="grid sm:grid-cols-2 gap-3">
            <div><label class="block text-sm font-medium mb-1">Parolingiz</label><input type="password" name="password" required class="border rounded-lg px-3 py-2 text-sm w-full"></div>
            <div><label class="block text-sm font-medium mb-1">Tasdiq: TASDIQLAYMAN deb yozing</label><input name="confirm" required autocomplete="off" class="border rounded-lg px-3 py-2 text-sm w-full"></div>
        </div>
        <button class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm">Ko'chirishni boshlash</button>
    </form>
    <?php endif; ?>
    <a href="<?php echo e(route('legacy.index')); ?>" class="text-sm text-indigo-600">← Orqaga</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/legacy/review.blade.php ENDPATH**/ ?>