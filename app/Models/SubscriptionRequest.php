<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** v22: filial obunasini sotib olish/uzaytirish so'rovi (chek rasmi bilan). Filial filtri YO'Q: sAdmin hammasini ko'radi. */
class SubscriptionRequest extends Model
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public const PLANS = ['monthly' => 'Oylik', 'yearly' => 'Yillik'];

    protected $fillable = ['branch_id', 'user_id', 'plan', 'amount', 'receipt_path', 'status', 'note', 'reject_reason', 'reviewed_by', 'reviewed_at', 'new_ends_on', 'pending_branch_id'];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'reviewed_at' => 'datetime', 'new_ends_on' => 'date'];
    }

    /** Ko'rib chiqish muddati (direktorga ko'rsatiladi). */
    public const REVIEW_TIME = '4–12 soat';

    protected static function booted(): void
    {
        // Bazadagi unikal kalit: bir filial uchun faqat bitta kutayotgan chek
        static::saving(function (self $r) {
            $r->pending_branch_id = $r->status === self::PENDING ? $r->branch_id : null;
        });

        $forget = fn () => \Illuminate\Support\Facades\Cache::forget('subscription.pending_count');
        static::saved($forget);
        static::deleted($forget);
    }

    public static function pendingCount(): int
    {
        return (int) \Illuminate\Support\Facades\Cache::remember('subscription.pending_count', 60, fn () => static::where('status', self::PENDING)->count());
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withoutGlobalScopes();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withoutGlobalScopes();
    }

    public function planLabel(): string
    {
        return self::PLANS[$this->plan] ?? $this->plan;
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }
}
