<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['count' => 0]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['count' => 0]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $urls = [
        'feed' => route('notes.feed'),
        'close' => route('notes.close', '__ID__'),
        'reopen' => route('notes.reopen', '__ID__'),
    ];
?>
<div x-data="notesBell(<?php echo \Illuminate\Support\Js::from((int) $count)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($urls)->toHtml() ?>)" @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
    <button type="button" @click="toggle()" class="btn-ghost relative p-2" aria-label="Eslatmalar" title="Eslatmalar">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'bell']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bell']); ?>
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
        <span x-show="count > 0" x-cloak x-text="count > 99 ? '99+' : count"
              class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-bold leading-none text-white"></span>
    </button>

    <div x-show="open" x-cloak x-transition.opacity
         class="absolute right-0 z-40 mt-2 w-[24rem] max-w-[92vw] overflow-hidden rounded-2xl border border-ink-200 bg-white shadow-pop dark:border-ink-700 dark:bg-ink-900">
        <div class="flex items-center justify-between border-b border-ink-100 px-4 py-3 dark:border-ink-800">
            <div class="text-sm font-semibold text-ink-900 dark:text-white">Eslatmalar</div>
            <div class="flex gap-1 rounded-lg bg-ink-100 p-0.5 text-xs font-medium dark:bg-ink-800">
                <button type="button" @click="setTab('active')" :class="tab === 'active' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300'" class="rounded-md px-3 py-1">Faol</button>
                <button type="button" @click="setTab('closed')" :class="tab === 'closed' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900' : 'text-ink-600 dark:text-ink-300'" class="rounded-md px-3 py-1">Yopilgan</button>
            </div>
        </div>

        <div class="max-h-[26rem] overflow-y-auto">
            <div x-show="loading && items.length === 0" class="px-4 py-8 text-center text-sm text-ink-500">Yuklanmoqda...</div>
            <div x-show="error" class="px-4 py-3 text-center text-xs text-brand-600">Ma'lumotni yangilab bo'lmadi, qayta urinilmoqda.</div>
            <div x-show="!loading && items.length === 0" class="px-4 py-8 text-center text-sm text-ink-500"
                 x-text="tab === 'active' ? 'Faol eslatma yo\'q.' : 'Yopilgan eslatma yo\'q.'"></div>

            <template x-for="item in items" :key="item.id">
                <div class="border-b border-ink-100 px-4 py-3 last:border-0 dark:border-ink-800">
                    <div class="flex items-start justify-between gap-3">
                        <a :href="item.student_url" class="text-sm font-semibold text-ink-900 hover:text-brand-600 dark:text-white" x-text="item.student"></a>
                        <button type="button" @click="act(item)" class="shrink-0 text-xs font-medium text-ink-500 hover:text-brand-600"
                                x-text="tab === 'active' ? 'Faolsizlantirish' : 'Qayta faollashtirish'"></button>
                    </div>
                    <div class="mt-1 whitespace-pre-wrap break-words text-sm text-ink-600 dark:text-ink-300" x-text="item.body"></div>
                    <div class="mt-1 text-xs text-ink-400">
                        <span x-text="item.created_at"></span> · <span x-text="item.author || '—'"></span>
                        <template x-if="showBranch && item.branch"><span> · <span x-text="item.branch"></span></span></template>
                        <template x-if="item.closed_at"><span> · yopilgan <span x-text="item.closed_at"></span><template x-if="item.closed_by"><span>, <span x-text="item.closed_by"></span></span></template></span></template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/components/notes-bell.blade.php ENDPATH**/ ?>