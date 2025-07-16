<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class NewsTag extends Pivot
{
    protected $table = 'news_tags';
    public $timestamps = false;

    protected $fillable = [
        'news_id',
        'tag_id',
    ];

    protected $casts = [
        'news_id' => 'integer',
        'tag_id' => 'integer',
    ];
}