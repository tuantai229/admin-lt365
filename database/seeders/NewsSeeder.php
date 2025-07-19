<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $category = DB::table('news_categories')->where('name', 'Tin tuyển sinh')->first();

        if ($category) {
            for ($i = 0; $i < 25; $i++) {
                $title = $faker->sentence(6, true);
                $newsId = DB::table('news')->insertGetId([
                    'admin_user_id' => 1, // Assuming an admin user with ID 1 exists
                    'name' => $title,
                    'slug' => Str::slug($title),
                    'content' => $faker->paragraphs(5, true),
                    'status' => 1, // Assuming 1 is 'published'
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('news_category_links')->insert([
                    'news_id' => $newsId,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
