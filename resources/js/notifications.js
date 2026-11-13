/* v14: hodimlar bildirishnomalari: qo'ng'iroqcha, toast, brauzer Notification va Web Push obunasi. */
const b64 = (s) => Uint8Array.from(atob(s.replace(/-/g, '+').replace(/_/g, '/').padEnd(Math.ceil(s.length / 4) * 4, '=')), (c) => c.charCodeAt(0));
const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export default (urls) => ({
    open: false, count: 0, items: [], toasts: [], seen: null, canEnable: false, timer: null,

    init() {
        this.canEnable = 'Notification' in window && Notification.permission === 'default';
        this.load();
        this.timer = setInterval(() => { if (!document.hidden) this.load(); }, 30000);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) this.load(); });
    },

    async load() {
        try {
            const r = await fetch(urls.feed, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!r.ok) return;
            const d = await r.json();
            this.count = d.count;
            this.items = d.items;
            const max = d.items.reduce((m, i) => Math.max(m, i.id), 0);
            if (this.seen === null) { this.seen = parseInt(localStorage.getItem('notif_seen') || '0', 10) || max; }
            d.items.filter((i) => !i.read && i.id > this.seen).reverse().forEach((i) => this.announce(i));
            if (max > this.seen) { this.seen = max; try { localStorage.setItem('notif_seen', String(max)); } catch (e) {} }
        } catch (e) {}
    },

    announce(i) {
        const t = { ...i, key: i.id };
        this.toasts.push(t);
        if (!i.important) setTimeout(() => this.dismiss(i.id), 8000);
        if ('Notification' in window && Notification.permission === 'granted' && document.hidden) {
            try { new Notification(i.title, { body: i.body, tag: 'n' + i.id }); } catch (e) {}
        }
    },

    dismiss(id) { this.toasts = this.toasts.filter((t) => t.id !== id); },

    async markRead(i) {
        if (i.read) return;
        i.read = true;
        try {
            const r = await fetch(urls.read.replace('__ID__', i.id), { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' }, credentials: 'same-origin' });
            this.count = (await r.json()).count;
        } catch (e) {}
    },

    async readAll() {
        try {
            await fetch(urls.readAll, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' }, credentials: 'same-origin' });
            this.items.forEach((i) => (i.read = true));
            this.count = 0;
        } catch (e) {}
    },

    async enable() {
        const perm = await Notification.requestPermission();
        this.canEnable = perm === 'default';
        if (perm !== 'granted' || !('serviceWorker' in navigator) || !('PushManager' in window)) return;
        try {
            const reg = await navigator.serviceWorker.register(urls.sw);
            const { key } = await (await fetch(urls.key, { credentials: 'same-origin' })).json();
            if (!key) return;
            const sub = (await reg.pushManager.getSubscription()) || (await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: b64(key) }));
            await fetch(urls.subscribe, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' }, credentials: 'same-origin', body: JSON.stringify(sub.toJSON()) });
        } catch (e) {}
    },
});
