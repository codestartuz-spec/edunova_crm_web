<!DOCTYPE html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bosh sahifa') · {{ \App\Support\Brand::name() }}</title>
    <link rel="icon" href="{{ \App\Support\Brand::faviconUrl() }}">
    <script>try{if(localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}}catch(e){}</script>
    @include('partials.fx-head')
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($brandCss = \App\Support\BrandTheme::css())<style id="brand-theme">{!! $brandCss !!}</style>@endif
</head>
<body class="h-full" x-data="{ sidebar: false }" @keydown.escape.window="sidebar = false" data-fx-mode="{{ \App\Support\Edu3D::current()['mode'] }}">
@include('partials.edu-bg', ['scene' => \App\Support\Edu3D::current()['scene']])
@php
    $me = auth()->user();
    $branches = $me->isGlobal() ? \App\Models\Branch::orderBy('name')->get(['id', 'name', 'status', 'subscription_ends_on', 'subscription_exempt']) : collect();
    // v13: qo'shimcha filialli operator uchun ham filial tanlagich (faqat o'ziga ruxsat etilgan filiallar)
    $multiBranch = ! $me->isGlobal() && $me->role === \App\Enums\Role::Operator && \App\Support\BranchContext::extraBranchIds($me) !== [];
    if ($multiBranch) {
        $branches = \App\Models\Branch::whereIn('id', \App\Support\BranchContext::accessibleBranchIds($me))->orderBy('name')->get(['id', 'name', 'status']);
    }
    $restricted = \App\Support\BranchContext::restrictedIds($me);
    $selectedBranch = \App\Support\BranchContext::id();
    $activeBranchName = $selectedBranch ? ($branches->firstWhere('id', $selectedBranch)?->name ?? $me->branch?->name) : null;
@endphp

{{-- Mobil menyu fon qatlami --}}
<div x-show="sidebar" x-transition.opacity x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-ink-950/55 backdrop-blur-sm lg:hidden"></div>

<aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="app-sidebar fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-ink-200/60 bg-white/90 shadow-pop backdrop-blur-xl transition-transform duration-300 ease-out dark:border-ink-800 dark:bg-ink-900/95 lg:shadow-none">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-44 bg-gradient-to-b from-brand-500/10 via-accent-400/5 to-transparent"></div>
    <div class="relative flex h-16 shrink-0 items-center gap-3 px-5">
        <x-logo />
        <div class="min-w-0 leading-tight">
            <div class="text-base font-extrabold tracking-tight text-ink-900 dark:text-white">{{ \App\Support\Brand::short() }}</div>
            <div class="max-w-[10.5rem] truncate text-[10px] font-bold uppercase tracking-[.18em] text-brand-600/70 dark:text-brand-300/70" title="{{ $activeBranchName }}">{{ $activeBranchName ?? 'CRM tizimi' }}</div>
        </div>
        <button class="btn-ghost ml-auto p-1.5 lg:hidden" @click="sidebar = false" aria-label="Yopish"><x-icon name="x" /></button>
    </div>

    <nav class="relative flex-1 space-y-1 overflow-y-auto px-3 py-3">
        @if ($me->isStudent())
            @include('partials.student-nav')
        @else
            @include('partials.sidebar-nav', ['me' => $me])
        @endif
    </nav>
    <div class="relative border-t border-ink-100 p-3 dark:border-ink-800">
        <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-2xl p-2 transition hover:bg-brand-50/70 dark:hover:bg-ink-800">
            <span class="brand-chip flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold">{{ mb_strtoupper(mb_substr($me->name, 0, 1)) }}</span>
            <span class="min-w-0 flex-1 leading-tight">
                <span class="block truncate text-sm font-bold text-ink-900 dark:text-white">{{ $me->name }}</span>
                <span class="block text-xs font-medium text-ink-500">{{ $me->role->label() }}</span>
            </span>
            <x-icon name="cog" class="h-4 w-4 text-ink-400 transition group-hover:text-brand-600" />
        </a>
    </div>
</aside>

