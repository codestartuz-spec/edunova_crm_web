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
    danger: { icon: 'bg-brand-50 text-brand-600 dark:bg-brand-950/60 dark:text-brand-300', ok: 'btn-primary' },
    info: { icon: 'bg-ink-100 text-ink-700 dark:bg-ink-800 dark:text-ink-200', ok: 'btn-danger' },
    success: { icon: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300', ok: 'btn-primary' },
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
    ok.className = `${TONES[tone].ok} justify-center sm:min-w-[7.5rem]`;
    m.querySelector('#confirm-icon').className = `flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ${TONES[tone].icon}`;
    m.querySelectorAll('[data-icon]').forEach((el) => el.classList.toggle('hidden', el.dataset.icon !== tone));
    lastFocus = document.activeElement;
    m.classList.remove('hidden', 'is-closing'); m.classList.add('flex');
    pending = { form, submitter };
    m.querySelector('#confirm-cancel').focus();
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
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
});
