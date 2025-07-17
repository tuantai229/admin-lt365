<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Awcodes\Curator\Models\Media;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'featured_image_id',
        'description',
        'content',
        'file_path',
        'file_size',
        'file_type',
        'price',
        'download_count',
        'level_id',
        'subject_id',
        'document_type_id',
        'difficulty_level_id',
        'school_id',
        'year',
        'is_featured',
        'status',
        'sort_order',
        'admin_user_id',
    ];

    protected $casts = [
        'level_id' => 'integer',
        'subject_id' => 'integer',
        'document_type_id' => 'integer',
        'difficulty_level_id' => 'integer',
        'school_id' => 'integer',
        'featured_image_id' => 'integer',
        'admin_user_id' => 'integer',
        'price' => 'integer', // Lưu dạng integer (VND)
        'download_count' => 'integer',
        'file_size' => 'integer',
        'year' => 'integer',
        'is_featured' => 'boolean',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'status' => 0,
        'sort_order' => 9999,
        'download_count' => 0,
        'price' => 0,
        'is_featured' => false,
        'level_id' => 0,
        'subject_id' => 0,
        'document_type_id' => 0,
        'difficulty_level_id' => 0,
        'school_id' => 0,
        'year' => 0,
    ];

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto detect file info khi tạo mới
        static::creating(function ($document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                $document->file_size = Storage::disk('public')->size($document->file_path);
                $document->file_type = Storage::disk('public')->mimeType($document->file_path);
            }
        });

        // Auto detect file info khi cập nhật
        static::updating(function ($document) {
            if ($document->isDirty('file_path') && $document->file_path && Storage::disk('public')->exists($document->file_path)) {
                $document->file_size = Storage::disk('public')->size($document->file_path);
                $document->file_type = Storage::disk('public')->mimeType($document->file_path);
            }
        });
    }

    /**
     * Relationships
     */
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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
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

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Accessors
     */
    public function getIsFreeAttribute(): bool
    {
        return $this->price == 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price == 0) {
            return 'Miễn phí';
        }
        return number_format($this->price) . ' VND';
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return 'N/A';
        
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', $this);
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            0 => 'Nháp',
            1 => 'Đã xuất bản',
            2 => 'Ẩn',
            default => 'Không xác định'
        };
    }

    /**
     * Mutators
     */
    public function setYearAttribute($value)
    {
        $this->attributes['year'] = $value ?: 0;
    }

    public function setSchoolIdAttribute($value)
    {
        $this->attributes['school_id'] = $value ?: 0;
    }

    public function setLevelIdAttribute($value)
    {
        $this->attributes['level_id'] = $value ?: 0;
    }

    public function setSubjectIdAttribute($value)
    {
        $this->attributes['subject_id'] = $value ?: 0;
    }

    public function setDocumentTypeIdAttribute($value)
    {
        $this->attributes['document_type_id'] = $value ?: 0;
    }

    public function setDifficultyLevelIdAttribute($value)
    {
        $this->attributes['difficulty_level_id'] = $value ?: 0;
    }

    /**
     * Helper methods
     */
    public function hasFile(): bool
    {
        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }
}