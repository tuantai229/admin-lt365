<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'type_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'type_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoritable(): MorphTo
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
}