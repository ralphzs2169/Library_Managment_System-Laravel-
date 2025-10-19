<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entity_type' => fake()->word(),
            'entity_id' => fake()->numberBetween(1, 1000),
            'action' => fake()->randomElement(['created', 'updated', 'deleted']),
            'details' => fake()->sentence(),
            'user_id' => null,
        ];
    }
}
