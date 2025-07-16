<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeacherLevelLink extends Pivot
{
    protected $table = 'teacher_level_links';
    public $timestamps = false;

    protected $fillable = [
        'teacher_id',
        'level_id',
    ];

    protected $casts = [
        'teacher_id' => 'integer',
        'level_id' => 'integer',
    ];
}