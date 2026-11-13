<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** v14: parolni tiklashga urinishlar jurnali (kim, qaysi raqam, qaysi kanal, qachon, IP). */
class PasswordResetLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'phone_entered', 'channel', 'event', 'ip', 'user_agent', 'note', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $event, ?User $user, ?string $phone, ?string $channel = null, ?string $note = null): self
    {
        $req = request();

        return static::create([
            'user_id' => $user?->id,
            'phone_entered' => $phone ? mb_substr($phone, 0, 40) : null,
            'channel' => $channel,
            'event' => $event,
            'ip' => $req?->ip(),
            'user_agent' => $req ? mb_substr((string) $req->userAgent(), 0, 255) : null,
            'note' => $note ? mb_substr($note, 0, 255) : null,
            'created_at' => now(),
        ]);
    }
}
