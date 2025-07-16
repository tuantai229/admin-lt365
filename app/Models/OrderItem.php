<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'document_id',
        'price',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'document_id' => 'integer',
        'price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}