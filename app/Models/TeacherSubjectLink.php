<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeacherSubjectLink extends Pivot
{
    protected $table = 'teacher_subject_links';
    public $timestamps = false;

    protected $fillable = [
        'teacher_id',
        'subject_id',
    ];

    protected $casts = [
        'teacher_id' => 'integer',
        'subject_id' => 'integer',
    ];
}