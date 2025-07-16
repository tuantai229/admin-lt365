<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolAdmission extends Model
{
    protected $fillable = [
        'school_id',
        'year',
        'total_students',
        'number_of_classes',
        'students_per_class',
        'estimated_tuition_fee',
        'program_type',
        'register_start_date',
        'register_end_date',
        'exam_date',
        'result_announcement_date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
