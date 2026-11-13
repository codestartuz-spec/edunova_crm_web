
<?php
    $isTeacher = $me->role === \App\Enums\Role::Teacher;

    $groups = [
        [
            'key' => 'learn', 'title' => "O'quv jarayoni", 'icon' => 'cap',
            'items' => [
                ['label' => "O'quvchilar", 'href' => route('students.index'), 'icon' => 'cap', 'active' => request()->routeIs('students.*'), 'show' => $me->can('students.view')],
                ['label' => $isTeacher ? 'Guruhlarim' : 'Guruhlar', 'href' => route('groups.index'), 'icon' => 'users', 'active' => request()->routeIs('groups.*'), 'show' => $me->can('viewAny', \App\Models\Group::class)],
                ['label' => 'Bugungi davomad', 'href' => route('attendance.today'), 'icon' => 'calendar', 'active' => request()->routeIs('attendance.today'), 'show' => $me->can('attendance.view')],
                ['label' => 'Davomad statistikasi', 'href' => route('attendance.stats'), 'icon' => 'chart', 'active' => request()->routeIs('attendance.stats'), 'show' => $me->can('attendance.stats')],
            ],
        ],
        [
            'key' => 'sales', 'title' => 'Murojaat va aloqa', 'icon' => 'funnel',
            'items' => [
                ['label' => 'Varonka', 'href' => route('leads.index'), 'icon' => 'funnel', 'active' => request()->routeIs('leads.*'), 'show' => $me->can('leads.view')],
                ['label' => 'SMS', 'href' => route('sms.index'), 'icon' => 'message', 'active' => request()->routeIs('sms.*'), 'show' => $me->canany(['sms.view', 'sms.send', 'sms.manage'])],
            ],
        ],
        [
            'key' => 'money', 'title' => 'Moliya', 'icon' => 'wallet',
            'items' => [
                ['label' => "To'lovlar", 'href' => route('payments.index'), 'icon' => 'banknote', 'active' => request()->routeIs('payments.*'), 'show' => $me->can('payments.view')],
                ['label' => 'Kassa', 'href' => route('cashbox.index'), 'icon' => 'wallet', 'active' => request()->routeIs('cashbox.*'), 'show' => $me->can('cashbox.view')],
                ['label' => 'Moliya', 'href' => route('finance.index'), 'icon' => 'chart', 'active' => request()->routeIs('finance.*'), 'show' => $me->can('finance.view')],
            ],
        ],
        [
            'key' => 'analytics', 'title' => 'Tahlil', 'icon' => 'chart',
            'items' => [
                ['label' => 'Statistika', 'href' => route('statistics.index'), 'icon' => 'chart', 'active' => request()->routeIs('statistics.*'), 'show' => $me->can('statistics.view')],
                ['label' => 'Sertifikatlar', 'href' => route('certificates.index'), 'icon' => 'file', 'active' => request()->routeIs('certificates.*'), 'show' => $me->can('certificates.issue')],
                ['label' => 'Hisobotlar', 'href' => route('reports.index'), 'icon' => 'log', 'active' => request()->routeIs('reports.*'), 'show' => $me->can('reports.view')],
            ],
        ],
        [
            'key' => 'manage', 'title' => 'Boshqaruv', 'icon' => 'shield',
            'items' => [
                $me->can('staff.view')
                    ? ['label' => 'Hodimlar', 'href' => route('staff.index'), 'icon' => 'shield', 'active' => request()->routeIs('staff.index', 'staff.create', 'staff.edit', 'staff.permissions.*', 'payroll.*'), 'show' => true]
                    : ['label' => "O'qituvchilar", 'href' => route('staff.index', ['role' => 'teacher']), 'icon' => 'shield', 'active' => request()->routeIs('staff.index', 'staff.create', 'staff.edit', 'staff.permissions.*', 'payroll.*'), 'show' => $me->can('teachers.view')],
                ['label' => 'Sozlamalar', 'href' => route('settings.index'), 'icon' => 'cog', 'active' => request()->is('settings', 'settings/*'), 'show' => $me->canany(['settings.branch', 'courses.view', 'courses.manage'])],
            ],
        ],
        [
            'key' => 'system', 'title' => 'Tizim', 'icon' => 'cog',
            'items' => [
                ['label' => 'Filiallar', 'href' => route('branches.index'), 'icon' => 'building', 'active' => request()->routeIs('branches.*'), 'show' => $me->can('branches.manage')],
                ['label' => 'Direktorlar', 'href' => route('directors.index'), 'icon' => 'shield', 'active' => request()->routeIs('directors.*'), 'show' => $me->isSuperAdmin()],
                ['label' => 'Brend', 'href' => route('brand.edit'), 'icon' => 'sparkles', 'active' => request()->routeIs('brand.*'), 'show' => $me->isSuperAdmin()],
                ['label' => 'Telegram bot', 'href' => route('telegram.index'), 'icon' => 'megaphone', 'active' => request()->routeIs('telegram.*'), 'show' => $me->isSuperAdmin()],
                ['label' => "Eski bazani ko'chirish", 'href' => route('legacy.index'), 'icon' => 'file', 'active' => request()->routeIs('legacy.*'), 'show' => $me->isSuperAdmin()],
                ['label' => 'Zaxira nusxalar', 'href' => route('backups.index'), 'icon' => 'file', 'active' => request()->routeIs('backups.*'), 'show' => $me->isSuperAdmin()],
                ['label' => 'Huquqiy hujjatlar', 'href' => route('legal.admin'), 'icon' => 'shield', 'active' => request()->routeIs('legal.admin*', 'legal.journal*'), 'show' => $me->can('legal.manage')],
                ['label' => 'Tizim holati', 'href' => route('system-status.index'), 'icon' => 'cog', 'active' => request()->routeIs('system-status.*'), 'show' => $me->can('system.view')],
                ['label' => 'Bildirishnomalar', 'href' => route('notifications.index'), 'icon' => 'bell', 'active' => request()->routeIs('notifications.*'), 'show' => $me->can('notifications.send')],
                ['label' => 'Harakatlar jurnali', 'href' => route('audit.index'), 'icon' => 'log', 'active' => request()->routeIs('audit.*'), 'show' => $me->can('audit.view')],
            ],
        ],
    ];
