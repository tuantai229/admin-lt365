<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            RolePermissionSeeder::class,
            UserPermissionsSeeder::class,
            LevelSeeder::class,
            SubjectSeeder::class,
            DocumentTypeSeeder::class,
            DifficultyLevelSeeder::class,
            SchoolTypeSeeder::class,
            NewsCategorySeeder::class,
            NewsSeeder::class,
            PageSeeder::class,
            ContactSeeder::class,
            NewsletterSeeder::class,
            UserSeeder::class,
            CommentSeeder::class,
            OrderSeeder::class,
            DocumentSeeder::class,
            SchoolSeeder::class,
            TeacherSeeder::class,
            CenterSeeder::class,
            HomeSettingsSeeder::class,
        ]);
    }
}
