<?php $__env->startSection('title', $course->name); ?>

<?php $__env->startSection('content'); ?>
<div x-data="coursePlayer(<?php echo \Illuminate\Support\Js::from(['course' => $course->id, 'videos' => $videos, 'audios' => $audios])->toHtml() ?>)" x-cloak>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $course->name,'subtitle' => 'Darslarni shu sahifaning o\'zida ko\'ring, tinglang va testni yeching']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->name),'subtitle' => 'Darslarni shu sahifaning o\'zida ko\'ring, tinglang va testni yeching']); ?>
         <?php $__env->slot('actions', null, []); ?> <a href="<?php echo e(route('student.courses')); ?>" class="btn-secondary">Orqaga</a> <?php $__env->endSlot(); ?>
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

    
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="card card-body py-3"><div class="text-xs text-ink-500">Video darslar</div><div class="mt-1 text-xl font-bold text-ink-900 dark:text-white"><span x-text="doneCount('video')"></span><span class="text-sm font-medium text-ink-400"> / <?php echo e($videos->count()); ?></span></div></div>
        <div class="card card-body py-3"><div class="text-xs text-ink-500">Audiolar</div><div class="mt-1 text-xl font-bold text-ink-900 dark:text-white"><span x-text="doneCount('audio')"></span><span class="text-sm font-medium text-ink-400"> / <?php echo e($audios->count()); ?></span></div></div>
        <div class="card card-body py-3"><div class="text-xs text-ink-500">Test savollari</div><div class="mt-1 text-xl font-bold text-ink-900 dark:text-white"><?php echo e($questionCount); ?></div></div>
        <div class="card card-body py-3"><div class="text-xs text-ink-500">Eng yaxshi natija</div><div class="mt-1 text-xl font-bold <?php echo e($best !== null && $best >= 60 ? 'text-emerald-600' : 'text-ink-900 dark:text-white'); ?>"><?php echo e($best !== null ? $best.'%' : '—'); ?></div></div>
    </div>

    
    <div class="flex gap-1 overflow-x-auto rounded-xl bg-ink-100 p-1 dark:bg-ink-800">
        <?php $__currentLoopData = ['video' => 'Videolar ('.$videos->count().')', 'audio' => 'Audiolar ('.$audios->count().')', 'test' => 'Test']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" @click="tab = '<?php echo e($k); ?>'" :class="tab === '<?php echo e($k); ?>' ? 'bg-white text-brand-700 shadow-sm dark:bg-ink-900 dark:text-brand-300' : 'text-ink-600 dark:text-ink-300'" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition"><?php echo e($label); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div x-show="tab === 'video'" class="mt-5">
        <?php if($videos->isEmpty()): ?>
            <div class="card card-body text-center text-sm text-ink-500">Bu kurs uchun videolar hali qo'shilmagan.</div>
        <?php else: ?>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="relative aspect-video w-full bg-black">
                        <template x-if="tab === 'video' && video">
                            <div class="absolute inset-0">
                                <template x-if="video.kind === 'iframe'">
                                    <iframe :src="video.src" :title="video.title" class="h-full w-full" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                            allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
                                </template>
                                <template x-if="video.kind === 'video'">
                                    <video :src="video.src" class="h-full w-full" controls controlsList="nodownload" playsinline preload="metadata" @ended="finish('video')"></video>
                                </template>
                                <template x-if="video.kind === 'link' || video.kind === 'none' || video.kind === 'audio'">
                                    <div class="flex h-full flex-col items-center justify-center gap-3 p-6 text-center text-white">
                                        <p class="text-sm text-white/80">Bu video sahifa ichida ijro etilmaydi. O'qituvchingizga xabar bering.</p>
                                        <a :href="video.url" target="_blank" rel="noopener noreferrer" class="btn-secondary btn-sm">Havolani alohida ochish</a>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="card-body space-y-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <div class="text-xs font-medium uppercase tracking-wide text-ink-400" x-text="'Dars ' + video.number"></div>
                                <h2 class="text-lg font-semibold text-ink-900 dark:text-white" x-text="video.title"></h2>
                            </div>
                            <span class="badge-gray" x-text="video.label"></span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" class="btn-secondary btn-sm" @click="step('video', -1)" :disabled="vi === 0">← Oldingi</button>
                            <button type="button" class="btn-secondary btn-sm" @click="step('video', 1)" :disabled="vi >= videos.length - 1">Keyingi →</button>
                            <button type="button" class="btn-ghost btn-sm ml-auto" @click="toggleDone('video', video.id)">
                                <span x-show="!isDone('video', video.id)">Ko'rildi deb belgilash</span>
                                <span x-show="isDone('video', video.id)" class="text-emerald-600">✓ Ko'rilgan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card h-fit overflow-hidden">
                <div class="border-b border-ink-100 px-5 py-4 dark:border-ink-800">
                    <div class="flex items-center justify-between text-sm font-semibold text-ink-900 dark:text-white">
                        <span>Darslar</span><span class="text-xs font-medium text-ink-500"><span x-text="doneCount('video')"></span> / <?php echo e($videos->count()); ?> ko'rilgan</span>
                    </div>
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800"><div class="h-full rounded-full bg-brand-600 transition-all" :style="'width:' + percent('video') + '%'"></div></div>
                </div>
                <ul class="max-h-[30rem] divide-y divide-ink-100 overflow-y-auto dark:divide-ink-800">
                    <template x-for="(v, i) in videos" :key="v.id">
                        <li>
                            <button type="button" @click="pick('video', i)" class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm transition hover:bg-ink-50 dark:hover:bg-ink-800/60" :class="vi === i ? 'bg-brand-50 dark:bg-brand-900/20' : ''">
                                <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full text-xs font-bold"
                                      :class="isDone('video', v.id) ? 'bg-emerald-100 text-emerald-700' : (vi === i ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300')">
                                    <span x-show="!isDone('video', v.id)" x-text="v.number"></span><span x-show="isDone('video', v.id)">✓</span>
                                </span>
                                <span class="min-w-0 flex-1 truncate" :class="vi === i ? 'font-semibold text-brand-700 dark:text-brand-300' : 'text-ink-800 dark:text-ink-200'" x-text="v.title"></span>
                                <svg x-show="vi === i" class="h-4 w-4 flex-none text-brand-600" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div x-show="tab === 'audio'" class="mt-5">
        <?php if($audios->isEmpty()): ?>
            <div class="card card-body text-center text-sm text-ink-500">Bu kurs uchun audiolar hali qo'shilmagan.</div>
        <?php else: ?>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card card-body space-y-4 lg:col-span-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 flex-none items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-900/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 14v-2a9 9 0 0 1 18 0v2"/><path d="M21 15a2 2 0 0 1-2 2h-1v-5h1a2 2 0 0 1 2 2zM3 15a2 2 0 0 0 2 2h1v-5H5a2 2 0 0 0-2 2z"/></svg>
                        </span>
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-ink-400" x-text="'Audio ' + audio.number"></div>
                            <h2 class="text-lg font-semibold text-ink-900 dark:text-white" x-text="audio.title"></h2>
                        </div>
                    </div>
                    <span class="badge-gray" x-text="audio.label"></span>
                </div>

                <template x-if="tab === 'audio' && audio">
                    <div>
                        <template x-if="audio.kind === 'audio'">
                            <audio x-ref="audio" :src="audio.src" class="w-full" controls controlsList="nodownload" preload="metadata"
                                   @loadedmetadata="$el.playbackRate = speed" @ended="finish('audio')"></audio>
                        </template>
                        <template x-if="audio.kind === 'iframe'">
                            <iframe :src="audio.src" :title="audio.title" class="h-28 w-full rounded-lg" frameborder="0" allow="autoplay; encrypted-media" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                        </template>
                        <template x-if="audio.kind === 'link' || audio.kind === 'none' || audio.kind === 'video'">
                            <div class="rounded-lg bg-ink-50 p-4 text-sm text-ink-600 dark:bg-ink-800 dark:text-ink-300">
                                Bu audio sahifa ichida ijro etilmaydi. O'qituvchingizga xabar bering.
                                <a :href="audio.url" target="_blank" rel="noopener noreferrer" class="ml-1 font-semibold text-brand-600">Havolani alohida ochish</a>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" class="btn-secondary btn-sm" @click="step('audio', -1)" :disabled="ai === 0">← Oldingi</button>
                    <button type="button" class="btn-secondary btn-sm" @click="step('audio', 1)" :disabled="ai >= audios.length - 1">Keyingi →</button>
                    <div class="ml-auto flex items-center gap-1" x-show="audio.kind === 'audio'">
                        <span class="mr-1 text-xs text-ink-500">Tezlik</span>
                        <template x-for="s in [0.75, 1, 1.25, 1.5]" :key="s">
                            <button type="button" @click="speed = s" class="rounded-md px-2 py-1 text-xs font-semibold" :class="speed === s ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300'" x-text="s + 'x'"></button>
                        </template>
                    </div>
                    <button type="button" class="btn-ghost btn-sm" @click="toggleDone('audio', audio.id)">
                        <span x-show="!isDone('audio', audio.id)">Tinglandi deb belgilash</span>
                        <span x-show="isDone('audio', audio.id)" class="text-emerald-600">✓ Tinglangan</span>
                    </button>
                </div>
            </div>

            <div class="card h-fit overflow-hidden">
                <div class="border-b border-ink-100 px-5 py-4 dark:border-ink-800">
                    <div class="flex items-center justify-between text-sm font-semibold text-ink-900 dark:text-white">
                        <span>Audiolar</span><span class="text-xs font-medium text-ink-500"><span x-text="doneCount('audio')"></span> / <?php echo e($audios->count()); ?> tinglangan</span>
                    </div>
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-800"><div class="h-full rounded-full bg-brand-600 transition-all" :style="'width:' + percent('audio') + '%'"></div></div>
                </div>
                <ul class="max-h-[30rem] divide-y divide-ink-100 overflow-y-auto dark:divide-ink-800">
                    <template x-for="(a, i) in audios" :key="a.id">
                        <li>
                            <button type="button" @click="pick('audio', i)" class="flex w-full items-center gap-3 px-5 py-3 text-left text-sm transition hover:bg-ink-50 dark:hover:bg-ink-800/60" :class="ai === i ? 'bg-brand-50 dark:bg-brand-900/20' : ''">
                                <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full text-xs font-bold"
                                      :class="isDone('audio', a.id) ? 'bg-emerald-100 text-emerald-700' : (ai === i ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-600 dark:bg-ink-800 dark:text-ink-300')">
                                    <span x-show="!isDone('audio', a.id)" x-text="a.number"></span><span x-show="isDone('audio', a.id)">✓</span>
                                </span>
                                <span class="min-w-0 flex-1 truncate" :class="ai === i ? 'font-semibold text-brand-700 dark:text-brand-300' : 'text-ink-800 dark:text-ink-200'" x-text="a.title"></span>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div x-show="tab === 'test'" class="mt-5 grid gap-6 lg:grid-cols-3">
        <div class="card card-body h-fit space-y-4">
            <div>
                <h2 class="text-base font-semibold text-ink-900 dark:text-white">Bilimingizni sinang</h2>
                <p class="mt-1 text-sm text-ink-500"><?php echo e($questionCount); ?> ta savoldan tasodifiy <?php echo e(min($questionCount, \App\Services\TestService::QUESTIONS_PER_TEST)); ?> tasi beriladi. Har bir savolda bitta to'g'ri javob bor.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="rounded-xl bg-ink-50 px-3 py-3 dark:bg-ink-800"><div class="text-xl font-bold text-ink-900 dark:text-white"><?php echo e($attempts); ?></div><div class="text-xs text-ink-500">urinish</div></div>
                <div class="rounded-xl bg-ink-50 px-3 py-3 dark:bg-ink-800"><div class="text-xl font-bold <?php echo e($best !== null && $best >= 60 ? 'text-emerald-600' : 'text-ink-900 dark:text-white'); ?>"><?php echo e($best !== null ? $best.'%' : '—'); ?></div><div class="text-xs text-ink-500">eng yaxshi</div></div>
            </div>
            <?php if($questionCount > 0): ?>
                <form method="POST" action="<?php echo e(route('student.tests.start', $course)); ?>"><?php echo csrf_field(); ?><button class="btn-primary w-full"><?php echo e($attempts ? 'Testni qayta boshlash' : 'Testni boshlash'); ?></button></form>
            <?php else: ?>
                <p class="rounded-lg bg-ink-50 px-3 py-2 text-sm text-ink-500 dark:bg-ink-800">Bu kursda hali test savollari yo'q.</p>
            <?php endif; ?>
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-ink-100 px-5 py-4 text-sm font-semibold text-ink-900 dark:border-ink-800 dark:text-white">Oxirgi urinishlar</div>
            <?php if($results->isEmpty()): ?>
                <p class="px-5 py-8 text-center text-sm text-ink-500">Hali test topshirilmagan.</p>
            <?php else: ?>
                <div class="table-wrap"><table class="table">
                    <thead><tr><th>Sana</th><th>To'g'ri javob</th><th>Natija</th><th></th></tr></thead>
                    <tbody>
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($r->finished_at?->format('d.m.Y H:i')); ?></td>
                            <td><?php echo e($r->correct_count); ?> / <?php echo e($r->total); ?></td>
                            <td><span class="<?php echo e($r->score >= 60 ? 'badge-green' : 'badge-red'); ?>"><?php echo e($r->score); ?>%</span></td>
                            <td class="text-right"><?php if($r->answers !== null): ?><a href="<?php echo e(route('student.tests.result', $r->id)); ?>" class="text-sm font-semibold text-brand-600">Ko'rish</a><?php endif; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('coursePlayer', (cfg) => ({
        tab: 'video', videos: cfg.videos, audios: cfg.audios, vi: 0, ai: 0, speed: 1,
        done: { video: {}, audio: {} }, key: 'edunova.course.' + cfg.course,

        init() {
            try {
                const s = JSON.parse(localStorage.getItem(this.key) || '{}');
                this.done = { video: s.video || {}, audio: s.audio || {} };
            } catch (e) {}

            const h = location.hash.replace('#', '');
            if (['video', 'audio', 'test'].includes(h)) this.tab = h;
            else if (!this.videos.length && this.audios.length) this.tab = 'audio';

            // keyingi o'rganilmagan darsdan boshlaymiz
            const nv = this.videos.findIndex(v => !this.done.video[v.id]);
            this.vi = nv === -1 ? 0 : nv;
            const na = this.audios.findIndex(a => !this.done.audio[a.id]);
            this.ai = na === -1 ? 0 : na;

            this.$watch('tab', t => { try { history.replaceState(null, '', '#' + t); } catch (e) {} });
            this.$watch('speed', s => { if (this.$refs.audio) this.$refs.audio.playbackRate = s; });
        },

        get video() { return this.videos[this.vi] || { number: '', title: '', label: '', kind: 'none', src: '', url: '#' }; },
        get audio() { return this.audios[this.ai] || { number: '', title: '', label: '', kind: 'none', src: '', url: '#' }; },

        save() { try { localStorage.setItem(this.key, JSON.stringify(this.done)); } catch (e) {} },
        isDone(type, id) { return !!this.done[type][id]; },
        toggleDone(type, id) { this.done[type][id] = !this.done[type][id]; this.save(); },
        doneCount(type) { return (type === 'video' ? this.videos : this.audios).filter(x => this.done[type][x.id]).length; },
        percent(type) { const n = (type === 'video' ? this.videos : this.audios).length; return n ? Math.round(this.doneCount(type) * 100 / n) : 0; },

        pick(type, i) { if (type === 'video') this.vi = i; else this.ai = i; },
        step(type, d) {
            const list = type === 'video' ? this.videos : this.audios;
            const cur = type === 'video' ? this.vi : this.ai;
            const next = Math.min(list.length - 1, Math.max(0, cur + d));
            this.pick(type, next);
            return next !== cur;
        },
        // Fayl tugaganda: belgilab, keyingisiga o'tamiz (audio avtomatik davom etadi)
        finish(type) {
            const list = type === 'video' ? this.videos : this.audios;
            const cur = type === 'video' ? this.vi : this.ai;
            this.done[type][list[cur].id] = true;
            this.save();
            if (this.step(type, 1)) {
                this.$nextTick(() => {
                    const el = type === 'audio' ? this.$refs.audio : this.$root.querySelector('video');
                    if (el && el.play) el.play().catch(() => {});
                });
            }
        },
    }));
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/student/course.blade.php ENDPATH**/ ?>