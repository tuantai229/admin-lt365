<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NewsCategory::class, 'parent_id');
    }

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_category_links', 'category_id', 'news_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeParents($query)
    {
        return $query->where('parent_id', 0);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}