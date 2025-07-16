<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'ip_address',
        'downloaded_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'document_id' => 'integer',
        'downloaded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}