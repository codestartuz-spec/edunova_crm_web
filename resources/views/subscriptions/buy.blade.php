@extends('layouts.app')
@section('title', 'Obuna: '.$branch->name)

@php
    $left = $branch->subscriptionDaysLeft();
    $exempt = (bool) $branch->subscription_exempt;
    $expired = $branch->subscriptionExpired();
    $lastDay = $branch->subscriptionLastDay();
    $startsFrom = ($branch->subscription_ends_on && $branch->subscription_ends_on->copy()->startOfDay()->gt(today()))
        ? $branch->subscription_ends_on->copy()->startOfDay() : today();
    $cardDigits = preg_replace('/\D/', '', (string) $card);
    $cardPretty = (strlen($cardDigits) === 16 && preg_match('/^[\d\s]+$/', (string) $card)) ? trim(chunk_split($cardDigits, 4, ' ')) : (string) $card;
    $savePct = ($monthly > 0 && $monthly * 12 > $yearly) ? (int) round(($monthly * 12 - $yearly) / ($monthly * 12) * 100) : 0;
    $perMonth = (int) round($yearly / 12);
    $termLines = collect(preg_split('/\R/', trim((string) $terms)))
        ->map(fn ($l) => trim(preg_replace('/^\s*\d+[.)]\s*/', '', $l)))->filter()->values();
    $cfg = [
        'plan' => old('plan') === 'yearly' ? 'yearly' : 'monthly',
        'prices' => ['monthly' => (int) $monthly, 'yearly' => (int) $yearly],
        'from' => $startsFrom->toDateString(),
        'card' => preg_match('/^[\d\s]+$/', (string) $card) ? $cardDigits : (string) $card,
    ];
@endphp

