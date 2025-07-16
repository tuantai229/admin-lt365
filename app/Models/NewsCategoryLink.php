<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class NewsCategoryLink extends Pivot
{
    protected $table = 'news_category_links';
    public $timestamps = false;

    protected $fillable = [
        'news_id',
        'category_id',
    ];

    protected $casts = [
        'news_id' => 'integer',
        'category_id' => 'integer',
    ];
}