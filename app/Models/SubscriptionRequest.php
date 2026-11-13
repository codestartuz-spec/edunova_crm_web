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

    protected $fillable = ['branch_id', 'user_id', 'plan', 'amount', 'receipt_path', 'status', 'note', 'reject_reason', 'reviewed_by', 'reviewed_at', 'new_ends_on'];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'reviewed_at' => 'datetime', 'new_ends_on' => 'date'];
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
