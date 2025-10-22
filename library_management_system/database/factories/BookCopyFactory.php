<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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

        $status = fake()->randomElement($statuses);

        return [
            'book_id' => \App\Models\Book::factory(), // assumes you have Book factory
            'copy_number' => fake()->unique()->numberBetween(1, 100),
            'status' => $status,
            'is_archived' => $status !== 'available', // true if status is not 'available'
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

}
