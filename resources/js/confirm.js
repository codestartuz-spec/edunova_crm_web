/* Yagona tasdiqlash oynasi: data-confirm va eski onsubmit="return confirm('...')" ni qamrab oladi. */
const MSG = /confirm\(\s*(['"])((?:\\.|(?!\1)[^\\])*)\1\s*\)/;

const legacy = () => {
    document.querySelectorAll('form[onsubmit*="confirm("]').forEach((f) => {
        const m = f.getAttribute('onsubmit').match(MSG);
        if (!m) return;
        f.dataset.confirm = m[2].replace(/\\(.)/g, '$1');
        f.removeAttribute('onsubmit');
    });
};

let pending = null;

const modal = () => document.getElementById('confirm-modal');
let lastFocus = null;
const TONES = {
    danger: {
        icon: 'bg-danger-50 text-danger-600 ring-danger-100 dark:bg-danger-950/60 dark:text-danger-300 dark:ring-danger-500/20',
        halo: 'confirm-halo--danger', strip: 'from-danger-500 to-accent-400', glow: 'bg-danger-400/20', ok: 'btn-danger',
    },
    info: {
        icon: 'bg-brand-50 text-brand-600 ring-brand-100 dark:bg-brand-950/60 dark:text-brand-300 dark:ring-brand-500/20',
        halo: 'confirm-halo--brand', strip: 'from-brand-500 to-accent-400', glow: 'bg-brand-400/20', ok: 'btn-primary',
    },
    success: {
        icon: 'bg-emerald-50 text-emerald-600 ring-emerald-100 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-500/20',
        halo: 'confirm-halo--success', strip: 'from-emerald-500 to-teal-400', glow: 'bg-emerald-400/20', ok: 'btn-primary',
    },
};

const close = () => {
    const m = modal();
    if (!m) return;
    const done = () => { m.classList.add('hidden'); m.classList.remove('flex', 'is-closing'); pending = null; lastFocus?.focus?.(); };
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return done();
    m.classList.add('is-closing');
    setTimeout(done, 160);
};

const open = (form, submitter) => {
    const m = modal();
    if (!m) return true;
    m.querySelector('#confirm-title').textContent = form.dataset.confirmTitle || 'Tasdiqlaysizmi?';
    m.querySelector('#confirm-text').textContent = form.dataset.confirm;
    const tone = TONES[form.dataset.confirmTone] ? form.dataset.confirmTone : (form.dataset.confirmOk && /chiqish/i.test(form.dataset.confirmOk) ? 'info' : 'danger');
    const ok = m.querySelector('#confirm-ok');
    ok.textContent = form.dataset.confirmOk || 'Ha';
    const t = TONES[tone];
    ok.className = `${t.ok} confirm-btn justify-center py-3`;
    m.querySelector('#confirm-icon').className = `relative flex h-14 w-14 items-center justify-center rounded-full shadow-inner ring-1 ring-inset ${t.icon}`;
    m.querySelector('#confirm-halo').className = `confirm-halo mx-auto mb-5 flex h-[4.5rem] w-[4.5rem] items-center justify-center rounded-full ${t.halo}`;
    m.querySelector('#confirm-strip').className = `absolute inset-x-0 top-0 h-1 bg-gradient-to-r ${t.strip}`;
    m.querySelector('#confirm-glow').className = `pointer-events-none absolute -top-16 left-1/2 h-40 w-72 -translate-x-1/2 rounded-full blur-3xl ${t.glow}`;
    m.querySelectorAll('[data-icon]').forEach((el) => el.classList.toggle('hidden', el.dataset.icon !== tone));
    lastFocus = document.activeElement;
    m.classList.remove('hidden', 'is-closing'); m.classList.add('flex');
    pending = { form, submitter };
    m.querySelector('#confirm-panel').focus({ preventScroll: true });
    return false;
};

document.addEventListener('DOMContentLoaded', legacy);

document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!form.dataset || !form.dataset.confirm || form.dataset.confirmed === '1') return;
    e.preventDefault();
    e.stopImmediatePropagation();
    open(form, e.submitter);
}, true);

document.addEventListener('click', (e) => {
    if (e.target.closest('#confirm-cancel') || e.target.closest('[data-confirm-dismiss]')) close();
    if (e.target.closest('#confirm-ok') && pending) {
        const { form, submitter } = pending;
        form.dataset.confirmed = '1';
        close();
        form.requestSubmit(submitter || undefined);
    }
});

document.addEventListener('keydown', (e) => {
    if (!pending) return;
    if (e.key === 'Escape') return close();
    if (e.key === 'Tab') { // fokus oyna ichida qoladi
        const f = [...modal().querySelectorAll('button')].filter((b) => !b.disabled);
        const first = f[0], last = f[f.length - 1];
        if (!modal().contains(document.activeElement) || document.activeElement.id === 'confirm-panel') { e.preventDefault(); (e.shiftKey ? last : first).focus(); }
        else if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
});
