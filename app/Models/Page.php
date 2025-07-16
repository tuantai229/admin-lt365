<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 0);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', 2);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            0 => 'Nháp',
            1 => 'Đã xuất bản',
            2 => 'Đã ẩn',
            default => 'Không xác định',
        };
    }

    public function isPublished()
    {
        return $this->status === 1;
    }

    public function isDraft()
    {
        return $this->status === 0;
    }

    public function isHidden()
    {
        return $this->status === 2;
    }
}
