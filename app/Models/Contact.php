<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function scopeUnread($query)
    {
        return $query->where('status', 0);
    }

    public function scopeRead($query)
    {
        return $query->where('status', 1);
    }
}