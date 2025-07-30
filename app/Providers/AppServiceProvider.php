<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'document' => \App\Models\Document::class,
            'school' => \App\Models\School::class,
            'news' => \App\Models\News::class,
            'teacher' => \App\Models\Teacher::class,
            'center' => \App\Models\Center::class,
            'admin_user' => \App\Models\AdminUser::class,
            'user' => \App\Models\User::class,
        ]);
    }
}
