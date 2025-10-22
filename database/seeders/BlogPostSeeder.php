<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use Faker\Factory as Faker; 

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            BlogPost::create([
                'title' => $faker->sentence(6),
                'body' => $faker->paragraph(5),
                'tags' => implode(',', $faker->words(3)),
                'published_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
