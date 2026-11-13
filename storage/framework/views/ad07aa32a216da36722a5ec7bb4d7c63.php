<?php $__env->startSection('title', 'Test'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="quiz(<?php echo \Illuminate\Support\Js::from(['id' => $attempt->id, 'questions' => $attempt->questions])->toHtml() ?>)" x-cloak
     @keydown.window="onKey($event)">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => ($course?->name ?? 'Kurs').' — test','subtitle' => 'Har bir savolga bitta javob tanlang. Javoblaringiz avtomatik saqlanib boradi.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($course?->name ?? 'Kurs').' — test'),'subtitle' => 'Har bir savolga bitta javob tanlang. Javoblaringiz avtomatik saqlanib boradi.']); ?>
         <?php $__env->slot('actions', null, []); ?> <a href="<?php echo e($course ? route('student.courses.show', $course) : route('student.courses')); ?>#test" class="btn-secondary">Chiqish</a> <?php $__env->endSlot(); ?>
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

    <form x-ref="form" method="POST" action="<?php echo e(route('student.tests.submit', $attempt->id)); ?>" class="grid gap-6 lg:grid-cols-3">
        <?php echo csrf_field(); ?>
        <template x-for="(v, qid) in answers" :key="qid">
            <input type="hidden" :name="'answers[' + qid + ']'" :value="v">
        </template>

        
        <div class="space-y-4 lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="h-1.5 bg-ink-100 dark:bg-ink-800"><div class="h-full bg-brand-600 transition-all" :style="'width:' + ((i + 1) * 100 / questions.length) + '%'"></div></div>
                <div class="card-body space-y-5">
                    <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wide text-ink-400">
                        <span>Savol <span x-text="i + 1"></span> / <span x-text="questions.length"></span></span>
                        <span x-text="answeredCount + ' ta javob berildi'"></span>
                    </div>
                    <h2 class="text-lg font-semibold leading-snug text-ink-900 dark:text-white" x-text="q.question"></h2>

                    <div class="space-y-2.5">
                        <template x-for="(opt, oi) in q.options" :key="oi">
                            <button type="button" @click="select(oi)"
                                    class="flex w-full items-center gap-3 rounded-xl border px-4 py-3 text-left text-sm transition"
                                    :class="answers[q.id] === oi ? 'border-brand-600 bg-brand-50 text-brand-900 dark:bg-brand-900/20 dark:text-white' : 'border-ink-200 hover:bg-ink-50 dark:border-ink-700 dark:hover:bg-ink-800'">
                                <span class="flex h-7 w-7 flex-none items-center justify-center rounded-full text-xs font-bold"
                                      :class="answers[q.id] === oi ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300'" x-text="letters[oi]"></span>
                                <span class="flex-1" x-text="opt"></span>
                            </button>
                        </template>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <button type="button" class="btn-secondary" @click="go(i - 1)" :disabled="i === 0">← Oldingi</button>
                        <button type="button" class="btn-primary" x-show="i < questions.length - 1" @click="go(i + 1)">Keyingi →</button>
                        <button type="button" class="btn-primary" x-show="i === questions.length - 1" @click="confirming = true">Yakunlash</button>
                    </div>
                </div>
            </div>
            <p class="hidden text-xs text-ink-400 sm:block">Maslahat: javobni 1–4 raqamlari bilan, savollar orasida ← → tugmalari bilan almashtirish mumkin.</p>
        </div>

        
        <div class="card card-body h-fit space-y-4 lg:sticky lg:top-24">
            <div class="text-sm font-semibold text-ink-900 dark:text-white">Savollar</div>
            <div class="grid grid-cols-5 gap-2">
                <template x-for="(item, n) in questions" :key="item.id">
                    <button type="button" @click="go(n)" class="h-10 rounded-lg text-sm font-semibold transition"
                            :class="n === i ? 'bg-brand-600 text-white ring-2 ring-brand-300' : (answers[item.id] !== undefined ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300')" x-text="n + 1"></button>
                </template>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-ink-500">
                <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded bg-emerald-200"></span> javob berilgan</span>
                <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded bg-ink-200"></span> javobsiz</span>
            </div>
            <button type="button" class="btn-primary w-full" @click="confirming = true">Testni topshirish</button>
        </div>

        
        <div x-show="confirming" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="confirming = false" @keydown.escape.window="confirming = false">
            <div class="card card-body w-full max-w-sm space-y-4">
                <h3 class="text-base font-semibold text-ink-900 dark:text-white">Testni topshirasizmi?</h3>
                <p class="text-sm text-ink-500">
                    <template x-if="unanswered > 0"><span><b class="text-brand-600" x-text="unanswered"></b> ta savolga javob berilmagan. </span></template>
                    Topshirgandan keyin javoblarni o'zgartirib bo'lmaydi.
                </p>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-secondary" @click="confirming = false">Davom etish</button>
                    <button type="button" class="btn-primary" @click="submit()">Topshirish</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('quiz', (cfg) => ({
        questions: cfg.questions, i: 0, answers: {}, confirming: false, letters: ['A', 'B', 'C', 'D'],
        key: 'edunova.test.' + cfg.id,

        init() {
            try {
                const s = JSON.parse(localStorage.getItem(this.key) || 'null');
                if (s && s.answers) { this.answers = s.answers; this.i = Math.min(s.i || 0, this.questions.length - 1); }
            } catch (e) {}
        },

        get q() { return this.questions[this.i]; },
        get answeredCount() { return this.questions.filter(x => this.answers[x.id] !== undefined).length; },
        get unanswered() { return this.questions.length - this.answeredCount; },

        persist() { try { localStorage.setItem(this.key, JSON.stringify({ answers: this.answers, i: this.i })); } catch (e) {} },
        select(oi) { this.answers = { ...this.answers, [this.q.id]: oi }; this.persist(); },
        go(n) { if (n >= 0 && n < this.questions.length) { this.i = n; this.persist(); window.scrollTo({ top: 0, behavior: 'smooth' }); } },
        submit() { try { localStorage.removeItem(this.key); } catch (e) {} this.$refs.form.submit(); },

        onKey(e) {
            if (this.confirming || e.target.closest('input,textarea,select')) return;
            if (['1', '2', '3', '4'].includes(e.key)) { const oi = Number(e.key) - 1; if (oi < this.q.options.length) this.select(oi); }
            else if (e.key === 'ArrowRight') this.go(this.i + 1);
            else if (e.key === 'ArrowLeft') this.go(this.i - 1);
        },
    }));
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/test.blade.php ENDPATH**/ ?>