<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Tin tuyển sinh',
            'Thành tích học sinh',
            'Tư vấn chọn trường',
            'Hướng dẫn thi cử',
            'Kinh nghiệm ôn thi',
        ];

        foreach ($categories as $category) {
            DB::table('news_categories')->insert([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => 'Danh mục ' . strtolower($category),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