<div class="lg:pl-72">
    <header class="app-topbar sticky top-0 z-20 flex h-16 items-center gap-2 border-b border-ink-200/60 bg-white/75 px-3 backdrop-blur-xl dark:border-ink-800 dark:bg-ink-900/75 sm:gap-3 sm:px-6">
        <span class="topbar-line"></span>
        <button class="btn-ghost -ml-1 p-2 lg:hidden" @click="sidebar = true" aria-label="Menyu"><x-icon name="menu" colored /></button>
        <a href="{{ route('dashboard') }}" class="hidden sm:block lg:hidden"><x-logo class="h-8 w-8 text-sm" /></a>

        <div class="flex-1"></div>

        @if ($me->isGlobal() || $multiBranch)
            <form method="POST" action="{{ route('branches.switch') }}" class="flex shrink-0 items-center gap-2">
                @csrf
                <x-icon name="building" colored class="hidden h-5 w-5 sm:block" />
                <select name="branch_id" onchange="this.form.submit()" class="input w-28 truncate py-2 pr-8 sm:w-56" aria-label="Filial">
                    @if ($me->isGlobal() && ($restricted === null || count($restricted) !== 1))<option value="">Barcha filiallar</option>@endif
                    @foreach ($branches as $b)
                        <option value="{{ $b->id }}" @selected($selectedBranch === $b->id)>{{ $b->name }}@if (! $b->isActive()) (arxiv)@endif</option>
                    @endforeach
                </select>
            </form>
        @else
            <span class="badge-brand hidden sm:inline-flex"><x-icon name="building" class="h-3.5 w-3.5" /> {{ $me->branch?->name }}</span>
        @endif

        @unless ($me->isStudent())
            <x-notif-bell />
        @endunless

        @if ($me->can('students.notes'))
            <x-notes-bell :count="\App\Models\StudentNote::active()->count()" />
        @endif

        <button class="btn-ghost relative h-10 w-10 shrink-0 !p-0" @click="$store.theme.toggle()" aria-label="Rejimni almashtirish" title="Yorug' / tungi rejim">
            <x-icon name="moon" colored class="no-ic-anim absolute left-2.5 top-2.5 h-5 w-5 transition duration-500" ::class="$store.theme.dark ? 'rotate-90 scale-0 opacity-0' : ''" />
            <x-icon name="sun" colored class="no-ic-anim absolute left-2.5 top-2.5 h-5 w-5 transition duration-500" ::class="$store.theme.dark ? '' : '-rotate-90 scale-0 opacity-0'" />
        </button>

        <button type="button" data-pwa-install class="btn-ghost hidden p-2 text-xs font-semibold" title="Ilovani o'rnatish"><x-icon name="download" colored class="h-4 w-4" /> <span class="hidden sm:inline">O'rnatish</span></button>

        <form method="POST" action="{{ route('logout') }}" data-confirm="Hozirgi seansni tugatib, tizimdan chiqasiz." data-confirm-title="Chiqishni tasdiqlaysizmi?" data-confirm-ok="Chiqish">
            @csrf
            <button class="btn-ghost shrink-0 p-2" title="Chiqish" aria-label="Chiqish"><x-icon name="logout" colored /></button>
        </form>
    </header>

    <main class="mx-auto max-w-7xl p-4 pb-28 sm:p-6 sm:pb-28 lg:p-8">
        <x-alert />
        @include('partials.subscription-banner', ['layoutBranches' => $branches])
        @yield('content')
        @include('partials.legal-links', ['class' => 'mt-10 border-t border-ink-100 pt-4 dark:border-ink-800'])
    </main>
</div>

@include('partials.mobile-tabbar', ['me' => $me])
@include('partials.confirm-modal')
@stack('scripts')
<script>
(function () {
    function mark() { document.querySelectorAll('tr[data-href]').forEach(function (r) { r.style.cursor = 'pointer'; }); }
    document.addEventListener('DOMContentLoaded', mark);
    document.addEventListener('click', function (e) {
        var row = e.target.closest && e.target.closest('tr[data-href]');
        if (!row || e.button !== 0) return;
        if (e.target.closest('a, button, input, select, textarea, label, form, summary, [role="button"], [data-no-row]')) return;
        var sel = window.getSelection && window.getSelection().toString();
        if (sel) return;
        var href = row.getAttribute('data-href');
        if (e.ctrlKey || e.metaKey) { window.open(href, '_blank'); } else { window.location.href = href; }
    });
})();
</script>
</body>
</html>
