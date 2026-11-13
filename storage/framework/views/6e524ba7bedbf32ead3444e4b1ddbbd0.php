<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chek №<?php echo e($payment->id); ?> · Edunova</title>
    <link rel="icon" href="<?php echo e(asset('favicon.svg')); ?>" type="image/svg+xml">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <?php if($format === '80mm'): ?>
        <style>
            @page { size: 80mm auto; margin: 0; }
            body { width: 76mm; margin: 0 auto; font-size: 11px; }
            .receipt { padding: 3mm; }
        </style>
    <?php else: ?>
        <style>
            @page { size: A5; margin: 12mm; }
            body { font-size: 13px; }
            .receipt { max-width: 480px; margin: 0 auto; }
        </style>
    <?php endif; ?>
</head>
<body class="bg-white text-ink-900">
    <div class="flex justify-center gap-2 border-b border-ink-100 bg-ink-50 p-3 print:hidden">
        <a href="?format=a5" class="btn-sm <?php echo e($format === 'a5' ? 'btn-primary' : 'btn-secondary'); ?>">A5</a>
        <a href="?format=80mm" class="btn-sm <?php echo e($format === '80mm' ? 'btn-primary' : 'btn-secondary'); ?>">80 mm (termoprinter)</a>
        <button onclick="window.print()" class="btn-primary btn-sm">Chop etish</button>
    </div>

    <div class="receipt py-6 leading-snug">
        <div class="text-center">
            <div class="text-base font-extrabold"><?php echo e($payment->branch?->name ?? '—'); ?></div>
            <?php if($payment->branch?->address): ?><div class="text-xs text-ink-500"><?php echo e($payment->branch->address); ?></div><?php endif; ?>
            <?php if($payment->branch?->phone): ?><div class="text-xs text-ink-500"><?php echo e(\App\Support\Format::prettyPhone($payment->branch->phone)); ?></div><?php endif; ?>
        </div>

        <div class="my-3 border-t border-dashed border-ink-300"></div>

        <div class="text-center text-sm font-bold uppercase"><?php echo e($payment->typeLabel()); ?> CHEKI</div>
        <div class="text-center text-xs text-ink-500">№<?php echo e($payment->id); ?> · <?php echo e($payment->created_at->format('d.m.Y H:i')); ?></div>

        <div class="my-3 border-t border-dashed border-ink-300"></div>

        <table class="w-full text-xs">
            <tr><td class="py-0.5 text-ink-500">O'quvchi</td><td class="py-0.5 text-right font-medium"><?php echo e($payment->student?->name ?? '—'); ?></td></tr>
            <?php if($payment->student?->phone): ?><tr><td class="py-0.5 text-ink-500">Telefon</td><td class="py-0.5 text-right"><?php echo e(\App\Support\Format::prettyPhone($payment->student->phone)); ?></td></tr><?php endif; ?>
            <?php if($payment->group): ?><tr><td class="py-0.5 text-ink-500">Guruh</td><td class="py-0.5 text-right"><?php echo e($payment->group->name); ?></td></tr><?php endif; ?>
            <?php if($payment->method): ?><tr><td class="py-0.5 text-ink-500">Usul</td><td class="py-0.5 text-right"><?php echo e($payment->method->label()); ?></td></tr><?php endif; ?>
            <?php if($payment->description): ?><tr><td class="py-0.5 text-ink-500">Izoh</td><td class="py-0.5 text-right"><?php echo e($payment->description); ?></td></tr><?php endif; ?>
        </table>

        <div class="my-3 border-t border-dashed border-ink-300"></div>

        <div class="flex items-baseline justify-between">
            <span class="font-semibold">Summa</span>
            <span class="text-lg font-extrabold"><?php echo e($payment->type === 'refund' ? '−' : ''); ?><?php echo e(\App\Support\Format::money($payment->amount)); ?></span>
        </div>
        <?php if($payment->student): ?>
            <div class="mt-1 flex items-baseline justify-between text-xs text-ink-500">
                <span>Joriy balans</span>
                <span class="font-medium <?php echo e($payment->student->balance < 0 ? 'text-brand-600' : ''); ?>"><?php echo e(\App\Support\Format::money($payment->student->balance)); ?></span>
            </div>
        <?php endif; ?>

        <div class="my-3 border-t border-dashed border-ink-300"></div>

        <div class="text-center text-xs text-ink-500">
            Kassir: <?php echo e($payment->creator?->name ?? '—'); ?>

            <?php if($payment->reversed_at): ?><div class="mt-1 font-semibold text-brand-600">STORNOLANGAN</div><?php endif; ?>
        </div>
        <div class="mt-4 text-center text-xs text-ink-400">Xaridingiz uchun rahmat!</div>
    </div>

    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 200));</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/payments/receipt.blade.php ENDPATH**/ ?>