<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Level;
use App\Models\Subject;
use App\Models\Province;
use App\Models\Commune;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = Level::where('status', 1)->pluck('id')->toArray();
        $subjects = Subject::where('status', 1)->pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            $name = 'Trung tâm ' . Str::random(10);
            $center = Center::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'tagline' => 'Tagline for ' . $name,
                'experience' => rand(1, 10),
                'address' => 'Address ' . $i,
                'province_id' => 1,
                'commune_id' => rand(1, 30),
                'phone' => '09' . rand(10000000, 99999999),
                'email' => Str::slug($name) . '@example.com',
                'website' => 'https://' . Str::slug($name) . '.com',
                'content' => 'Content for ' . $name,
                'status' => 1,
                'sort_order' => $i + 1,
                'featured_image_id' => null,
            ]);

            if (!empty($levels)) {
                $randomLevels = (array) array_rand(array_flip($levels), rand(1, min(3, count($levels))));
                $center->levels()->attach($randomLevels);
            }
            if (!empty($subjects)) {
                $randomSubjects = (array) array_rand(array_flip($subjects), rand(1, min(5, count($subjects))));
                $center->subjects()->attach($randomSubjects);
            }
        }
    }
}
