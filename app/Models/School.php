<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'featured_image_id',
        'level_id',
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
        'level_id' => 'integer',
        'province_id' => 'integer',
        'commune_id' => 'integer',
        'featured_image_id' => 'integer',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function schoolTypes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolType::class, 'school_type_links');
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
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

    public function admissions(): HasMany
    {
        return $this->hasMany(SchoolAdmission::class);
    }

    public function admissionMethods(): HasMany
    {
        return $this->hasMany(SchoolAdmissionMethod::class);
    }

    public function admissionStats(): HasMany
    {
        return $this->hasMany(SchoolAdmissionStat::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
