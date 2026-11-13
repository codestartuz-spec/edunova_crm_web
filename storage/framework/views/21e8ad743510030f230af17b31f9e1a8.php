<?php $today = today()->toDateString(); $canEditPast = auth()->user()->can('editPastAttendance', $group); ?>
<div class="table-wrap">
    <table class="table">
        <thead>
        <tr>
            <th class="w-10">№</th>
            <th class="sticky left-0 z-10 bg-ink-50 dark:bg-ink-900">O'quvchi</th>
            <?php $__currentLoopData = $matrix['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $cls = match ($d['state']) {
                        'held' => '', 'missed' => 'text-amber-600', 'today' => 'text-brand-600', default => 'text-ink-300',
                    };
                ?>
                <th class="text-center <?php echo e($cls); ?>" title="<?php echo e(['held' => 'Davomad olingan', 'missed' => 'Davomad olinmagan', 'today' => 'Bugun', 'upcoming' => 'Kelgusi dars'][$d['state']]); ?>">
                    <?php if($canEditPast && in_array($d['state'], ['held', 'missed'], true)): ?>
                        <a href="<?php echo e(route('attendance.past.show', ['group' => $group, 'date' => $d['date']])); ?>" class="hover:underline"><?php echo e(\Carbon\Carbon::parse($d['date'])->format('d.m')); ?></a>
                    <?php else: ?>
                        <?php echo e(\Carbon\Carbon::parse($d['date'])->format('d.m')); ?>

                    <?php endif; ?>
                </th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th class="text-center">%</th>
        </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $matrix['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="text-ink-400"><?php echo e($loop->iteration); ?></td>
                <td class="sticky left-0 whitespace-nowrap bg-white font-medium text-ink-900 dark:bg-ink-900 dark:text-white">
                    <?php echo e($row['student']->name); ?> <?php if (! ($row['active'])): ?><span class="badge-gray ml-1">chiqqan</span><?php endif; ?>
                </td>
                <?php $__currentLoopData = $matrix['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="text-center">
                        <?php if(array_key_exists($d['date'], $row['cells'])): ?>
                            <?php if($row['cells'][$d['date']]): ?><span class="text-emerald-600" title="Keldi">●</span><?php else: ?><span class="text-brand-600" title="Kelmadi">✕</span><?php endif; ?>
                        <?php else: ?><span class="text-ink-300">·</span><?php endif; ?>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td class="text-center font-semibold"><?php echo e($row['rate'] === null ? '—' : $row['rate'].'%'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="<?php echo e(count($matrix['days']) + 3); ?>" class="py-10 text-center text-ink-500">Davomad ma'lumoti yo'q.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<p class="px-5 pb-4 pt-2 text-xs text-ink-400"><span class="text-emerald-600">●</span> keldi &nbsp; <span class="text-brand-600">✕</span> kelmadi &nbsp; · yozuv yo'q &nbsp; <span class="text-amber-600">sariq sana</span> — davomad olinmagan</p>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/groups/_matrix.blade.php ENDPATH**/ ?>