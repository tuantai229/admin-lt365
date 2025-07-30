<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MetaSeo extends Model
{
    protected $table = 'meta_seo';
    
    protected $fillable = [
        'type', 'type_id', 'meta_title', 'meta_description', 
        'meta_keywords', 'meta_robots', 'og_image_id', 'status'
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo(null, 'type', 'type_id');
    }
}
