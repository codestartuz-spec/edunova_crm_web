{{--
    v14: yagona tasdiqlash oynasi.
    <form data-confirm="Matn" data-confirm-title="Sarlavha" data-confirm-ok="Tugma matni" data-confirm-tone="danger|info|success">
    Telefonda pastdan chiqadigan oyna (bottom sheet), kompyuterda markazda.
--}}
<div id="confirm-modal" class="confirm-overlay fixed inset-0 z-[100] hidden items-end justify-center p-0 sm:items-center sm:p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-text">
    <div class="confirm-backdrop absolute inset-0 bg-ink-950/55 backdrop-blur-sm" data-confirm-dismiss></div>
    <div class="confirm-panel relative w-full max-w-md rounded-t-3xl bg-white p-6 shadow-2xl ring-1 ring-black/5 dark:bg-ink-900 dark:ring-white/10 sm:rounded-3xl sm:p-7">
        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-ink-200 dark:bg-ink-700 sm:hidden"></div>
        <div class="flex items-start gap-4">
            <span id="confirm-icon" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-300">
                <span data-icon="danger"><x-icon name="alert" class="h-6 w-6" /></span>
                <span data-icon="info" class="hidden"><x-icon name="logout" class="h-6 w-6" /></span>
                <span data-icon="success" class="hidden"><x-icon name="check" class="h-6 w-6" /></span>
            </span>
            <div class="min-w-0 flex-1 pt-0.5">
                <h3 id="confirm-title" class="text-lg font-bold leading-snug text-ink-900 dark:text-white">Tasdiqlaysizmi?</h3>
                <p id="confirm-text" class="mt-1.5 text-sm leading-relaxed text-ink-500 dark:text-ink-400"></p>
            </div>
        </div>
        <div class="mt-7 grid grid-cols-2 gap-3 sm:flex sm:justify-end">
            <button type="button" id="confirm-cancel" class="btn-secondary justify-center sm:min-w-[7.5rem]">Bekor qilish</button>
            <button type="button" id="confirm-ok" class="btn-danger justify-center sm:min-w-[7.5rem]">Ha</button>
        </div>
    </div>
</div>
