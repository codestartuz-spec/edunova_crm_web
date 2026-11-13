<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Filialning CRM obuna to'lovi (faqat tarix; tizimning boshqa qismlariga ta'sir qilmaydi). Tahrirlanmaydi va o'chirilmaydi. */
class BranchPayment extends Model
{
    public const CASH = 'cash';

    public const CARD = 'card';

    public const METHODS = [self::CASH => 'Naqd', self::CARD => 'Karta'];

    public $timestamps = false;

    protected $fillable = ['branch_id', 'amount', 'method', 'note', 'created_by', 'created_at'];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'created_at' => 'datetime'];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }
}
