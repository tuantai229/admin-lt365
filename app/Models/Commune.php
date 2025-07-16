<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commune extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'name',
        'slug',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'province_id' => 'integer',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    public function centers(): HasMany
    {
        return $this->hasMany(Center::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}