<?php

namespace Database\Seeders;

use App\Models\CategoryGenre;
use App\Models\User;
use App\Models\Department;
use App\Models\Settings;

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
        $this->call([
            DepartmentSeeder::class,
            CategorySeeder::class,
            GenreSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
            BookCopySeeder::class,
            UserSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
