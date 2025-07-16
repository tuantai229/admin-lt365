<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolAdmissionMethod extends Model
{
    protected $fillable = [
        'school_id',
        'method_name',
        'duration_minutes',
        'sort_order',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
