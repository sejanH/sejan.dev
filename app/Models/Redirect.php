<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_url',
        'target_url',
        'status_code',
        'hits',
        'last_hit_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'hits' => 'integer',
        'last_hit_at' => 'datetime',
    ];

    /**
     * Increment hits counter and record last access time.
     */
    public function recordHit(): void
    {
        $this->increment('hits');
        $this->update(['last_hit_at' => now()]);
    }
}
