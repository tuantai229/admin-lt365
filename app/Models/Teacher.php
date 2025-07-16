<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'featured_image_id',
        'tagline',
        'experience',
        'address',
        'province_id',
        'commune_id',
        'phone',
        'email',
        'website',
        'content',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'featured_image_id' => 'integer',
        'experience' => 'integer',
        'province_id' => 'integer',
        'commune_id' => 'integer',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject_links');
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'teacher_level_links');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(UserFavorite::class, 'favoritable', 'type', 'type_id');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable', 'type', 'type_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable', 'type', 'type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}