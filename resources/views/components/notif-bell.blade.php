{{-- v14: hodimlar bildirishnomalari: qo'ng'iroqcha + toast + brauzer bildirishnomasi + Web Push yoqish --}}
<div x-data="notifBell(@js(['feed' => route('notifications.feed'), 'read' => route('notifications.read', '__ID__'), 'readAll' => route('notifications.read-all'), 'key' => route('push.key'), 'subscribe' => route('push.subscribe'), 'sw' => url('/sw.js')]))"
     @keydown.escape.window="open = false" @click.outside="open = false" class="relative">
    <button type="button" @click="open = !open" class="btn-ghost relative p-2" aria-label="Bildirishnomalar" title="Bildirishnomalar">
        <x-icon name="bell" />
        <span x-show="count > 0" x-cloak x-text="count > 99 ? '99+' : count" class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-emerald-600 px-1 text-[10px] font-bold leading-none text-white"></span>
    </button>

    <div x-show="open" x-cloak x-transition.opacity class="absolute right-0 z-40 mt-2 w-[24rem] max-w-[92vw] overflow-hidden rounded-2xl border border-ink-200 bg-white shadow-pop dark:border-ink-700 dark:bg-ink-900">
        <div class="flex items-center justify-between border-b border-ink-100 px-4 py-3 dark:border-ink-800">
            <div class="text-sm font-semibold text-ink-900 dark:text-white">Bildirishnomalar</div>
            <button type="button" class="text-xs font-semibold text-brand-600" @click="readAll()" x-show="count > 0">Hammasini o'qilgan qilish</button>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <template x-for="i in items" :key="i.id">
                <a :href="i.url || '#'" @click="markRead(i)" class="block border-b border-ink-100 px-4 py-3 text-sm hover:bg-ink-50 dark:border-ink-800 dark:hover:bg-ink-800" :class="i.read ? 'opacity-60' : ''">
                    <div class="flex items-center gap-2 font-semibold text-ink-900 dark:text-white"><span x-show="i.important" class="badge-red">Muhim</span><span x-text="i.title"></span></div>
                    <div class="mt-0.5 text-ink-600 dark:text-ink-300" x-text="i.body"></div>
                    <div class="mt-1 text-xs text-ink-400" x-text="i.at"></div>
                </a>
            </template>
            <p x-show="items.length === 0" class="px-4 py-8 text-center text-sm text-ink-500">Hozircha bildirishnoma yo'q.</p>
        </div>
        <div class="border-t border-ink-100 p-3 dark:border-ink-800" x-show="canEnable" x-cloak>
            <button type="button" class="btn-secondary w-full text-xs" @click="enable()">Brauzer bildirishnomasi va push'ni yoqish</button>
        </div>
    </div>

    <div class="pointer-events-none fixed right-4 top-4 z-[90] flex w-80 max-w-[92vw] flex-col gap-2">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto rounded-xl border bg-white p-3 shadow-pop dark:bg-ink-900" :class="t.important ? 'border-brand-500' : 'border-ink-200 dark:border-ink-700'">
                <div class="flex items-start justify-between gap-2">
                    <div><div class="text-sm font-semibold text-ink-900 dark:text-white" x-text="t.title"></div><div class="mt-0.5 text-sm text-ink-600 dark:text-ink-300" x-text="t.body"></div></div>
                    <button type="button" class="text-ink-400" @click="dismiss(t.id)" aria-label="Yopish">✕</button>
                </div>
            </div>
        </template>
    </div>
</div>
