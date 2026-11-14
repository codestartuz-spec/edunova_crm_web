<?php

namespace App\Models;

use App\Enums\BranchStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Branch extends Model
{
    /** v9: umumiy Edunova rangi (agar filial o'z rangini tanlamagan bo'lsa). */
    public const DEFAULT_BRAND_COLOR = '#5d46e2';

    protected $fillable = [
        'name', 'code', 'phone', 'address', 'charity_percent', 'discount_days_before', 'discount_days_after', 'sms_enabled', 'sms_modem_daily_limit', 'status', 'opened_at', 'closed_at', 'closed_reason', 'subscription_ends_on', 'subscription_exempt',
        'eskiz_email', 'eskiz_password', 'eskiz_from', 'sms_auto_debt', 'sms_auto_absent',
        'stir', 'director_name', 'director_title', 'contract_template',
        'brand_color', 'public_about', 'usluga_from', 'usluga_rates',
    ];

    protected $hidden = ['eskiz_password'];

    protected static function booted(): void
    {
        // v20: ba'zi filiallarga cheklangan direktor boshqa filiallarni umuman ko'rmaydi
        static::addGlobalScope(new \App\Models\Concerns\BranchVisibilityScope);

        // v22: yangi filial sinov muddati (standart 30 kun) bilan ochiladi
        static::creating(function (self $b) {
            if (! $b->subscription_exempt && ! $b->subscription_ends_on) {
                $b->subscription_ends_on = now()->addDays(\App\Services\SubscriptionService::trialDays())->toDateString();
            }
        });
    }

    protected $attributes = ['status' => 'active'];

    protected function casts(): array
    {
        return [
            'status' => BranchStatus::class,
            'subscription_ends_on' => 'date:Y-m-d',
            'subscription_exempt' => 'boolean',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'eskiz_password' => \App\Casts\SafeEncrypted::class,
            'charity_percent' => 'float',
            'usluga_from' => 'date:Y-m-d',
            'usluga_rates' => 'array',
            'sms_enabled' => 'boolean',
            'sms_auto_debt' => 'boolean',
            'sms_auto_absent' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BranchPayment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BranchStatus::Active);
    }

    public function isActive(): bool
    {
        return $this->status === BranchStatus::Active;
    }

    /** v22: obuna tugaganmi (tugash kunidan boshlab). Obunasiz filial hech qachon tugamaydi. */
    public function subscriptionExpired(): bool
    {
        return ! $this->subscription_exempt && $this->subscription_ends_on !== null && $this->subscription_ends_on->startOfDay()->lte(today());
    }

    /** v22: obuna tugashigacha qolgan kun (null — obunasiz; manfiy/0 — tugagan). */
    public function subscriptionDaysLeft(): ?int
    {
        if ($this->subscription_exempt || ! $this->subscription_ends_on) {
            return null;
        }

        return (int) today()->diffInDays($this->subscription_ends_on->startOfDay(), false);
    }

    /** v22: obunasi tugamagan (yoki obunasiz) filiallar — avtomatik SMS va boshqa avtomatik ishlar faqat shularga. */
    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->where('subscription_exempt', true)->orWhereNull('subscription_ends_on')->orWhere('subscription_ends_on', '>', today()->toDateString()));
    }

    /** v22: obunaning OXIRGI faol kuni (tugash sanasidan bir kun oldin): ekranda "shu kunigacha" deb ko'rsatiladi. */
    public function subscriptionLastDay(): ?\Illuminate\Support\Carbon
    {
        return $this->subscription_ends_on?->copy()->subDay();
    }

    public function subscriptionRequests(): HasMany
    {
        return $this->hasMany(SubscriptionRequest::class);
    }

    public function hasOwnSmsAccount(): bool
    {
        return filled($this->eskiz_email) && filled($this->eskiz_password);
    }

    /** v9: ochiq murojaat sahifasida ishlatiladigan rang — filial o'zi tanlagan yoki standart. */
    public function effectiveBrandColor(): string
    {
        return $this->brand_color ?: self::DEFAULT_BRAND_COLOR;
    }

    /** Filial nomidan takrorlanmaydigan kod (slug) yasaydi. */
    public static function makeCode(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'filial';
        $code = $base;
        $i = 2;

        while (static::where('code', $code)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $code = $base.'-'.$i++;
        }

        return $code;
    }
}
