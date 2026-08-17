<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Target extends Model
{
    protected $fillable = [
        'name', 'domain', 'ip_address', 'status', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function scanJobs(): HasMany
    {
        return $this->hasMany(ScanJob::class);
    }

    public function vulnerabilities(): HasMany
    {
        return $this->hasMany(Vulnerability::class);
    }
}