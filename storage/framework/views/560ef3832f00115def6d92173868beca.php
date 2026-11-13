<?php $__env->startSection('title', "Ko'chirish jarayoni"); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-3xl space-y-5" x-data="legacyProgress('<?php echo e(route('legacy.status', $token)); ?>')" x-init="start()">
    <div>
        <h1 class="text-xl font-semibold">Eski bazani ko'chirish</h1>
        <p class="text-sm text-gray-500 mt-1">Sahifani yopmang — jarayon fonda ishlaydi, holat har soniyada yangilanadi.</p>
    </div>

    <div class="rounded-xl border bg-white p-5 space-y-3">
        <div class="flex items-center justify-between text-sm">
            <span class="font-medium" x-text="d.label || 'Navbatda…'"></span>
            <span class="text-gray-500"><span x-text="Math.round(d.percent || 0)"></span>% · <span x-text="fmt(d.elapsed || 0)"></span></span>
        </div>
        <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full transition-all duration-500" :class="d.status === 'failed' ? 'bg-red-500' : (d.status === 'done' ? 'bg-green-500' : 'bg-indigo-600')" :style="`width:${d.percent || 0}%`"></div>
        </div>
        <div class="max-h-64 overflow-y-auto rounded-lg bg-gray-50 border p-3 text-xs font-mono space-y-0.5" x-ref="log">
            <template x-for="(l, i) in (d.log || [])" :key="i"><div><span class="text-gray-400" x-text="l.t"></span> <span x-text="l.text"></span></div></template>
            <div x-show="!(d.log || []).length" class="text-gray-400">Jarayon boshlanishi kutilmoqda…</div>
        </div>
    </div>

    <div x-show="d.status === 'failed'" x-cloak class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm space-y-2">
        <div x-text="d.error"></div>
        <a href="<?php echo e(route('legacy.index')); ?>" class="inline-block text-indigo-600">← Boshiga qaytish</a>
    </div>

    <div x-show="d.status === 'done'" x-cloak class="rounded-xl border border-green-200 bg-white p-5 space-y-3">
        <div class="font-semibold text-green-700">✔ Ko'chirish muvaffaqiyatli yakunlandi</div>
        <ul class="text-sm space-y-1">
            <template x-for="c in ((d.report || {}).checks || [])" :key="c.label">
                <li :class="c.ok ? 'text-green-700' : 'text-amber-700'"><span x-text="c.ok ? '✔' : '!'"></span> <span x-text="c.label"></span>: <span x-text="c.detail"></span></li>
            </template>
        </ul>
        <p class="text-sm text-gray-600" x-show="((d.report || {}).directors || []).length">Direktorga aylantirilgan sAdminlar (ruxsatlarsiz): <span x-text="(((d.report || {}).directors) || []).map(x => x.name).join(', ')"></span></p>
        <a href="<?php echo e(route('legacy.index')); ?>" class="inline-block px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">Tayyor</a>
    </div>
</div>
<script>
function legacyProgress(url) {
    return {
        d: {}, timer: null, fails: 0,
        fmt(s) { return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0'); },
        start() { this.tick(); this.timer = setInterval(() => this.tick(), 1000); },
        async tick() {
            try {
                const r = await fetch(url, {headers: {'Accept': 'application/json'}, cache: 'no-store'});
                if (!r.ok) throw new Error(r.status);
                this.d = await r.json(); this.fails = 0;
                this.$nextTick(() => { const el = this.$refs.log; if (el) el.scrollTop = el.scrollHeight; });
                if (['done', 'failed'].includes(this.d.status)) clearInterval(this.timer);
            } catch (e) {
                if (++this.fails > 30) { clearInterval(this.timer); this.d = {status: 'failed', error: "Serverdan javob kelmayapti. Sahifani yangilang."}; }
            }
        },
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/legacy/progress.blade.php ENDPATH**/ ?>