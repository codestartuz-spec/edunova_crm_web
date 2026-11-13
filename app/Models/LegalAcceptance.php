<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalAcceptance extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'user_name', 'user_role', 'document_type', 'version', 'document_title', 'ip', 'user_agent', 'accepted_at'];

    protected function casts(): array
    {
        return ['accepted_at' => 'datetime', 'version' => 'integer'];
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }
}
