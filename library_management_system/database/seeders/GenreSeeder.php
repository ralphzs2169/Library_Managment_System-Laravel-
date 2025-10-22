<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $genres = [
            'Adventure',
            'Horror',
            'Thriller',
            'Comedy',
            'Drama',
            'Poetry',
            'Graphic Novel',
            'Young Adult',
            'Children\'s',
            'Classic',
            'Science Fiction',
            'Fantasy',
            'Mystery',
            'Romance',
            'Biography',
            'Self-Help',
            'Historical Fiction',
            'Non-Fiction',
            'Crime',
            'Satire',
        ];

        // DB::table('genres')->truncate(); // clears all existing rows

        foreach ($genres as $genre) {
            DB::table('genres')->insert([
                'name' => $genre,
                'category_id' => rand(1, 10), // assuming there are 10 categories
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
