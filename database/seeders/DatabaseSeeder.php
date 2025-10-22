<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);
        DatabaseSeeder::call([
            ProductSeeder::class,
            BlogPostSeeder::class,
            PageSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
