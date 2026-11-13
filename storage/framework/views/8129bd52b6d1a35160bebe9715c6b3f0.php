<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Murojaat'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>body{background:transparent}</style>
</head>
<body class="p-0">
<div class="mx-auto max-w-lg p-2"><?php echo $__env->yieldContent('content'); ?></div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/layouts/embed.blade.php ENDPATH**/ ?>