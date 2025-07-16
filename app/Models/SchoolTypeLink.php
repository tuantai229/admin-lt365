<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolTypeLink extends Pivot
{
    protected $table = 'school_type_links';
    public $timestamps = false;

    protected $fillable = [
        'school_id',
        'school_type_id',
    ];

    protected $casts = [
        'school_id' => 'integer',
        'school_type_id' => 'integer',
    ];
}