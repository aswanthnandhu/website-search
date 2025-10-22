<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Faker\Factory as Faker;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            Product::create([
                'name' => $faker->word(),
                'description' => $faker->sentence(10),
                'category' => $faker->randomElement(['Electronics', 'Clothing', 'Books', 'Furniture']),
                'price' => $faker->randomFloat(2, 10, 500),
            ]);
        }
    }
}
