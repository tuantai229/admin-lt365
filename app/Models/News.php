<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Awcodes\Curator\Models\Media;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'school_id',
        'name',
        'slug',
        'featured_image_id',
        'content',
        'view_count',
        'is_featured',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'admin_user_id' => 'integer',
        'school_id' => 'integer',
        'featured_image_id' => 'integer',
        'view_count' => 'integer',
        'is_featured' => 'boolean',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'news_category_links', 'news_id', 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_tags');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(UserFavorite::class, 'favoritable', 'type', 'type_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable', 'type', 'type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
