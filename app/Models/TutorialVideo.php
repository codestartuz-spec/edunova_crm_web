<?php

namespace App\Models;

use App\Support\MediaEmbed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** v24: tizim haqidagi video dars. Filialga bog'liq EMAS (hamma uchun umumiy), faqat sAdmin boshqaradi. */
class TutorialVideo extends Model
{
    protected $fillable = ['title', 'url', 'category', 'description', 'duration', 'sort_order', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    /** Bo'lim shu foydalanuvchiga ochiqmi: sAdmin doim; boshqalarga faqat kamida bitta faol video bo'lsa. */
    public static function availableTo(?User $user): bool
    {
        if ($user?->isSuperAdmin()) {
            return true;
        }
        if ($user === null) {
            return false;
        }
        $attrs = request()->attributes;   // bir so'rov ichida bitta SQL (statik emas: testlar va Octane uchun xavfsiz)
        if (! $attrs->has('tutorials.has_active')) {
            $attrs->set('tutorials.has_active', static::query()->where('is_active', true)->exists());
        }

        return (bool) $attrs->get('tutorials.has_active');
    }

    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    /** Sahifa ichida ijro etish ma'lumoti: kind (iframe|video), src, label. */
    public function getEmbedAttribute(): array
    {
        return MediaEmbed::resolve($this->url);
    }

    /** Pleyerda avtomatik ishga tushirish uchun manzil (faqat YouTube/Vimeo iframe'ga autoplay qo'shiladi). */
    public function playSrc(): string
    {
        $e = $this->embed;
        if ($e['kind'] === 'iframe' && (str_contains($e['src'], '/embed/') || str_contains($e['src'], 'player.vimeo.com'))) {
            return $e['src'].(str_contains($e['src'], '?') ? '&' : '?').'autoplay=1';
        }

        return $e['src'];
    }
}
