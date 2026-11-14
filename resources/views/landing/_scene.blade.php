{{-- Hikoya sahnasi mock'i (faqat bezak). $n: 1..5; $chip: belgi matni --}}
@switch($n)
    @case(1)
        <div class="space-y-3">
            <div class="flex items-center gap-3 rounded-2xl bg-white p-3 shadow-card ring-1 ring-ink-100 dark:bg-ink-800 dark:ring-ink-700">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300"><x-icon name="send" class="h-5 w-5" /></span>
                <div class="min-w-0 flex-1"><div class="h-2.5 w-28 rounded-full bg-ink-200 dark:bg-ink-600"></div><div class="mt-2 h-2 w-16 rounded-full bg-ink-100 dark:bg-ink-700"></div></div>
                <span class="sc-pop rounded-full bg-brand-600 px-3 py-1 text-xs font-bold text-white">{{ $chip }}</span>
            </div>
            @foreach ([['megaphone', 'accent', 70], ['phone', 'brand', 48]] as [$ic, $tone, $w])
                <div class="sc-slide flex items-center gap-3 rounded-2xl bg-white/70 p-3 ring-1 ring-ink-100 dark:bg-ink-800/60 dark:ring-ink-700" style="--i: {{ $loop->iteration }}">
                    <span class="grid h-10 w-10 place-items-center rounded-xl {{ $tone === 'accent' ? 'bg-accent-100 text-accent-600 dark:bg-accent-500/15 dark:text-accent-300' : 'bg-brand-100 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300' }}"><x-icon :name="$ic" class="h-5 w-5" /></span>
                    <div class="flex-1"><div class="h-2.5 rounded-full bg-ink-200 dark:bg-ink-600" style="width: {{ $w }}%"></div><div class="mt-2 h-2 w-12 rounded-full bg-ink-100 dark:bg-ink-700"></div></div>
                </div>
            @endforeach
            <div class="flex gap-2 pt-1"><span class="h-9 flex-1 rounded-xl bg-brand-600 shadow-glow"></span><span class="h-9 w-24 rounded-xl bg-ink-100 dark:bg-ink-700"></span></div>
        </div>
        @break
    @case(2)
        <div class="rounded-2xl bg-white p-4 shadow-card ring-1 ring-ink-100 dark:bg-ink-800 dark:ring-ink-700">
            <div class="mb-3 flex items-center justify-between"><div class="h-3 w-28 rounded-full bg-ink-200 dark:bg-ink-600"></div><span class="sc-pop rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ $chip }}</span></div>
            <div class="flex -space-x-2">
                @foreach ([['from-brand-400','to-accent-400'],['from-accent-300','to-brand-500'],['from-sky-400','to-brand-500'],['from-emerald-400','to-sky-400'],['from-brand-300','to-accent-300']] as $g)
                    <span class="sc-pop h-11 w-11 rounded-full bg-gradient-to-br {{ $g[0] }} {{ $g[1] }} ring-2 ring-white dark:ring-ink-800" style="--i: {{ $loop->iteration }}"></span>
                @endforeach
                <span class="sc-pop grid h-11 w-11 place-items-center rounded-full bg-brand-50 text-brand-600 ring-2 ring-white dark:bg-brand-500/20 dark:text-brand-300 dark:ring-ink-800" style="--i: 6"><x-icon name="plus" class="h-5 w-5" /></span>
            </div>
            <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-700"><span class="sc-fill block h-full rounded-full bg-gradient-to-r from-brand-500 to-accent-400" style="--w: 80%"></span></div>
            <div class="mt-4 grid grid-cols-3 gap-2">@foreach ([1,2,3] as $k)<div class="h-14 rounded-xl bg-ink-50 dark:bg-ink-700/60"></div>@endforeach</div>
        </div>
        @break
    @case(3)
        <div class="rounded-2xl bg-white p-4 shadow-card ring-1 ring-ink-100 dark:bg-ink-800 dark:ring-ink-700">
            <div class="mb-3 flex items-center justify-between"><div class="h-3 w-24 rounded-full bg-ink-200 dark:bg-ink-600"></div><span class="sc-pop rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">{{ $chip }}</span></div>
            <div class="grid grid-cols-7 gap-1.5">
                @foreach (range(1, 21) as $d)
                    @php $miss = in_array($d, [5, 12, 16]); @endphp
                    <span class="sc-pop grid h-9 place-items-center rounded-lg {{ $miss ? 'bg-rose-100 text-rose-500 dark:bg-rose-500/15' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300' }}" style="--i: {{ $d * 0.35 }}"><x-icon :name="$miss ? 'x' : 'check'" class="h-4 w-4" /></span>
                @endforeach
            </div>
        </div>
        @break
    @case(4)
        <div class="space-y-3">
            <div class="rounded-2xl bg-white p-4 shadow-card ring-1 ring-ink-100 dark:bg-ink-800 dark:ring-ink-700">
                <div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-xl bg-accent-100 text-accent-600 dark:bg-accent-500/15 dark:text-accent-300"><x-icon name="banknote" class="h-5 w-5" /></span><div class="flex-1"><div class="h-3 w-24 rounded-full bg-ink-200 dark:bg-ink-600"></div><div class="mt-2 h-4 w-32 rounded-full bg-accent-300 dark:bg-accent-500/60"></div></div><span class="sc-pop grid h-9 w-9 place-items-center rounded-full bg-emerald-500 text-white shadow-lg"><x-icon name="check" class="h-5 w-5" /></span></div>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-ink-100 dark:bg-ink-700"><span class="sc-fill block h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500" style="--w: 100%"></span></div>
                <div class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-300">{{ $chip }}</div>
            </div>
            <div class="sc-slide flex items-center gap-3 rounded-2xl bg-sky-50 p-3 ring-1 ring-sky-100 dark:bg-sky-500/10 dark:ring-sky-500/20" style="--i: 2"><span class="grid h-9 w-9 place-items-center rounded-full bg-sky-500 text-white"><x-icon name="send" class="h-4 w-4" /></span><div class="flex-1"><div class="h-2.5 w-36 rounded-full bg-sky-200 dark:bg-sky-500/40"></div><div class="mt-1.5 h-2 w-20 rounded-full bg-sky-100 dark:bg-sky-500/20"></div></div></div>
        </div>
        @break
    @default
        <div class="rounded-2xl bg-white p-4 shadow-card ring-1 ring-ink-100 dark:bg-ink-800 dark:ring-ink-700">
            <div class="mb-3 flex items-center justify-between"><div class="h-3 w-24 rounded-full bg-ink-200 dark:bg-ink-600"></div><span class="sc-pop rounded-full bg-accent-100 px-3 py-1 text-xs font-bold text-accent-700 dark:bg-accent-500/20 dark:text-accent-300">{{ $chip }}</span></div>
            <div class="flex h-32 items-end gap-2">
                @foreach ([38, 55, 46, 72, 60, 88, 76, 95] as $h)
                    <span class="sc-bar flex-1 rounded-t-lg bg-gradient-to-t from-brand-600 to-accent-400" style="--h: {{ $h }}%; --i: {{ $loop->index }}"></span>
                @endforeach
            </div>
            <div class="mt-3 grid grid-cols-3 gap-2">@foreach ([1,2,3] as $k)<div class="rounded-xl bg-ink-50 p-2 dark:bg-ink-700/60"><div class="h-2 w-10 rounded-full bg-ink-200 dark:bg-ink-600"></div><div class="mt-1.5 h-3 w-14 rounded-full bg-brand-300 dark:bg-brand-500/60"></div></div>@endforeach</div>
        </div>
@endswitch
