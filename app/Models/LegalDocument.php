<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    protected $fillable = ['type', 'title', 'body', 'version', 'published_at', 'updated_by'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'version' => 'integer'];
    }
}
