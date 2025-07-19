<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Document;
use App\Models\Level;
use App\Models\Subject;
use App\Models\DocumentType;
use App\Models\DifficultyLevel;
use App\Models\AdminUser;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Document::truncate();

        $levels = Level::pluck('id')->toArray();
        $subjects = Subject::pluck('id')->toArray();
        $documentTypes = DocumentType::pluck('id')->toArray();
        $difficultyLevels = DifficultyLevel::pluck('id')->toArray();
        $adminUsers = AdminUser::pluck('id')->toArray();

        if (empty($levels) || empty($subjects) || empty($documentTypes) || empty($difficultyLevels) || empty($adminUsers)) {
            $this->command->info('Please seed the related tables (Level, Subject, DocumentType, DifficultyLevel, AdminUser) first.');
            return;
        }

        for ($i = 1; $i <= 20; $i++) {
            $name = 'Tài liệu chuyên đề ' . $i;
            Document::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'featured_image_id' => null,
                'description' => 'Mô tả cho ' . $name,
                'content' => 'Nội dung chi tiết cho ' . $name,
                'file_path' => 'documents/sample.pdf',
                'file_size' => rand(1024, 51200), // 1MB to 50MB
                'file_type' => 'application/pdf',
                'price' => rand(0, 5) * 10000, // Price from 0 to 50,000
                'download_count' => rand(0, 1000),
                'level_id' => $levels[array_rand($levels)],
                'subject_id' => $subjects[array_rand($subjects)],
                'document_type_id' => $documentTypes[array_rand($documentTypes)],
                'difficulty_level_id' => $difficultyLevels[array_rand($difficultyLevels)],
                'school_id' => 0, // Assuming no schools or optional
                'year' => rand(2020, 2025),
                'is_featured' => rand(0, 1),
                'status' => 1, // 1 = Published
                'sort_order' => $i,
                'admin_user_id' => $adminUsers[array_rand($adminUsers)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
