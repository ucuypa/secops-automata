<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScanJob extends Model
{
    protected $fillable = [
        'target_id', 'scan_type', 'status', 'started_at', 'finished_at', 'raw_output', 'error_message'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'raw_output' => 'array',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }

    public function vulnerabilities(): HasMany
    {
        return $this->hasMany(Vulnerability::class);
    }
}