<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array'
    ];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value): self
    {
        $setting = static::updateOrCreate(['key' => $key], ['value' => $value]);
        
        // Clear cache
        Cache::forget("setting_{$key}");
        
        return $setting;
    }

    /**
     * Get general settings
     */
    public static function getGeneralSettings(): array
    {
        return static::get('general_settings', [
            'domain' => '',
            'hotline' => '',
            'email' => '',
            'address' => '',
            'working_hours' => '',
            'footer_intro' => '',
            'facebook' => '',
            'youtube' => '',
            'instagram' => '',
            'tiktok' => '',
        ]);
    }

    /**
     * Get menu navigation
     */
    public static function getMainNavigation(): array
    {
        return static::get('main_navigation', []);
    }

    /**
     * Get footer category links
     */
    public static function getFooterCategoryLinks(): array
    {
        return static::get('footer_category_links', []);
    }

    /**
     * Get footer support links
     */
    public static function getFooterSupportLinks(): array
    {
        return static::get('footer_support_links', []);
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $keys = ['general_settings', 'main_navigation', 'footer_category_links', 'footer_support_links'];
        
        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }
    }
}