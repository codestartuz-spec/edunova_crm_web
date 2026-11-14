/*
 * v28: 3D tizimi boshqaruvi.
 *  - fx darajasi: full | lite | off (localStorage 'edu-fx'; tanlanmagan bo'lsa qurilmaga qarab)
 *  - animatsion SVG fon parallaksi (sichqoncha + scroll), faqat "full"da
 *  - rich sahifalarda kartalarning sichqoncha ortidan qiyshayishi (faqat "full" va sichqoncha bor qurilmada)
 */
const KEY = 'edu-fx';
const LEVELS = ['full', 'lite', 'off'];
const root = document.documentElement;
const mq = (q) => window.matchMedia && window.matchMedia(q).matches;

export function detectFx() {
    try {
        const v = localStorage.getItem(KEY);
        if (LEVELS.includes(v)) return v;
    } catch (_) {}
    if (mq('(prefers-reduced-motion: reduce)')) return 'off';
    const lowMem = navigator.deviceMemory && navigator.deviceMemory <= 2;
    const lowCpu = navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2;
    return lowMem || lowCpu ? 'lite' : 'full';
}

export function setFx(level) {
    if (!LEVELS.includes(level)) return;
    try { localStorage.setItem(KEY, level); } catch (_) {}
    root.dataset.fx = level;
    window.dispatchEvent(new CustomEvent('edufxchange', { detail: level }));
}

if (!root.dataset.fx) root.dataset.fx = detectFx();
window.eduFx = { get: () => root.dataset.fx, set: setFx, levels: LEVELS };

const fx = () => root.dataset.fx;
const fine = mq('(hover: hover) and (pointer: fine)');

/* ---------- Fon parallaksi ---------- */
(function bgParallax() {
    const bg = document.querySelector('.edu-bg');
    if (!bg) return;
    const AMT = { 'edu-l0': [8, 6, 30], 'edu-l1': [18, 14, 70], 'edu-l2': [34, 26, 130], 'edu-ld': [12, 10, 50] };
    const layers = [...bg.querySelectorAll('.edu-layer')].map((el) => {
        const key = Object.keys(AMT).find((k) => el.classList.contains(k));
        return { el, a: AMT[key] || [10, 8, 40] };
    });
    if (!layers.length) return;

    let tx = 0, ty = 0, cx = 0, cy = 0, ts = 0, cs = 0, run = false;
    const apply = () => {
        const on = fx() === 'full';
        layers.forEach(({ el, a }) => {
            el.style.transform = on ? `translate3d(${(cx * a[0]).toFixed(1)}px, ${(cy * a[1] - cs * a[2]).toFixed(1)}px, 0)` : '';
        });
    };
    const loop = () => {
        cx += (tx - cx) * .07; cy += (ty - cy) * .07; cs += (ts - cs) * .1;
        apply();
        if (Math.abs(tx - cx) > .002 || Math.abs(ty - cy) > .002 || Math.abs(ts - cs) > .002) requestAnimationFrame(loop); else run = false;
    };
    const go = () => { if (!run) { run = true; requestAnimationFrame(loop); } };

    if (fine) {
        window.addEventListener('pointermove', (e) => {
            if (fx() !== 'full') return;
            tx = (e.clientX / window.innerWidth - .5) * 2;
            ty = (e.clientY / window.innerHeight - .5) * 2;
            go();
        }, { passive: true });
    }
    window.addEventListener('scroll', () => {
        if (fx() !== 'full') return;
        ts = Math.min(1, window.scrollY / (window.innerHeight * 2));
        go();
    }, { passive: true });
    window.addEventListener('edufxchange', apply);
    apply();
})();

/* ---------- Kartalar qiyshayishi (rich rejim) ---------- */
(function tilt() {
    if (!fine) return;
    const SEL = '.stat-card, .card-hover, .action-card, [data-tilt]';
    const MAX = 6;
    let cur = null, raf = 0;

    const reset = (el) => {
        if (!el) return;
        ['--rx', '--ry', '--tz'].forEach((k) => el.style.removeProperty(k));
    };
    const active = () => fx() === 'full' && document.body && document.body.dataset.fxMode === 'rich';

    document.addEventListener('pointermove', (e) => {
        if (!active()) { if (cur) { reset(cur); cur = null; } return; }
        const t = e.target.closest ? e.target.closest(SEL) : null;
        if (t !== cur) { reset(cur); cur = t; }
        if (!t) return;
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
            const r = t.getBoundingClientRect();
            if (r.width > 780 || r.height > 460) return;
            const px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
            t.style.setProperty('--ry', ((px - .5) * 2 * MAX).toFixed(2) + 'deg');
            t.style.setProperty('--rx', (-(py - .5) * 2 * MAX).toFixed(2) + 'deg');
        });
    }, { passive: true });
    document.addEventListener('pointerleave', () => { cancelAnimationFrame(raf); reset(cur); cur = null; }, true);
    window.addEventListener('blur', () => { reset(cur); cur = null; });
})();
