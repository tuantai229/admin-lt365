<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'phone',
        'full_name',
        'avatar',
        'date_of_birth',
        'gender',
        'address',
        'bio',
        'status',
        'last_login_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'status' => 'integer',
        ];
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto update last_login_at when user authenticates
        static::created(function ($user) {
            $user->email_verified_at = now();
            $user->save();
        });
    }

    /**
     * Relationships
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(UserDownload::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Accessors & Mutators
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Default avatar based on gender
        $default = match($this->gender) {
            'female' => 'default-avatar-female.png',
            'male' => 'default-avatar-male.png',
            default => 'default-avatar.png'
        };
        
        return asset('images/' . $default);
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            1 => 'Hoạt động',
            0 => 'Không hoạt động',
            default => 'Không xác định'
        };
    }

    public function getGenderTextAttribute()
    {
        return match($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            default => 'Chưa xác định'
        };
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        return now()->diffInYears($this->date_of_birth);
    }

    /**
     * Helper methods
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function activate(): void
    {
        $this->update(['status' => 1]);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 0]);
    }
}