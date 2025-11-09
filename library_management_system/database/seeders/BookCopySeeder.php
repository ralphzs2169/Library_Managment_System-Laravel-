<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookCopy;
use App\Models\Book;

class BookCopySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Get all existing books
        $books = Book::all();

        foreach ($books as $book) {
            $copyCount = fake()->numberBetween(1, 5);

            foreach (range(1, $copyCount) as $i) {
                BookCopy::factory()->create([
                    'book_id' => $book->id,
                    'copy_number' => $i,
                ]);
            }
        }
    }
}
