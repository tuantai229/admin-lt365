<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolType;
use Illuminate\Support\Str;

class SchoolTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolTypes = [
            ['name' => 'Công lập', 'status' => 1],
            ['name' => 'Tư thục', 'status' => 1],
            ['name' => 'Chất lượng cao', 'status' => 1],
            ['name' => 'Song ngữ', 'status' => 1],
            ['name' => 'Liên cấp', 'status' => 1],
        ];

        foreach ($schoolTypes as $type) {
            SchoolType::create([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'status' => $type['status'],
            ]);
        }
    }
}
