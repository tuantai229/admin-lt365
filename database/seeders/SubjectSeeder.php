<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $subjects = [
            ['name' => 'Toán học', 'sort_order' => 10],
            ['name' => 'Ngữ văn', 'sort_order' => 20],
            ['name' => 'Tiếng Anh', 'sort_order' => 30],
            ['name' => 'Vật lý', 'sort_order' => 40],
            ['name' => 'Hóa học', 'sort_order' => 50],
            ['name' => 'Sinh học', 'sort_order' => 60],
            ['name' => 'Lịch sử', 'sort_order' => 70],
            ['name' => 'Địa lý', 'sort_order' => 80],
            ['name' => 'Giáo dục công dân', 'sort_order' => 90],
            ['name' => 'Công nghệ', 'sort_order' => 100],
            ['name' => 'Tin học', 'sort_order' => 110],
            ['name' => 'Giáo dục thể chất', 'sort_order' => 120],
            ['name' => 'Âm nhạc', 'sort_order' => 130],
            ['name' => 'Mỹ thuật', 'sort_order' => 140],
            ['name' => 'Tự nhiên & Xã hội', 'sort_order' => 150],
            ['name' => 'Khoa học', 'sort_order' => 160],
            ['name' => 'Đạo đức', 'sort_order' => 170],
            ['name' => 'Tiếng Việt', 'sort_order' => 180],
        ];

        foreach ($subjects as $subject) {
            Subject::create([
                'name' => $subject['name'],
                'slug' => Str::slug($subject['name']),
                'status' => 1,
                'sort_order' => $subject['sort_order'],
            ]);
        }
    }
}