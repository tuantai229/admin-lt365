<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CenterLevelLink extends Pivot
{
    protected $table = 'center_level_links';
    public $timestamps = false;

    protected $fillable = [
        'center_id',
        'level_id',
    ];

    protected $casts = [
        'center_id' => 'integer',
        'level_id' => 'integer',
    ];
}