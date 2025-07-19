<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('vi_VN');

        for ($i = 0; $i < 20; $i++) {
            $name = $faker->company . ' ' . $faker->companySuffix;
            School::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'featured_image_id' => null,
                'level_id' => $faker->numberBetween(1, 5), // Assuming level IDs exist
                'address' => $faker->address,
                'province_id' => 1,
                'commune_id' => $faker->numberBetween(1, 30), // Assuming commune IDs exist
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'website' => $faker->domainName,
                'tagline' => $faker->sentence,
                'content' => $faker->paragraphs(3, true),
                'tuition_fee' => $faker->numberBetween(1000000, 50000000),
                'admission_info' => $faker->paragraphs(2, true),
                'is_featured' => $faker->boolean,
                'status' => 1, // 1 for active
                'sort_order' => $i + 1,
            ]);
        }
    }
}
