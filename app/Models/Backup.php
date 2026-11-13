<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = ['status', 'trigger', 'file', 'size', 'sha256', 'duration_ms', 'telegram', 'error', 'started_by'];

    protected function casts(): array
    {
        return ['telegram' => 'array'];
    }
}