@section('content')
<style>
    .bx { --bx-ease: cubic-bezier(.22, 1, .36, 1); }

    /* ---------- kirish animatsiyasi ---------- */
    @keyframes bx-rise { from { opacity: 0; transform: translateY(20px) scale(.985); } to { opacity: 1; transform: none; } }
    .bx-rise { animation: bx-rise .75s var(--bx-ease) both; animation-delay: calc(var(--d, 0) * 90ms); }

    /* ---------- hero + 3D sahna ---------- */
    .bx-hero { position: relative; overflow: hidden; display: grid; gap: 1rem; padding: 1.4rem;
        background: radial-gradient(560px 280px at 92% -10%, rgb(var(--brand-500) / .20), transparent 70%),
                    radial-gradient(420px 240px at -5% 110%, rgb(var(--brand-300) / .18), transparent 70%); }
    .dark .bx-hero { background: radial-gradient(560px 280px at 92% -10%, rgb(var(--brand-500) / .28), transparent 70%),
                    radial-gradient(420px 240px at -5% 110%, rgb(var(--brand-700) / .22), transparent 70%); }
    @media (min-width: 768px) { .bx-hero { grid-template-columns: 1.05fr .95fr; align-items: center; padding: 1.75rem 2rem; } }
    .bx-eyebrow { font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: rgb(var(--brand-600)); }
    .dark .bx-eyebrow { color: rgb(var(--brand-300)); }
    .bx-big { margin-top: .35rem; font-size: clamp(2rem, 5vw, 2.9rem); font-weight: 800; line-height: 1.05; letter-spacing: -.02em;
        background: linear-gradient(135deg, rgb(var(--brand-500)), rgb(var(--brand-800))); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .dark .bx-big { background-image: linear-gradient(135deg, rgb(var(--brand-300)), rgb(var(--brand-500))); }
    .bx-big small { font-size: 1rem; font-weight: 600; letter-spacing: 0; }
    .bx-big.is-bad { background-image: linear-gradient(135deg, #f43f5e, #be123c); }
    .bx-pill { display: inline-flex; align-items: center; gap: .4rem; border-radius: 999px; padding: .32rem .75rem; font-size: .75rem; font-weight: 600;
        background: rgb(var(--brand-500) / .10); color: rgb(var(--brand-700)); border: 1px solid rgb(var(--brand-500) / .18); }
    .dark .bx-pill { background: rgb(var(--brand-400) / .12); color: rgb(var(--brand-200)); border-color: rgb(var(--brand-300) / .2); }

    .bx-stage { position: relative; display: none; height: 220px; perspective: 900px; }
    @media (min-width: 768px) { .bx-stage { display: block; } }
    .bx-world { position: absolute; inset: 0; transform-style: preserve-3d;
        transform: rotateX(calc(10deg + var(--wx, 0deg))) rotateY(calc(-16deg + var(--wy, 0deg))); }
    .bx-floor { position: absolute; left: 50%; top: 52%; width: 380px; height: 380px; margin: -190px 0 0 -190px;
        transform: translateY(86px) rotateX(78deg); border-radius: 50%;
        background-image: linear-gradient(rgb(var(--brand-500) / .32) 1px, transparent 1px), linear-gradient(90deg, rgb(var(--brand-500) / .32) 1px, transparent 1px);
        background-size: 34px 34px; -webkit-mask-image: radial-gradient(closest-side, #000 25%, transparent 100%); mask-image: radial-gradient(closest-side, #000 25%, transparent 100%); }
    .bx-glow { position: absolute; border-radius: 50%; filter: blur(26px); background: rgb(var(--brand-400) / .45); animation: bx-pulse 5s ease-in-out infinite; }
    @keyframes bx-pulse { 0%, 100% { opacity: .55; transform: scale(1); } 50% { opacity: .95; transform: scale(1.18); } }
    .bx-float { position: absolute; transform-style: preserve-3d; animation: bx-bob var(--t, 5s) ease-in-out infinite; animation-delay: var(--dl, 0s); }
    @keyframes bx-bob { 0%, 100% { transform: translate3d(0, 0, var(--z, 0px)); } 50% { transform: translate3d(0, -16px, var(--z, 0px)); } }

    .bx-coin { position: relative; width: var(--s, 96px); height: var(--s, 96px); transform-style: preserve-3d; animation: bx-coin 8s linear infinite; }
    @keyframes bx-coin { to { transform: rotateY(360deg); } }
    .bx-coin i { position: absolute; inset: 0; display: grid; place-items: center; border-radius: 50%; font-style: normal; font-weight: 800;
        font-size: calc(var(--s) * .2); letter-spacing: .04em; color: #92400e;
        background: radial-gradient(circle at 34% 28%, #fde68a, #f59e0b 58%, #b45309);
        box-shadow: inset 0 0 0 calc(var(--s) * .035) rgb(180 83 9 / .55), inset 0 0 0 calc(var(--s) * .07) rgb(253 230 138 / .55);
        transform: translateZ(calc(var(--k, 0) * 1px)); }
    .bx-coin i:not(.f):not(.b) { filter: brightness(.78); color: transparent; }
    .bx-coin i.b { transform: translateZ(-7px) rotateY(180deg); }

    .bx-cube { position: relative; width: var(--s, 56px); height: var(--s, 56px); transform-style: preserve-3d; animation: bx-cube 14s linear infinite; }
    @keyframes bx-cube { from { transform: rotateX(-24deg) rotateY(0); } to { transform: rotateX(-24deg) rotateY(360deg); } }
    .bx-cube b { position: absolute; inset: 0; border-radius: 9px; border: 1px solid rgb(255 255 255 / .4);
        background: linear-gradient(135deg, rgb(var(--brand-400) / .92), rgb(var(--brand-800) / .92)); box-shadow: inset 0 0 18px rgb(255 255 255 / .28); }
    .bx-cube b:nth-child(1) { transform: translateZ(calc(var(--s) / 2)); }
    .bx-cube b:nth-child(2) { transform: rotateY(90deg) translateZ(calc(var(--s) / 2)); }
    .bx-cube b:nth-child(3) { transform: rotateY(180deg) translateZ(calc(var(--s) / 2)); }
    .bx-cube b:nth-child(4) { transform: rotateY(-90deg) translateZ(calc(var(--s) / 2)); }
    .bx-cube b:nth-child(5) { transform: rotateX(90deg) translateZ(calc(var(--s) / 2)); }
    .bx-cube b:nth-child(6) { transform: rotateX(-90deg) translateZ(calc(var(--s) / 2)); }

    /* ---------- bosqichlar (stepper) ---------- */
    .bx-steps { display: flex; align-items: flex-start; justify-content: space-between; margin: 0 0 1.4rem; padding: 0; list-style: none; }
    .bx-steps li { position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; gap: .45rem; text-align: center; font-size: .75rem; font-weight: 600; color: rgb(var(--brand-900) / .55); }
    .dark .bx-steps li { color: rgb(255 255 255 / .45); }
    .bx-steps li + li::before { content: ''; position: absolute; top: 19px; right: 50%; width: 100%; height: 3px; border-radius: 3px; background: rgb(var(--brand-500) / .16); z-index: 0; }
    .bx-steps li + li::after { content: ''; position: absolute; top: 19px; right: 50%; width: 100%; height: 3px; border-radius: 3px; background: linear-gradient(90deg, rgb(var(--brand-400)), rgb(var(--brand-600)));
        transform: scaleX(0); transform-origin: right; transition: transform .6s var(--bx-ease); z-index: 0; }
    .bx-steps li.is-done + li::after { transform: scaleX(1); }
    .bx-node { position: relative; z-index: 1; display: grid; place-items: center; width: 40px; height: 40px; border-radius: 13px; font-size: .95rem; font-weight: 800;
        color: rgb(var(--brand-700)); background: #fff; border: 1px solid rgb(var(--brand-300) / .6);
        box-shadow: 0 8px 14px -8px rgb(var(--brand-600) / .5), inset 0 -3px 0 rgb(var(--brand-500) / .12);
        transform: perspective(300px) rotateX(18deg); transition: transform .5s var(--bx-ease), background .3s, color .3s, box-shadow .3s; }
    .dark .bx-node { background: rgb(var(--brand-950)); color: rgb(var(--brand-200)); border-color: rgb(var(--brand-400) / .35); }
    .bx-node .ic-ok { display: none; }
    .bx-steps li.is-cur .bx-node { color: #fff; background: linear-gradient(135deg, rgb(var(--brand-500)), rgb(var(--brand-700))); border-color: transparent;
        transform: perspective(300px) rotateX(0) translateZ(14px) scale(1.1); box-shadow: 0 16px 24px -10px rgb(var(--brand-600) / .85); animation: bx-ping 2.2s ease-in-out infinite; }
    @keyframes bx-ping { 0%, 100% { box-shadow: 0 16px 24px -10px rgb(var(--brand-600) / .85), 0 0 0 0 rgb(var(--brand-500) / .35); } 50% { box-shadow: 0 16px 24px -10px rgb(var(--brand-600) / .85), 0 0 0 9px rgb(var(--brand-500) / 0); } }
    .bx-steps li.is-done .bx-node { color: #fff; background: linear-gradient(135deg, #34d399, #059669); border-color: transparent; transform: perspective(300px) rotateX(0); box-shadow: 0 10px 18px -8px rgba(5, 150, 105, .7); animation: none; }
    .bx-steps li.is-done .bx-node .n { display: none; }
    .bx-steps li.is-done .bx-node .ic-ok { display: block; }
    .bx-steps li.is-cur, .bx-steps li.is-done { color: rgb(var(--brand-800)); }
    .dark .bx-steps li.is-cur, .dark .bx-steps li.is-done { color: #fff; }

    /* ---------- tarif kartalari ---------- */
    .bx-plans { display: grid; gap: 1rem; perspective: 1200px; }
    @media (min-width: 640px) { .bx-plans { grid-template-columns: 1fr 1fr; } }
    .bx-plan { position: relative; display: block; cursor: pointer; border-radius: 20px; padding: 1.15rem 1.2rem 1.1rem; background: #fff;
        border: 1px solid rgb(var(--brand-200) / .8); transform-style: preserve-3d;
        transform: perspective(1000px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) translateZ(var(--tz, 0px));
        transition: transform .25s ease-out, box-shadow .35s, border-color .3s, background .3s; box-shadow: 0 10px 24px -18px rgb(var(--brand-700) / .45); }
    .dark .bx-plan { background: rgb(255 255 255 / .03); border-color: rgb(255 255 255 / .10); }
    .bx-plan:hover { --tz: 8px; }
    .bx-plan:focus-within { outline: 2px solid rgb(var(--brand-500) / .55); outline-offset: 3px; }
    .bx-plan.is-on { --tz: 20px; border-color: rgb(var(--brand-500)); background: linear-gradient(160deg, rgb(var(--brand-50)), #fff 70%);
        box-shadow: 0 0 0 3px rgb(var(--brand-500) / .18), 0 28px 40px -24px rgb(var(--brand-600) / .7); }
    .dark .bx-plan.is-on { background: linear-gradient(160deg, rgb(var(--brand-500) / .22), rgb(var(--brand-950) / .55) 75%); }
    .bx-plan::after { content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; opacity: 0; transition: opacity .3s;
        background: radial-gradient(220px circle at var(--gx, 50%) var(--gy, 0%), rgb(255 255 255 / .55), transparent 62%); }
    .dark .bx-plan::after { background: radial-gradient(220px circle at var(--gx, 50%) var(--gy, 0%), rgb(255 255 255 / .10), transparent 62%); }
    .bx-plan:hover::after { opacity: 1; }
    .bx-plan > * { transform: translateZ(16px); }
    .bx-plan .bx-tick { position: absolute; top: .9rem; right: .9rem; display: grid; place-items: center; width: 26px; height: 26px; border-radius: 50%;
        color: #fff; background: linear-gradient(135deg, rgb(var(--brand-500)), rgb(var(--brand-700))); transform: translateZ(30px) scale(0); transition: transform .45s cubic-bezier(.34, 1.56, .64, 1); }
    .bx-plan.is-on .bx-tick { transform: translateZ(30px) scale(1); }
    .bx-plan .bx-price { display: block; margin-top: .5rem; font-size: 1.55rem; font-weight: 800; letter-spacing: -.02em; transform: translateZ(28px); }
    .bx-save { position: relative; display: inline-flex; align-items: center; gap: .3rem; margin-top: .55rem; border-radius: 999px; padding: .25rem .65rem; overflow: hidden;
        font-size: .72rem; font-weight: 700; color: #047857; background: #d1fae5; }
    .dark .bx-save { color: #6ee7b7; background: rgb(16 185 129 / .16); }
    .bx-save::after { content: ''; position: absolute; inset: 0; background: linear-gradient(110deg, transparent 35%, rgb(255 255 255 / .75) 50%, transparent 65%); transform: translateX(-120%); animation: bx-shine 3.4s ease-in-out infinite; }
    @keyframes bx-shine { 0%, 55% { transform: translateX(-120%); } 100% { transform: translateX(120%); } }
    .bx-best { position: absolute; top: -11px; left: 1.1rem; border-radius: 999px; padding: .15rem .6rem; font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
        color: #fff; background: linear-gradient(135deg, #f59e0b, #ea580c); box-shadow: 0 8px 14px -6px rgb(234 88 12 / .7); transform: translateZ(34px); }

    /* ---------- summa paneli ---------- */
    .bx-sum { display: grid; gap: .9rem; margin-top: 1.1rem; padding: 1rem 1.1rem; border-radius: 18px; border: 1px dashed rgb(var(--brand-400) / .55);
        background: linear-gradient(135deg, rgb(var(--brand-500) / .07), rgb(var(--brand-300) / .05)); }
    @media (min-width: 640px) { .bx-sum { grid-template-columns: 1fr 1fr auto; align-items: center; } }
    .bx-amount { margin-top: .15rem; font-size: 1.6rem; font-weight: 800; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .bx-until { margin-top: .15rem; font-size: 1.05rem; font-weight: 700; }
    .bx-sub { font-size: .72rem; opacity: .75; }
    .bx-mini { display: inline-flex; align-items: center; gap: .4rem; border-radius: 12px; padding: .55rem .85rem; font-size: .78rem; font-weight: 700; cursor: pointer;
        color: rgb(var(--brand-700)); background: #fff; border: 1px solid rgb(var(--brand-300) / .7); transition: transform .2s, box-shadow .2s; }
    .dark .bx-mini { color: rgb(var(--brand-200)); background: rgb(255 255 255 / .05); border-color: rgb(255 255 255 / .14); }
    .bx-mini:hover { transform: translateY(-2px); box-shadow: 0 10px 18px -10px rgb(var(--brand-600) / .6); }

    /* ---------- 3D bank kartasi ---------- */
    .bx-ccwrap { perspective: 1100px; padding: .4rem .2rem .2rem; container-type: inline-size; }
    .bx-cc { position: relative; max-width: 390px; margin: 0 auto; aspect-ratio: 1.586; transform-style: preserve-3d;
        transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)); transition: transform .25s ease-out; }
    .bx-cc-idle { position: absolute; inset: 0; transform-style: preserve-3d; animation: bx-idle 9s ease-in-out infinite; }
    @keyframes bx-idle { 0%, 100% { transform: translateY(0) rotateX(5deg) rotateY(-9deg); } 50% { transform: translateY(-7px) rotateX(-3deg) rotateY(9deg); } }
    .bx-cc-bg { position: absolute; inset: 0; overflow: hidden; border-radius: 22px;
        background: linear-gradient(135deg, rgb(var(--brand-500)), rgb(var(--brand-800)) 68%, rgb(var(--brand-950)));
        box-shadow: 0 34px 50px -26px rgb(var(--brand-700) / .8), 0 12px 22px -12px rgb(0 0 0 / .4), inset 0 1px 0 rgb(255 255 255 / .3); }
    .bx-cc-bg::before { content: ''; position: absolute; inset: -45%; background: conic-gradient(from 200deg at 72% 18%, transparent 0 30%, rgb(255 255 255 / .16) 40%, transparent 52%); animation: bx-spin-bg 14s linear infinite; }
    @keyframes bx-spin-bg { to { transform: rotate(360deg); } }
    .bx-cc-bg::after { content: ''; position: absolute; inset: 0; background: radial-gradient(280px circle at var(--gx, 28%) var(--gy, 8%), rgb(255 255 255 / .30), transparent 62%); }
    .bx-cc-body { position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: space-between; padding: 1.15rem 1.3rem; color: #fff; transform-style: preserve-3d; }
    .bx-cc-top, .bx-cc-bot { display: flex; align-items: center; justify-content: space-between; gap: .8rem; }
    .bx-cc-tag { font-size: .7rem; font-weight: 800; letter-spacing: .28em; opacity: .85; }
    .bx-chip { position: relative; display: grid; grid-template-columns: 1fr 1fr; gap: 2px; width: 44px; height: 33px; padding: 4px; border-radius: 7px;
        background: linear-gradient(135deg, #fde68a, #d97706); box-shadow: inset 0 0 0 1px rgb(120 53 15 / .4); }
    .bx-chip i { border-radius: 2px; background: rgb(120 53 15 / .28); }
    .bx-chip i:nth-child(3) { grid-column: 1 / 3; }
    .bx-cc-num { font-size: clamp(.9rem, 6.2cqw, 1.5rem); font-weight: 700; letter-spacing: .07em; font-variant-numeric: tabular-nums; white-space: nowrap; text-shadow: 0 2px 8px rgb(0 0 0 / .35); }
    .bx-cc-cap { font-size: .6rem; letter-spacing: .2em; text-transform: uppercase; opacity: .65; }
    .bx-cc-holder { font-size: .95rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bx-cc-pulse { position: relative; width: 38px; height: 26px; flex-shrink: 0; }
    .bx-cc-pulse span { position: absolute; top: 0; width: 26px; height: 26px; border-radius: 50%; }
    .bx-cc-pulse span:first-child { left: 0; background: rgb(244 63 94 / .85); }
    .bx-cc-pulse span:last-child { left: 12px; background: rgb(251 191 36 / .85); mix-blend-mode: screen; }
    .bx-copy { display: flex; align-items: center; justify-content: center; gap: .55rem; width: 100%; margin-top: 1.1rem; border-radius: 14px; padding: .7rem 1rem; font-size: .85rem; font-weight: 700; cursor: pointer;
        color: rgb(var(--brand-700)); background: rgb(var(--brand-500) / .09); border: 1px solid rgb(var(--brand-400) / .45); transition: transform .2s, background .3s, color .3s, border-color .3s; }
    .dark .bx-copy { color: rgb(var(--brand-200)); }
    .bx-copy:hover { transform: translateY(-2px); background: rgb(var(--brand-500) / .15); }
    .bx-copy.is-done { color: #047857; background: #d1fae5; border-color: #6ee7b7; }
    .dark .bx-copy.is-done { color: #6ee7b7; background: rgb(16 185 129 / .16); border-color: rgb(16 185 129 / .4); }
    .bx-copy-ic { position: relative; display: grid; place-items: center; width: 18px; height: 18px; }
    .bx-copy-ic svg { position: absolute; transition: transform .35s cubic-bezier(.34, 1.56, .64, 1), opacity .25s; }
    .bx-ic-check { transform: scale(0) rotate(-40deg); opacity: 0; }
    .bx-copy.is-done .bx-ic-check { transform: none; opacity: 1; }
    .bx-copy.is-done .bx-ic-copy { transform: scale(0); opacity: 0; }

    /* ---------- chek yuklash zonasi ---------- */
    .bx-drop { position: relative; display: block; cursor: pointer; border-radius: 20px; padding: 1.5rem 1rem; text-align: center; overflow: hidden;
        border: 2px dashed rgb(var(--brand-400) / .6); background: rgb(var(--brand-500) / .04); transition: transform .3s var(--bx-ease), border-color .3s, background .3s, box-shadow .3s; }
    .bx-drop:hover, .bx-drop.is-drag { transform: translateY(-3px); border-color: rgb(var(--brand-500)); background: rgb(var(--brand-500) / .09); box-shadow: 0 20px 30px -22px rgb(var(--brand-600) / .7); }
    .bx-drop.is-drag { transform: translateY(-3px) scale(1.015); }
    .bx-drop:focus-within { outline: 2px solid rgb(var(--brand-500) / .55); outline-offset: 3px; }
    .bx-up-ic { display: grid; place-items: center; width: 58px; height: 58px; margin: 0 auto .7rem; border-radius: 18px; color: #fff;
        background: linear-gradient(135deg, rgb(var(--brand-400)), rgb(var(--brand-700))); box-shadow: 0 16px 24px -12px rgb(var(--brand-600) / .85), inset 0 1px 0 rgb(255 255 255 / .35);
        transform: perspective(300px) rotateX(16deg) rotateY(-14deg); animation: bx-up 3.4s ease-in-out infinite; }
    @keyframes bx-up { 0%, 100% { transform: perspective(300px) rotateX(16deg) rotateY(-14deg) translateY(0); } 50% { transform: perspective(300px) rotateX(-6deg) rotateY(14deg) translateY(-7px); } }
    .bx-file { display: flex; align-items: center; gap: .9rem; border-radius: 18px; padding: .8rem; border: 1px solid rgb(16 185 129 / .45); background: rgb(16 185 129 / .07); animation: bx-rise .5s var(--bx-ease) both; }
    .bx-thumb { width: 64px; height: 64px; border-radius: 14px; object-fit: cover; flex-shrink: 0; box-shadow: 0 10px 18px -10px rgb(0 0 0 / .5); transform: perspective(300px) rotateY(-10deg); }

    /* ---------- shartlar ---------- */
    .bx-terms { display: grid; gap: .6rem; }
    .bx-term { display: flex; gap: .7rem; align-items: flex-start; font-size: .85rem; }
    .bx-term b { display: grid; place-items: center; flex-shrink: 0; width: 24px; height: 24px; border-radius: 8px; font-size: .72rem; color: #fff;
        background: linear-gradient(135deg, rgb(var(--brand-500)), rgb(var(--brand-700))); transform: perspective(200px) rotateX(14deg); }

    /* ---------- yuborish tugmasi ---------- */
    .bx-submit { position: relative; display: inline-flex; align-items: center; justify-content: center; gap: .6rem; min-width: 220px; }
    .bx-spin { width: 16px; height: 16px; border-radius: 50%; border: 2px solid rgb(255 255 255 / .4); border-top-color: #fff; animation: bx-rot .7s linear infinite; }
    @keyframes bx-rot { to { transform: rotate(360deg); } }

    @media (prefers-reduced-motion: reduce) {
        .bx *, .bx *::before, .bx *::after { animation: none !important; transition-duration: .01ms !important; }
    }
</style>

<div class="bx">
    <x-page-header icon="wallet" :title="$branch->subscriptionExpired() ? 'Obuna sotib olish' : 'Obunani uzaytirish'" :subtitle="$branch->name.' filiali'">
        <x-slot:actions><a href="{{ route('subscriptions.index') }}" class="btn-secondary">Orqaga</a></x-slot:actions>
    </x-page-header>

    {{-- Hero: joriy holat + 3D sahna --}}
    <div class="bx-hero card bx-rise" style="--d: 0">
        <div class="relative z-10">
            <div class="bx-eyebrow">Joriy holat</div>
            @if ($exempt)
                <div class="bx-big">Cheksiz</div>
            @elseif ($left === null)
                <div class="bx-big">Obuna yo'q</div>
            @elseif ($left <= 0)
                <div class="bx-big is-bad">Muddati tugagan</div>
            @else
                <div class="bx-big"><span data-bx-count="{{ $left }}">{{ $left }}</span> <small>kun qoldi</small></div>
            @endif
            @if (! $exempt && $lastDay)
                <p class="mt-1.5 text-sm text-ink-500 dark:text-ink-300">Oxirgi faol kun: <b class="text-ink-900 dark:text-white">{{ $lastDay->format('d.m.Y') }}</b></p>
            @endif
            <p class="mt-3 max-w-md text-sm text-ink-600 dark:text-ink-300">Tarifni tanlang, summani kartaga o'tkazing va chek rasmini yuklang. Tasdiqlangach obuna avtomatik faollashadi.</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="bx-pill"><x-icon name="clock" class="h-3.5 w-3.5" /> Tekshiruv: {{ \App\Models\SubscriptionRequest::REVIEW_TIME }}</span>
                @if ($expired)
                    <span class="bx-pill"><x-icon name="calendar" class="h-3.5 w-3.5" /> Muddat tasdiqlangan kundan boshlanadi</span>
                @else
                    <span class="bx-pill"><x-icon name="shield" class="h-3.5 w-3.5" /> Qolgan kunlar yo'qolmaydi</span>
                @endif
            </div>
        </div>

        <div class="bx-stage" aria-hidden="true">
            <div class="bx-world">
                <div class="bx-floor"></div>
                <div class="bx-glow" style="left: 38%; top: 40px; width: 110px; height: 110px"></div>
                <div class="bx-float" style="left: 41%; top: 46px; --z: 70px; --t: 6s">@include('subscriptions._coin', ['s' => 104, 'dur' => 9])</div>
                <div class="bx-float" style="left: 10%; top: 118px; --z: 20px; --t: 5s; --dl: -1.5s">@include('subscriptions._coin', ['s' => 62, 'dur' => 6])</div>
                <div class="bx-float" style="left: 76%; top: 34px; --z: 40px; --t: 7s; --dl: -3s"><div class="bx-cube" style="--s: 54px"><b></b><b></b><b></b><b></b><b></b><b></b></div></div>
                <div class="bx-float" style="left: 72%; top: 128px; --z: 10px; --t: 5.5s; --dl: -2s">@include('subscriptions._coin', ['s' => 44, 'dur' => 5])</div>
            </div>
        </div>
    </div>

    @if (! $configured)
        <div class="bx-rise mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" style="--d: 1">Obuna narxi va karta raqami hali belgilanmagan. Administrator bilan bog'laning.</div>
    @elseif ($hasPending)
        <div class="bx-rise mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" style="--d: 1">Bu filial uchun chek allaqachon yuborilgan. Administrator uni {{ \App\Models\SubscriptionRequest::REVIEW_TIME }} ichida ko'rib chiqadi, natija shu sahifada va «Obuna» bo'limida ko'rinadi.</div>
    @else
    <div class="mt-6 grid gap-6 lg:grid-cols-3" x-data="bxBuy(@js($cfg))" @pageshow.window="loading = false">
        <div class="space-y-6 lg:col-span-2">
            {{-- Shartlar (yig'iladigan) --}}
            <div class="card bx-rise" style="--d: 1" x-data="{ open: false }">
                <button type="button" class="flex w-full items-center justify-between gap-3 p-5 text-left" @click="open = !open" :aria-expanded="open">
                    <span class="font-semibold text-ink-900 dark:text-white">Obuna shartlari <span class="ml-1 text-xs font-medium text-ink-500">({{ $termLines->count() }})</span></span>
                    <span class="inline-flex text-ink-500 transition-transform duration-300" :class="open ? 'rotate-180' : ''"><x-icon name="chevron" class="h-5 w-5" /></span>
                </button>
                <div x-show="open" x-transition.opacity x-cloak class="border-t border-ink-100 px-5 pb-5 pt-4 dark:border-ink-800">
                    <div class="bx-terms">
                        @foreach ($termLines as $i => $line)
                            <div class="bx-term"><b>{{ $i + 1 }}</b><span class="text-ink-700 dark:text-ink-200">{{ $line }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('subscriptions.store', $branch) }}" enctype="multipart/form-data" class="card card-body space-y-6 bx-rise" style="--d: 2" @submit="loading = true">
                @csrf
                <x-once />

                {{-- Bosqichlar --}}
                <ol class="bx-steps" aria-label="Bosqichlar">
                    <li class="is-done"><span class="bx-node"><span class="n">1</span><x-icon name="check" class="ic-ok h-5 w-5" /></span>Tarif</li>
                    <li :class="{ 'is-done': s2, 'is-cur': cur === 2 }"><span class="bx-node"><span class="n">2</span><x-icon name="check" class="ic-ok h-5 w-5" /></span>O'tkazma</li>
                    <li :class="{ 'is-done': !!file, 'is-cur': cur === 3 }"><span class="bx-node"><span class="n">3</span><x-icon name="check" class="ic-ok h-5 w-5" /></span>Chek</li>
                </ol>

                {{-- 1. Tarif --}}
                <div>
                    <div class="label">Tarifni tanlang</div>
                    <div class="bx-plans">
                        <label class="bx-plan" :class="{ 'is-on': plan === 'monthly' }" data-bxtilt data-bxtilt-max="7">
                            <input type="radio" name="plan" value="monthly" x-model="plan" class="sr-only">
                            <span class="bx-tick"><x-icon name="check" class="h-4 w-4" /></span>
                            <span class="block font-semibold text-ink-900 dark:text-white">Oylik</span>
                            <span class="block text-sm text-ink-500">1 oy</span>
                            <span class="bx-price text-ink-900 dark:text-white">{{ \App\Support\Format::money($monthly) }}</span>
                            <span class="mt-1 block text-xs text-ink-500">Istalgan paytda uzaytirasiz</span>
                        </label>
                        <label class="bx-plan" :class="{ 'is-on': plan === 'yearly' }" data-bxtilt data-bxtilt-max="7">
                            <input type="radio" name="plan" value="yearly" x-model="plan" class="sr-only">
                            @if ($savePct > 0)<span class="bx-best">Foydali</span>@endif
                            <span class="bx-tick"><x-icon name="check" class="h-4 w-4" /></span>
                            <span class="block font-semibold text-ink-900 dark:text-white">Yillik</span>
                            <span class="block text-sm text-ink-500">12 oy · oyiga ≈ {{ \App\Support\Format::money($perMonth) }}</span>
                            <span class="bx-price text-ink-900 dark:text-white">{{ \App\Support\Format::money($yearly) }}</span>
                            @if ($savePct > 0)<span class="bx-save"><x-icon name="sparkles" class="h-3.5 w-3.5" /> {{ \App\Support\Format::money($monthly * 12 - $yearly) }} tejaysiz (−{{ $savePct }}%)</span>@endif
                        </label>
                    </div>
                    @error('plan')<p class="error-text">{{ $message }}</p>@enderror

                    <div class="bx-sum">
                        <div>
                            <div class="bx-eyebrow">To'lanadigan summa</div>
                            <div class="bx-amount text-ink-900 dark:text-white" x-text="money(shown)">{{ \App\Support\Format::money($monthly) }}</div>
                        </div>
                        <div>
                            <div class="bx-eyebrow">Yangi muddat</div>
                            <div class="bx-until text-ink-900 dark:text-white"><span x-text="until">—</span> gacha</div>
                            <div class="bx-sub text-ink-600 dark:text-ink-300">{{ $expired ? 'Tasdiqlangan kundan hisoblanadi (taxminan)' : 'Hozirgi muddat tugagandan keyin' }}</div>
                        </div>
                        <button type="button" class="bx-mini" @click="copyAmount()"><x-icon name="copy" class="h-4 w-4" /><span x-text="amtCopied ? 'Nusxalandi' : 'Summani nusxalash'">Summani nusxalash</span></button>
                    </div>
                </div>

                {{-- 2. Karta (faqat mobil; kompyuterda o'ng ustunda) --}}
                <div class="lg:hidden">
                    <div class="label">Kartaga o'tkazing</div>
                    @include('subscriptions._pay_card', ['cardPretty' => $cardPretty, 'holder' => $holder, 'branchName' => $branch->name])
                </div>

                {{-- 3. Chek --}}
                <div>
                    <div class="label">To'lov cheki (rasm)</div>
                    <label class="bx-drop" :class="{ 'is-drag': drag }" x-show="!file" @dragover.prevent="drag = true" @dragleave.prevent="drag = false" @drop.prevent="drop($event)">
                        <span class="bx-up-ic"><x-icon name="upload" class="h-7 w-7" /></span>
                        <span class="block font-semibold text-ink-900 dark:text-white">Chek rasmini bu yerga tashlang</span>
                        <span class="mt-0.5 block text-sm text-ink-500">yoki bosib tanlang</span>
                        <input id="receipt" type="file" name="receipt" accept="image/png,image/jpeg,image/webp" required x-ref="file" @change="onChange($event)" class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                    </label>
                    <template x-if="file">
                        <div class="bx-file">
                            <img :src="preview" alt="Chek" class="bx-thumb">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-semibold text-ink-900 dark:text-white" x-text="file.name"></div>
                                <div class="text-xs text-ink-500" x-text="size(file.size)"></div>
                                <div class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600"><x-icon name="check" class="h-3.5 w-3.5" /> Tayyor</div>
                            </div>
                            <button type="button" class="btn-secondary shrink-0" @click="clear()">O'zgartirish</button>
                        </div>
                    </template>
                    <p class="error-text" x-show="ferr" x-text="ferr" x-cloak></p>
                    <p class="hint">JPG, PNG yoki WEBP, 5 MB gacha. Summa va sana ko'rinib turishi kerak.</p>
                    @error('receipt')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="note">Izoh (ixtiyoriy)</label>
                    <input id="note" name="note" maxlength="500" value="{{ old('note') }}" class="input">
                </div>

                <div class="rounded-xl border border-ink-200 bg-ink-50 p-3 text-xs text-ink-600 dark:border-ink-700 dark:bg-ink-800/60 dark:text-ink-300">Chek yuborilgach administrator uni <b>{{ \App\Models\SubscriptionRequest::REVIEW_TIME }}</b> ichida ko'rib chiqadi. Tasdiqlangach obuna avtomatik faollashadi.</div>

                <div><button class="btn-primary bx-submit" :disabled="loading"><span class="bx-spin" x-show="loading" x-cloak></span><span x-text="loading ? 'Yuborilmoqda…' : 'Chekni yuborish'">Chekni yuborish</span></button></div>
            </form>
        </div>

        {{-- O'ng ustun: 3D karta (kompyuter) --}}
        <div class="hidden lg:sticky lg:top-24 lg:block lg:self-start">
            <div class="card card-body bx-rise" style="--d: 3">
                <h3 class="mb-2 font-semibold text-ink-900 dark:text-white">To'lov uchun karta</h3>
                @include('subscriptions._pay_card', ['cardPretty' => $cardPretty, 'holder' => $holder, 'branchName' => $branch->name])
                <p class="mt-4 text-xs text-ink-500">Tanlangan tarif summasini aynan shu kartaga o'tkazing, so'ng chekni yuklang.</p>
                @unless ($exempt)
                    <p class="mt-2 text-xs text-ink-500">
                        @if ($expired)Obuna <b>{{ $lastDay?->format('d.m.Y') }}</b> kuni tugagan. Yangi muddat to'lov tasdiqlangan kundan boshlanadi.
                        @else Hozirgi obuna <b>{{ $lastDay?->format('d.m.Y') }}</b> kunigacha amal qiladi. Yangi muddat shundan keyin boshlanadi (qolgan kunlar yo'qolmaydi).
                        @endif</p>
                @endunless
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bxBuy', (c) => ({
        plan: c.plan, prices: c.prices, shown: c.prices[c.plan], raf: 0,
        touched: false, copied: false, amtCopied: false, file: null, preview: '', ferr: '', drag: false, loading: false,
        init() { this.$watch('plan', () => this.tweenTo(this.prices[this.plan])); },
        get s2() { return this.touched || !!this.file; },
        get cur() { return !this.s2 ? 2 : (!this.file ? 3 : 4); },
        get until() {
            const [y, m, d] = c.from.split('-').map(Number);
            const tm = (m - 1) + (this.plan === 'yearly' ? 12 : 1);
            const ty = y + Math.floor(tm / 12), mm = tm % 12;
            const dim = new Date(Date.UTC(ty, mm + 1, 0)).getUTCDate();
            const end = new Date(Date.UTC(ty, mm, Math.min(d, dim)));
            end.setUTCDate(end.getUTCDate() - 1);
            const p = (n) => String(n).padStart(2, '0');
            return p(end.getUTCDate()) + '.' + p(end.getUTCMonth() + 1) + '.' + end.getUTCFullYear();
        },
        money(n) { return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + " so'm"; },
        tweenTo(to) {
            cancelAnimationFrame(this.raf);
            if (matchMedia('(prefers-reduced-motion: reduce)').matches) { this.shown = to; return; }
            const from = this.shown, t0 = performance.now(), dur = 450;
            const step = (t) => {
                const p = Math.min(1, (t - t0) / dur);
                this.shown = p < 1 ? from + (to - from) * (1 - Math.pow(1 - p, 3)) : to;
                if (p < 1) this.raf = requestAnimationFrame(step);
            };
            this.raf = requestAnimationFrame(step);
        },
        async copyText(t) {
            try { await navigator.clipboard.writeText(t); return true; } catch (e) {
                const a = document.createElement('textarea'); a.value = t; a.style.cssText = 'position:fixed;opacity:0';
                document.body.appendChild(a); a.select(); let ok = false;
                try { ok = document.execCommand('copy'); } catch (_) {}
                a.remove(); return ok;
            }
        },
        async copyCard() { if (await this.copyText(c.card)) { this.touched = true; this.copied = true; setTimeout(() => this.copied = false, 2200); } },
        async copyAmount() { if (await this.copyText(String(this.prices[this.plan]))) { this.touched = true; this.amtCopied = true; setTimeout(() => this.amtCopied = false, 2200); } },
        size(b) { return b > 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB'; },
        clear() { if (this.preview) URL.revokeObjectURL(this.preview); this.preview = ''; this.file = null; if (this.$refs.file) this.$refs.file.value = ''; },
        pick(f) {
            this.ferr = '';
            if (!f) { this.clear(); return; }
            if (!['image/png', 'image/jpeg', 'image/webp'].includes(f.type)) { this.clear(); this.ferr = 'Faqat JPG, PNG yoki WEBP rasm yuklang.'; return; }
            if (f.size > 5 * 1024 * 1024) { this.clear(); this.ferr = 'Rasm 5 MB dan oshmasligi kerak.'; return; }
            if (this.preview) URL.revokeObjectURL(this.preview);
            this.file = { name: f.name, size: f.size }; this.preview = URL.createObjectURL(f);
        },
        onChange(e) { this.pick(e.target.files[0]); },
        drop(e) {
            this.drag = false;
            const f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            if (!f) return;
            try { const dt = new DataTransfer(); dt.items.add(f); this.$refs.file.files = dt.files; } catch (_) {}
            this.pick(f);
        },
    }));
});

(function () {
    const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Qolgan kun sanog'i
    document.querySelectorAll('[data-bx-count]').forEach((el) => {
        const to = parseInt(el.dataset.bxCount, 10);
        if (calm || !(to > 0)) return;
        const t0 = performance.now(), dur = 1100;
        const tick = (t) => { const p = Math.min(1, (t - t0) / dur); el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(tick); };
        el.textContent = '0'; requestAnimationFrame(tick);
    });

    if (calm || !matchMedia('(hover: hover) and (pointer: fine)').matches) return;

    // Kartochkalarni sichqoncha ortidan qiyshaytirish
    document.querySelectorAll('[data-bxtilt]').forEach((el) => {
        const max = parseFloat(el.getAttribute('data-bxtilt-max')) || 7;
        let raf = 0;
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect(), px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
            cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
                el.style.setProperty('--ry', ((px - .5) * 2 * max).toFixed(2) + 'deg');
                el.style.setProperty('--rx', (-(py - .5) * 2 * max).toFixed(2) + 'deg');
                el.style.setProperty('--gx', (px * 100).toFixed(1) + '%');
                el.style.setProperty('--gy', (py * 100).toFixed(1) + '%');
            });
        });
        el.addEventListener('pointerleave', () => {
            cancelAnimationFrame(raf);
            ['--rx', '--ry', '--gx', '--gy'].forEach((k) => el.style.removeProperty(k));
        });
    });

    // Hero 3D sahnasi: sichqoncha parallaksi
    document.querySelectorAll('.bx-hero').forEach((hero) => {
        const world = hero.querySelector('.bx-world');
        if (!world) return;
        let tx = 0, ty = 0, cx = 0, cy = 0, run = false;
        const loop = () => {
            cx += (tx - cx) * .08; cy += (ty - cy) * .08;
            world.style.setProperty('--wx', cx.toFixed(2) + 'deg'); world.style.setProperty('--wy', cy.toFixed(2) + 'deg');
            if (Math.abs(tx - cx) > .02 || Math.abs(ty - cy) > .02) requestAnimationFrame(loop); else run = false;
        };
        const go = () => { if (!run) { run = true; requestAnimationFrame(loop); } };
        hero.addEventListener('pointermove', (e) => {
            const r = hero.getBoundingClientRect();
            ty = ((e.clientX - r.left) / r.width - .5) * 26; tx = -((e.clientY - r.top) / r.height - .5) * 16; go();
        });
        hero.addEventListener('pointerleave', () => { tx = 0; ty = 0; go(); });
    });
})();
</script>
@endpush
