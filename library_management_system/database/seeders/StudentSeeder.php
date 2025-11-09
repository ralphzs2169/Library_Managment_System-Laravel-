<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      public function run(): void
    {
        // Get all users with role = 'student' between ID 53–92
        $studentUsers = User::where('role', 'student')->get();

        foreach ($studentUsers as $user) {
            Student::create([
                'user_id' => $user->id,
                'student_number' => fake()->unique()->numerify('STU#####'),
                'year_level' => fake()->numberBetween(1, 4),
                'department_id' => fake()->numberBetween(1, 5), // adjust to your schema
            ]);
        }
    }       
}
