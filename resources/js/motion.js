/*
 * v14: yengil animatsiyalar - raqamlar "sanab" chiqiladi (count-up), sparkline chiziqlari chiziladi,
 * varonka bosqichlari ketma-ket ochiladi. prefers-reduced-motion yoqilgan bo'lsa animatsiyasiz ishlaydi.
 * Belgilar: [data-countup="123" data-unit="money|percent|int"], svg.spark path, svg.funnel-anim > g.
 */
const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const nf = new Intl.NumberFormat('ru-RU');

export function formatValue(v, unit) {
    const sp = (s) => s.replace(/\u00a0/g, ' ');
    if (unit === 'money') return sp(nf.format(Math.round(v))) + " so'm";
    if (unit === 'num') return (v < 0 ? '-' : '') + sp(nf.format(Math.abs(Math.round(v))));
    if (unit === 'percent') return nf.format(Math.round(v * 10) / 10) + '%';
    return nf.format(Math.round(v)).replace(/\u00a0/g, ' ');
}

const ease = (t) => 1 - Math.pow(1 - t, 4);

export function countUp(el, to, unit = el.dataset.unit || 'int', from = 0, duration = 1100) {
    to = Number(to);
    if (!Number.isFinite(to)) return;
    el.dataset.countup = String(to);
    if (reduce || from === to) { el.textContent = formatValue(to, unit); return; }
    el.classList.remove('count-done'); el.classList.add('counting');
    const start = performance.now();
    const step = (now) => {
        const p = Math.min(1, (now - start) / duration);
        el.textContent = formatValue(from + (to - from) * ease(p), unit);
        if (p < 1) requestAnimationFrame(step);
        else { el.classList.remove('counting'); el.classList.add('count-done'); setTimeout(() => el.classList.remove('count-done'), 700); }
    };
    requestAnimationFrame(step);
}

const onVisible = (selector, fn) => {
    const els = document.querySelectorAll(selector);
    if (!els.length) return;
    if (!('IntersectionObserver' in window)) { els.forEach((e) => fn(e)); return; }
    const io = new IntersectionObserver((entries) => {
        entries.forEach((en) => { if (en.isIntersecting) { io.unobserve(en.target); fn(en.target); } });
    }, { threshold: 0.2 });
    els.forEach((e) => io.observe(e));
};

function init() {
    onVisible('[data-countup]', (el) => countUp(el, el.dataset.countup));

    onVisible('svg.spark', (svg) => {
        if (reduce || !svg.animate) return;
        svg.querySelectorAll('path[data-line]').forEach((p) => p.animate([{ strokeDashoffset: 1 }, { strokeDashoffset: 0 }], { duration: 900, easing: 'ease-out', fill: 'backwards' }));
        svg.querySelectorAll('path[data-area]').forEach((p) => p.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 900, delay: 300, fill: 'backwards' }));
    });

    onVisible('svg.funnel-anim', (svg) => {
        if (reduce || !svg.animate) return;
        svg.querySelectorAll(':scope > g').forEach((g, i) => {
            g.style.transformBox = 'fill-box';
            g.style.transformOrigin = 'center top';
            g.animate([{ opacity: 0, transform: 'scaleX(.35) translateY(-8px)' }, { opacity: 1, transform: 'none' }], { duration: 650, delay: i * 140, easing: 'cubic-bezier(.2,.8,.2,1)', fill: 'backwards' });
        });
    });

    // KPI'larni 5 daqiqada bir yangilash (keshlangan JSON): sahifa qayta yuklanmaydi, raqamlar silliq o'zgaradi
    const feed = document.querySelector('[data-kpi-feed]');
    if (feed) {
        setInterval(async () => {
            if (document.hidden) return;
            try {
                const r = await fetch(feed.dataset.kpiFeed, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                if (!r.ok) return;
                const data = await r.json();
                const up = document.querySelector('[data-kpi-updated]');
                if (up && data.updated) up.textContent = data.updated;
                (data.cards || []).forEach((c) => {
                    const v = feed.querySelector(`[data-kpi="${c.key}"] [data-countup]`);
                    if (v && c.value !== null) countUp(v, c.value, c.unit, Number(v.dataset.countup) || 0, 800);
                });
            } catch (_) { /* tarmoq xatosi - keyingi safar */ }
        }, 300000);
    }
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
window.EdunovaMotion = { countUp, formatValue };
