<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => fake('en_US')->firstName(),
            'lastname' => fake('en_US')->lastName(),
            'middle_initial' => strtoupper(fake('en_US')->randomLetter()),
            'role' => fake()->randomElement(['student', 'teacher']),
            'password' => static::$password ??= Hash::make('password'),
            'username' => fake('en_US')->unique()->userName(),
            'email' => fake('en_US')->unique()->safeEmail(),
            'library_status' => 'active'
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
