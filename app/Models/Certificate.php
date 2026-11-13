<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use BelongsToBranch;

    public const ISSUED = 'issued';
    public const REVOKED = 'revoked';

    protected $fillable = [
        'branch_id', 'student_id', 'group_id', 'number', 'code', 'student_name', 'course_name', 'level_name', 'title', 'body', 'signer_name', 'signer_title', 'issued_on',
        'status', 'revoked_at', 'revoked_by', 'revoke_reason', 'replaced_by_id', 'issued_by',
    ];

    protected function casts(): array
    {
        return ['issued_on' => 'date', 'revoked_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class)->withTrashed();
    }

    public function isRevoked(): bool
    {
        return $this->status === self::REVOKED;
    }
}
