@php
    $__u = auth()->user();
    $__banner = null;
    if ($__u && ! $__u->isStudent()) {
        if ($__u->isSuperAdmin()) {
            $__n = \App\Models\SubscriptionRequest::where('status', 'pending')->count();
            if ($__n > 0) {
                $__banner = ['tone' => 'amber', 'text' => "Tekshiruvni kutayotgan obuna cheklari: {$__n} ta.", 'href' => route('subscriptions.index'), 'cta' => "Ko'rish"];
            }
        } else {
            $__cands = $__u->isGlobal() ? \App\Models\Branch::query()->active()->get() : \App\Models\Branch::withoutGlobalScopes()->whereKey($__u->branch_id)->get();
            $__cur = \App\Support\BranchContext::id();
            if ($__cur) { $__cands = $__cands->where('id', $__cur); }
            $__exp = $__cands->filter(fn ($b) => $b->subscriptionExpired());
            $__soon = $__cands->filter(fn ($b) => ! $b->subscriptionExpired() && ($d = $b->subscriptionDaysLeft()) !== null && $d <= 7);
            $__href = $__u->isDirector() ? route('subscriptions.index') : null;
            if ($__exp->isNotEmpty()) {
                $__banner = ['tone' => 'red', 'text' => "Obunasi tugagan: {$__exp->pluck('name')->join(', ')}. Faqat ko'rish rejimi — yangi ma'lumot kiritib bo'lmaydi.", 'href' => $__href, 'cta' => 'Obunani uzaytirish'];
            } elseif ($__soon->isNotEmpty() && $__u->isDirector()) {
                $__banner = ['tone' => 'amber', 'text' => 'Obuna tugashiga oz qoldi: '.$__soon->map(fn ($b) => "{$b->name} ({$b->subscriptionDaysLeft()} kun)")->join(', ').'.', 'href' => $__href, 'cta' => 'Uzaytirish'];
            }
        }
    }
@endphp
@if ($__banner)
    <div class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border px-4 py-3 text-sm {{ $__banner['tone'] === 'red' ? 'border-brand-200 bg-brand-50 text-brand-800 dark:border-brand-900 dark:bg-brand-950/50 dark:text-brand-200' : 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200' }}">
        <x-icon name="alert" class="h-5 w-5 shrink-0" />
        <div class="min-w-0 flex-1">{{ $__banner['text'] }}</div>
        @if ($__banner['href'])<a href="{{ $__banner['href'] }}" class="btn-secondary btn-sm">{{ $__banner['cta'] }}</a>@endif
    </div>
@endif
