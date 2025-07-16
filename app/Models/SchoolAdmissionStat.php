<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolAdmissionStat extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year',
        'target_quota',
        'registered_count',
        'cutoff_score',
        'cutoff_score_max',
        'sort_order',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
