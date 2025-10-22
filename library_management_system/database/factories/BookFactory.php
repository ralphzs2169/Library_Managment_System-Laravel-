<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cover_image' => 'covers/' . fake()->image(storage_path('app/public/covers'), 200, 300, null, false),
            'title' => fake()->sentence(3),
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake()->paragraph(),
            'publisher' => fake()->company(),
            'publication_year' => fake()->year(),
            'copies_available' => fake()->numberBetween(1, 20),
            'language' => fake()->randomElement(['English', 'Filipino', 'Spanish', 'Chinese', 'Others']),
            'price' => fake()->randomFloat(2, 5, 100),
            'genre_id' => fake()->numberBetween(1, 10),
            'author_id' => fake()->numberBetween(1, 30),
        ];
    }
}
