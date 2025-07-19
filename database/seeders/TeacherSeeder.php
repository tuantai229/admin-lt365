<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Level;
use App\Models\Province;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = Level::all();
        $subjects = Subject::all();
        $provinces = Province::all();

        for ($i = 0; $i < 20; $i++) {
            $name = 'Teacher ' . ($i + 1);
            $teacher = Teacher::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'tagline' => 'Tagline for ' . $name,
                'experience' => rand(1, 20),
                'address' => 'Address ' . ($i + 1),
                'province_id' => $provinces->isNotEmpty() ? $provinces->random()->id : 0,
                'commune_id' => 0, // Assuming we don't need to seed this for now
                'phone' => '098765432' . $i,
                'email' => 'teacher' . $i . '@example.com',
                'website' => 'https://teacher' . $i . '.com',
                'content' => 'Content for ' . $name,
                'status' => 1,
                'sort_order' => $i + 1,
            ]);

            if ($levels->isNotEmpty()) {
                $teacher->levels()->attach($levels->random(rand(1, 3))->pluck('id')->toArray());
            }

            if ($subjects->isNotEmpty()) {
                $teacher->subjects()->attach($subjects->random(rand(1, 3))->pluck('id')->toArray());
            }
        }
    }
}
