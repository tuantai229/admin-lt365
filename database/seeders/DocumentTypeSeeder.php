<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('document_types')->insert([
            [
                'id' => 1,
                'name' => 'Đề thi',
                'slug' => 'de-thi',
                'status' => 1,
                'sort_order' => 10,
            ],
            [
                'id' => 2,
                'name' => 'Tài liệu ôn tập',
                'slug' => 'tai-lieu-on-tap',
                'status' => 1,
                'sort_order' => 20,
            ],
            [
                'id' => 3,
                'name' => 'Bài tập',
                'slug' => 'bai-tap',
                'status' => 1,
                'sort_order' => 30,
            ],
            [
                'id' => 4,
                'name' => 'Bài giảng',
                'slug' => 'bai-giang',
                'status' => 1,
                'sort_order' => 40,
            ],
            [
                'id' => 5,
                'name' => 'Tổng hợp lý thuyết',
                'slug' => 'tong-hop-ly-thuyet',
                'status' => 1,
                'sort_order' => 50,
            ],
            [
                'id' => 6,
                'name' => 'Văn mẫu',
                'slug' => 'van-mau',
                'status' => 1,
                'sort_order' => 60,
            ],
        ]);
    }
}
