<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'featured_image_id',
        'description',
        'file_url',
        'file_size',
        'file_type',
        'level_id',
        'subject_id',
        'document_type_id',
        'difficulty_level_id',
        'price',
        'is_free',
        'download_count',
        'view_count',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'level_id' => 'integer',
        'subject_id' => 'integer',
        'document_type_id' => 'integer',
        'difficulty_level_id' => 'integer',
        'featured_image_id' => 'integer',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'status' => 'integer',
        'sort_order' => 'integer',
        'file_size' => 'integer',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function difficultyLevel(): BelongsTo
    {
        return $this->belongsTo(DifficultyLevel::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'document_tags');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(UserDownload::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }
}