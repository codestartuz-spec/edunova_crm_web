{{--
    Yagona tasdiqlash oynasi (v24 dizayn).
    <form data-confirm="Matn" data-confirm-title="Sarlavha" data-confirm-ok="Tugma matni" data-confirm-tone="danger|info|success">
    Telefonda pastdan chiqadigan oyna (bottom sheet), kompyuterda markazda. Rang ohangi (tone) ga qarab o'zgaradi:
    danger — qizil (o'chirish va xavfli amallar), info — brend rangi (masalan, chiqish), success — yashil.
--}}
<div id="confirm-modal" class="confirm-overlay fixed inset-0 z-[100] hidden items-end justify-center p-0 sm:items-center sm:p-4" role="alertdialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-text">
    <div class="confirm-backdrop absolute inset-0 bg-ink-950/60 backdrop-blur-md" data-confirm-dismiss></div>
    <div id="confirm-panel" tabindex="-1" class="confirm-panel relative w-full max-w-[26rem] overflow-hidden rounded-t-[1.75rem] bg-white shadow-pop ring-1 ring-ink-900/5 focus:outline-none dark:bg-ink-900 dark:ring-white/10 sm:rounded-[1.75rem]">
        {{-- Ohangga mos yuqori chiziq va yumshoq yorug'lik --}}
        <div id="confirm-strip" class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-500 to-accent-400"></div>
        <div id="confirm-glow" class="pointer-events-none absolute -top-16 left-1/2 h-40 w-72 -translate-x-1/2 rounded-full bg-brand-400/20 blur-3xl"></div>

        <div class="relative px-6 pb-6 pt-7 text-center sm:px-8 sm:pb-7 sm:pt-9">
            <div class="mx-auto mb-5 h-1 w-10 rounded-full bg-ink-200 dark:bg-ink-700 sm:hidden"></div>

            <div class="confirm-halo mx-auto mb-5 flex h-[4.5rem] w-[4.5rem] items-center justify-center rounded-full" id="confirm-halo">
                <span id="confirm-icon" class="relative flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600 shadow-inner ring-1 ring-inset ring-brand-100 dark:bg-brand-950/60 dark:text-brand-300 dark:ring-brand-500/20">
                    <span data-icon="danger"><x-icon name="alert" class="h-7 w-7" /></span>
                    <span data-icon="info" class="hidden"><x-icon name="logout" class="h-7 w-7" /></span>
                    <span data-icon="success" class="hidden"><x-icon name="check" class="h-7 w-7" /></span>
                </span>
            </div>

            <h3 id="confirm-title" class="text-xl font-extrabold leading-snug tracking-tight text-ink-900 dark:text-white">Tasdiqlaysizmi?</h3>
            <p id="confirm-text" class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-ink-500 dark:text-ink-400"></p>

            <div class="mt-7 grid grid-cols-2 gap-3">
                <button type="button" id="confirm-cancel" class="btn-secondary confirm-btn justify-center py-3">Bekor qilish</button>
                <button type="button" id="confirm-ok" class="btn-primary confirm-btn justify-center py-3">Ha</button>
            </div>
            <p class="mt-4 hidden text-[11px] text-ink-400 sm:block"><kbd class="rounded border border-ink-200 bg-ink-50 px-1.5 py-0.5 font-sans text-[10px] font-semibold text-ink-500 dark:border-ink-700 dark:bg-ink-800 dark:text-ink-300">Esc</kbd> — bekor qilish</p>
        </div>
    </div>
</div>