?>

<?php if (isset($component)) { $__componentOriginalc295f12dca9d42f28a259237a5724830 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc295f12dca9d42f28a259237a5724830 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-link','data' => ['href' => route('dashboard'),'icon' => 'home','active' => request()->routeIs('dashboard')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('dashboard')),'icon' => 'home','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('dashboard'))]); ?>Bosh sahifa <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $attributes = $__attributesOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__attributesOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $component = $__componentOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__componentOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>

<?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $visible = array_values(array_filter($group['items'], fn ($i) => $i['show']));
        $hasActive = (bool) collect($visible)->contains(fn ($i) => $i['active']);
    ?>
    <?php if($visible !== []): ?>
        <div x-data="navGroup('<?php echo e($group['key']); ?>', <?php echo e($hasActive ? 'true' : 'false'); ?>)" class="pt-2">
            <button type="button" @click="toggle()" :aria-expanded="open.toString()"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-wider text-ink-400 transition hover:bg-ink-100 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-200">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $group['icon'],'class' => 'h-4 w-4 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group['icon']),'class' => 'h-4 w-4 shrink-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                <span class="flex-1 text-left"><?php echo e($group['title']); ?></span>
                <?php if($hasActive): ?><span class="h-1.5 w-1.5 rounded-full bg-brand-500" x-show="!open"></span><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron','class' => 'h-4 w-4 shrink-0 transition-transform',':class' => 'open ? \'rotate-180\' : \'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron','class' => 'h-4 w-4 shrink-0 transition-transform',':class' => 'open ? \'rotate-180\' : \'\'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
            </button>
            <div x-show="open" x-cloak x-transition:enter="transition duration-250 ease-out" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 space-y-1 border-l border-ink-100 pl-2 ml-4 dark:border-ink-800">
                <?php $__currentLoopData = $visible; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginalc295f12dca9d42f28a259237a5724830 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc295f12dca9d42f28a259237a5724830 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-link','data' => ['href' => $item['href'],'icon' => $item['icon'],'active' => $item['active'],'target' => ! empty($item['blank']) ? '_blank' : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['href']),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['icon']),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['active']),'target' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! empty($item['blank']) ? '_blank' : null)]); ?><?php echo e($item['label']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $attributes = $__attributesOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__attributesOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $component = $__componentOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__componentOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/partials/sidebar-nav.blade.php ENDPATH**/ ?>