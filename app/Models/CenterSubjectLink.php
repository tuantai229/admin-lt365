<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CenterSubjectLink extends Pivot
{
    protected $table = 'center_subject_links';
    public $timestamps = false;

    protected $fillable = [
        'center_id',
        'subject_id',
    ];

    protected $casts = [
        'center_id' => 'integer',
        'subject_id' => 'integer',
    ];
}
