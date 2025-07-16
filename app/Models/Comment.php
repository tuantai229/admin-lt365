<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'type_id',
        'parent_id',
        'content',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'type_id' => 'integer',
        'parent_id' => 'integer',
        'status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo('type', 'type', 'type_id');
    }

    // Helper methods để lấy đối tượng theo type
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'type_id')->where('type', 'document');
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'type_id')->where('type', 'news');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'type_id')->where('type', 'school');
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class, 'type_id')->where('type', 'center');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'type_id')->where('type', 'teacher');
    }

    // Scope để lấy comment gốc (không có parent)
    public function scopeParents($query)
    {
        return $query->where('parent_id', 0);
    }

    // Scope để lấy reply
    public function scopeReplies($query)
    {
        return $query->where('parent_id', '>', 0);
    }
}