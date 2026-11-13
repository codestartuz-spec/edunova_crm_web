<?php $__env->startSection('title', 'Sertifikat berish'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $isGroup = $mode === 'group';
        $blocked = collect($checks)->contains('level', 'block');
        $dupBlock = ! $isGroup && $blocked;
        $state = [
            'f' => [
                'student_name' => old('student_name', $init['student_name']),
                'course_name' => old('course_name', $init['course_name']),
                'level_name' => old('level_name', $init['level_name']),
                'title' => old('title', $d['title']),
                'body' => old('body', $d['body']),
                'signer_name' => old('signer_name', $d['signer_name']),
                'signer_title' => old('signer_title', $d['signer_title']),
                'issued_on' => old('issued_on', $d['issued_on']),
            ],
            'force' => (bool) old('force'),
            'picked' => $isGroup ? $members->reject(fn ($m) => $m['has'])->pluck('id')->all() : [],
            'accent' => $accent, 'branch' => $branchName, 'logo' => $d['logo'], 'signature' => $d['signature'],
        ];
    ?>

    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Sertifikat berish','subtitle' => $isGroup ? 'Guruh: '.($init['level_name']) : $student->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Sertifikat berish','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGroup ? 'Guruh: '.($init['level_name']) : $student->name)]); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e($isGroup ? route('groups.show', $init['group_id']) : route('students.show', $student->id)); ?>" class="btn-secondary btn-sm">← Orqaga</a>
         <?php $__env->endSlot(); ?>
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

    <form method="POST" action="<?php echo e($action); ?>" x-data="certEditor(<?php echo \Illuminate\Support\Js::from($state)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($dupBlock)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($isGroup && $blocked)->toHtml() ?>)" class="grid gap-6 xl:grid-cols-[minmax(0,420px)_minmax(0,1fr)]">
        <?php echo csrf_field(); ?>
        <?php if(! $isGroup): ?><input type="hidden" name="group_id" value="<?php echo e($init['group_id']); ?>"><?php endif; ?>

        
        <div class="card card-body space-y-4">
            <h2 class="text-base font-semibold text-ink-900 dark:text-white">Ma'lumotlar (o'zgartirsangiz, ko'rinish darhol yangilanadi)</h2>

            <?php if(! $isGroup): ?>
                <div>
                    <label class="label">Guruh</label>
                    <select class="input" onchange="location.href='<?php echo e(route('certificates.create-student', $student->id)); ?>?group_id='+this.value">
                        <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><option value="<?php echo e($g->id); ?>" <?php if($g->id === $init['group_id']): echo 'selected'; endif; ?>><?php echo e($g->name); ?> — <?php echo e($g->course?->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><option>Guruh yo'q</option><?php endif; ?>
                    </select>
                    <p class="mt-1 text-xs text-ink-500">Guruh almashtirilsa, kiritilgan o'zgarishlar yo'qoladi.</p>
                </div>
                <div><label class="label">O'quvchi F.I.O</label><input name="student_name" x-model="f.student_name" class="input" maxlength="120"></div>
            <?php endif; ?>
            <div class="grid gap-3 sm:grid-cols-2">
                <div><label class="label">Kurs nomi</label><input name="course_name" x-model="f.course_name" class="input" maxlength="120"></div>
                <div><label class="label">Guruh / daraja</label><input name="level_name" x-model="f.level_name" class="input" maxlength="120"></div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div><label class="label">Sarlavha</label><input name="title" x-model="f.title" class="input" maxlength="60" required></div>
                <div><label class="label">Berilgan sana</label><input type="date" name="issued_on" x-model="f.issued_on" max="<?php echo e(today()->toDateString()); ?>" class="input" required></div>
            </div>
            <div><label class="label">Matn</label><textarea name="body" x-model="f.body" rows="3" maxlength="300" class="input" required></textarea></div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div><label class="label">Imzolovchi F.I.O</label><input name="signer_name" x-model="f.signer_name" class="input" maxlength="80"></div>
                <div><label class="label">Lavozimi</label><input name="signer_title" x-model="f.signer_title" class="input" maxlength="60"></div>
            </div>
            <?php $__currentLoopData = ['title', 'body', 'issued_on', 'student_ids', 'group_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php $__errorArgs = [$k];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($isGroup): ?>
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label class="label !mb-0">O'quvchilar (<span x-text="picked.length"></span>)</label>
                        <button type="button" class="text-xs font-semibold text-brand-600" @click="picked = picked.length === all.length ? [] : all.slice()">Hammasini belgilash / olib tashlash</button>
                    </div>
                    <div class="max-h-64 space-y-1 overflow-y-auto rounded-xl border border-ink-200 p-2 dark:border-ink-700">
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-ink-50 dark:hover:bg-ink-800">
                                <input type="checkbox" name="student_ids[]" value="<?php echo e($m['id']); ?>" x-model.number="picked" class="checkbox" @click="preview = <?php echo e($m['id']); ?>; f.student_name = <?php echo \Illuminate\Support\Js::from($m['name'])->toHtml() ?>" >
                                <span class="font-medium"><?php echo e($m['name']); ?></span>
                                <?php if($m['has']): ?><span class="badge-gray ml-auto">Sertifikat bor</span><?php elseif($m['debt']): ?><span class="badge-amber ml-auto">Qarz</span><?php endif; ?>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="mt-1 text-xs text-ink-500">Ko'rinishda ismni ko'rish uchun o'quvchiga bosing. Har bir sertifikatga o'z ismi yoziladi.</p>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="space-y-4">
            <div class="card card-body">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-ink-900 dark:text-white">Jonli ko'rinish</h2>
                    <span class="text-xs text-ink-400">PDF shunday chiqadi (QR kod berilgach paydo bo'ladi)</span>
                </div>
                <div x-ref="wrap" class="relative w-full overflow-hidden rounded-lg border border-ink-200 bg-white dark:border-ink-700" :style="`height:${595 * scale}px`">
                    <div :style="`width:842px;height:595px;transform:scale(${scale});transform-origin:0 0;position:absolute;left:0;top:0;font-family:'DejaVu Sans',Verdana,sans-serif;color:#1f2937`">
                        <div :style="`position:absolute;left:16px;top:16px;width:810px;height:8px;background:${accent}`"></div>
                        <div :style="`position:absolute;left:16px;top:16px;width:804px;height:557px;border:3px solid ${accent};box-sizing:content-box`"></div>
                        <div style="position:absolute;left:25px;top:25px;width:790px;height:541px;border:.75px solid #b8923a;box-sizing:content-box"></div>
                        <template x-for="c in ['top:34px;left:34px;border-top:3px solid #b8923a;border-left:3px solid #b8923a', 'top:34px;right:34px;border-top:3px solid #b8923a;border-right:3px solid #b8923a', 'bottom:34px;left:34px;border-bottom:3px solid #b8923a;border-left:3px solid #b8923a', 'bottom:34px;right:34px;border-bottom:3px solid #b8923a;border-right:3px solid #b8923a']"><div :style="`position:absolute;width:34px;height:34px;${c}`"></div></template>

                        <div style="position:absolute;left:60px;right:60px;top:42px;text-align:center">
                            <img x-show="logo" :src="logo" style="height:46px" alt="">
                            <div style="font-size:9px;letter-spacing:3px;color:#6b7280;margin-top:4px" x-text="branch.toUpperCase()"></div>
                        </div>
                        <div style="position:absolute;left:60px;right:60px;top:112px;text-align:center">
                            <div :style="`font-size:38px;letter-spacing:12px;font-weight:bold;color:${accent}`" x-text="f.title"></div>
                            <div style="font-size:9px;letter-spacing:4px;color:#b8923a;margin-top:2px">MUVAFFAQIYAT SERTIFIKATI</div>
                            <div style="width:120px;border-top:1.5px solid #b8923a;margin:10px auto 0"></div>
                        </div>
                        <div style="position:absolute;left:60px;right:60px;top:186px;text-align:center">
                            <div style="font-size:9px;letter-spacing:3px;color:#6b7280">SERTIFIKAT EGASI</div>
                            <div style="font-size:32px;font-weight:bold;margin:8px 50px 0;padding-bottom:8px;border-bottom:.75px solid #d1d5db" x-text="f.student_name || '—'"></div>
                            <div x-show="f.course_name" :style="`font-size:17px;font-weight:bold;color:${accent};margin-top:14px`" x-text="f.course_name + (f.level_name ? ' · ' + f.level_name : '')"></div>
                            <div style="font-size:11px;color:#4b5563;line-height:1.5;margin:8px 90px 0" x-text="f.body.charAt(0).toUpperCase() + f.body.slice(1)"></div>
                        </div>
                        <div style="position:absolute;left:62px;right:62px;top:402px;height:128px;display:flex;align-items:flex-end">
                            <div style="width:34%;text-align:center">
                                <img x-show="signature" :src="signature" style="height:42px" alt="">
                                <div style="border-top:.75px solid #9ca3af;padding-top:4px;width:180px;margin:0 auto;font-size:10px"><b x-text="f.signer_name || ' '"></b><br><span style="font-size:8px;color:#6b7280" x-text="f.signer_title"></span></div>
                            </div>
                            <div style="width:32%;text-align:center">
                                <div style="font-size:8px;letter-spacing:2px;color:#6b7280">BERILGAN SANA</div>
                                <div style="font-size:12px;font-weight:bold;margin-bottom:8px" x-text="fmt(f.issued_on)"></div>
                                <div style="font-size:8px;letter-spacing:2px;color:#6b7280">SERTIFIKAT RAQAMI</div>
                                <div style="font-size:10px;font-weight:bold">EDU-<?php echo e(now()->year); ?>-••••••</div>
                            </div>
                            <div style="width:34%;text-align:center">
                                <div style="width:74px;height:74px;margin:0 auto 4px;background:repeating-conic-gradient(#e5e7eb 0 25%, #fff 0 50%) 0 0/12px 12px"></div>
                                <span style="font-size:8px;color:#6b7280">Sertifikat haqiqiyligini tekshirish<br>uchun QR kodni skanerlang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-body space-y-2">
                <h2 class="text-base font-semibold text-ink-900 dark:text-white">Berishdan oldin tekshiruv</h2>
                <?php $__empty_1 = true; $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-start gap-2 rounded-lg px-3 py-2 text-sm <?php echo e(['block' => 'bg-red-50 text-red-700', 'warn' => 'bg-amber-50 text-amber-800', 'info' => 'bg-sky-50 text-sky-800'][$c['level']]); ?>">
                        <span><?php echo e(['block' => '⛔', 'warn' => '⚠️', 'info' => 'ℹ️'][$c['level']]); ?></span><span><?php echo e($c['text']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700">✅ Muammo topilmadi.</div>
                <?php endif; ?>

                <?php if($dupBlock): ?>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="force" value="1" x-model="force" class="checkbox"> Baribir yangi sertifikat berish (eskisi ham amalda qoladi)</label>
                <?php endif; ?>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button class="btn-primary" :disabled="!canIssue" :class="!canIssue && 'opacity-50 cursor-not-allowed'">✔ Tasdiqlash va berish</button>
                    <a href="<?php echo e($isGroup ? route('groups.show', $init['group_id']) : route('students.show', $student->id)); ?>" class="btn-ghost">Bekor qilish</a>
                    <?php if($isGroup): ?><span class="text-sm text-ink-500"><b x-text="picked.length"></b> ta sertifikat beriladi</span><?php endif; ?>
                </div>
            </div>
        </div>
    </form>

    <script>
        function certEditor(s, dupBlock, hardBlock) {
            return {
                ...s, dupBlock, hardBlock, scale: 1, all: <?php echo json_encode($members->pluck('id')->all()); ?>, preview: null,
                init() {
                    const fit = () => { this.scale = Math.min(1.2, this.$refs.wrap.clientWidth / 842); };
                    fit(); new ResizeObserver(fit).observe(this.$refs.wrap);
                },
                fmt(v) { if (!v) return '—'; const p = v.split('-'); return p.length === 3 ? `${p[2]}.${p[1]}.${p[0]}` : v; },
                get canIssue() { if (this.hardBlock) return false; if (this.dupBlock && !this.force) return false; if (this.all.length && !this.picked.length) return false; return true; },
            };
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/certificates/create.blade.php ENDPATH**/ ?>