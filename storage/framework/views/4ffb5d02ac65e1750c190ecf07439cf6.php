<?php $__env->startSection('title', "Eski bazani ko'chirish"); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-xl font-semibold">Eski bazani ko'chirish</h1>
        <p class="text-sm text-gray-500 mt-1">Eski tizimning (.sql yoki .sql.gz) zaxira faylini yuklang. Jadvallar tuzilmasi o'zgarmaydi — faqat ma'lumotlar ko'chiriladi. Telegram bot, SMS shlyuzi, huquqiy hujjatlar va sertifikat sozlamalari saqlanadi. SMS tarixi va navbati ko'chirilmaydi (eskilari tozalanadi). Rasm/video/audio fayllar ko'chirilmaydi.</p>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>
    <?php if($errors->any()): ?><div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>

    <form method="POST" action="<?php echo e(route('legacy.upload')); ?>" enctype="multipart/form-data" class="rounded-xl border bg-white p-5 space-y-3">
        <?php echo csrf_field(); ?>
        <label class="block text-sm font-medium">Baza fayli</label>
        <input type="file" name="dump" accept=".sql,.gz" required class="block w-full text-sm">
        <p class="text-xs text-gray-500">Katta fayl bo'lsa, PHP <code>upload_max_filesize</code> va <code>post_max_size</code> sozlamalarini oshiring (yoki <code>php artisan app:import-legacy fayl.sql</code> ishlating).</p>
        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">Yuklash va tekshirish</button>
    </form>

    <?php if($report): ?>
    <div class="rounded-xl border bg-white p-5 space-y-3">
        <h2 class="font-semibold">Oxirgi ko'chirish hisoboti <span class="text-xs text-gray-500 font-normal">(<?php echo e($report['finished_at']); ?>, <?php echo e($report['seconds']); ?> s)</span></h2>
        <ul class="text-sm space-y-1">
            <?php $__currentLoopData = $report['checks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="<?php echo e($c['ok'] ? 'text-green-700' : 'text-red-700'); ?>"><?php echo e($c['ok'] ? '✔' : '✘'); ?> <?php echo e($c['label']); ?>: <?php echo e($c['detail']); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <?php if(!empty($report['directors'])): ?>
            <p class="text-sm">Direktorga aylantirilgan sAdminlar (umumiy direktor ruxsatlari berildi): <?php echo e(collect($report['directors'])->pluck('name')->implode(', ')); ?></p>
        <?php endif; ?>
        <p class="text-sm text-gray-600">Ko'chirilmagan jadvallar: <?php echo e(implode(', ', $report['skipped_tables']) ?: '—'); ?>. Eski bazadagi ruxsatlar (hodim va direktorlar) ko'chirilmadi: <?php echo e($report['dropped_permissions']); ?> ta. Hodimlar ruxsatsiz, direktorlar umumiy shablon bilan.</p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/legacy/index.blade.php ENDPATH**/ ?>