<link rel="manifest" href="<?php echo e(route('pwa.manifest')); ?>">
<meta name="theme-color" content="<?php echo e(\App\Support\BrandTheme::currentHex() ?? '#e11d48'); ?>">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?php echo e(\App\Support\Brand::short()); ?>">
<link rel="apple-touch-icon" href="<?php echo e(\App\Support\Brand::iconUrl(180)); ?>">
<script>window.PWA_SW = <?php echo \Illuminate\Support\Js::from(route('sw'))->toHtml() ?>;</script>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/partials/pwa-head.blade.php ENDPATH**/ ?>