<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCheck extends Model
{
    public $timestamps = false;

    protected $fillable = ['status', 'fail_count', 'warn_count', 'duration_ms', 'results', 'created_at'];

    protected function casts(): array
    {
        return ['results' => 'array', 'created_at' => 'datetime'];
    }
}
