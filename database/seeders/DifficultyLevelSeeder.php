<?php

namespace Database\Seeders;

use App\Models\DifficultyLevel;
use Illuminate\Database\Seeder;

class DifficultyLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Dễ',
                'slug' => 'de',
                'status' => 1,
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Trung bình',
                'slug' => 'trung-binh',
                'status' => 1,
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Khó',
                'slug' => 'kho',
                'status' => 1,
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'name' => 'Rất khó',
                'slug' => 'rat-kho',
                'status' => 1,
                'sort_order' => 4,
            ],
        ];

        foreach ($data as $item) {
            DifficultyLevel::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
