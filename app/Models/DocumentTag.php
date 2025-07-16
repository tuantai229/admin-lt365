<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DocumentTag extends Pivot
{
    protected $table = 'document_tags';
    public $timestamps = false;

    protected $fillable = [
        'document_id',
        'tag_id',
    ];

    protected $casts = [
        'document_id' => 'integer',
        'tag_id' => 'integer',
    ];
}