<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Book;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookCopy>
 */
class BookCopyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $statuses = ['available', 'borrowed', 'lost', 'damaged', 'withdrawn'];

        return [
            'book_id' => Book::factory(),
            'copy_number' => 1, // default, will be updated later
            'status' => fake()->randomElement($statuses),
            'is_archived' => false,
        ];
    }


}
