<?php

namespace Database\Seeders;

use App\Models\CategoryGenre;
use App\Models\User;
use App\Models\Department;
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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            DepartmentSeeder::class,
            CategorySeeder::class,
            GenreSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
            CopySeeder::class
        ]);
    }
}
