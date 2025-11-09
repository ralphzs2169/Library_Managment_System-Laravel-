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
          $covers = [
            'aHcq6K7Iz13P47rDrNzDFwmpZlfFPBLmWsWUtBCZ.jpg',
            'zR7HyJPFGa5RE1J4m72Q98Su3uYRsjhF5oHFTFCk.jpg',
            'nZu7jDhUbXAqmpAZw3Lto9lfuIzXqnMLgc5VXXHX.jpg',
            'pfMXpuzu2wROMCV9C9iX8XVKLSQMcbYK9mXOu3R4.jpg',
            'djVWtopfed7NyT3D7oC6eba2ufniAgcQycAN9kMV.jpg',
            'IcwlAT1ijnCI2Yk6U5HsOH5fsA3IkHzMe5gyw4Xv.jpg'
        ];

        return [
            'cover_image' => 'covers/' . fake()->randomElement($covers),
            'title' => fake('en_US')->sentence(3),
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake('en_US')->paragraph(),
            'publisher' => fake('en_US')->company(),
            'publication_year' => fake()->year(),
            'language' => fake()->randomElement(['English', 'Filipino', 'Spanish', 'Chinese', 'Others']),
            'price' => fake()->randomFloat(2, 5, 100),
            'genre_id' => fake()->numberBetween(1, 10),
            'author_id' => fake()->numberBetween(1, 30),
        ];
    }
}
