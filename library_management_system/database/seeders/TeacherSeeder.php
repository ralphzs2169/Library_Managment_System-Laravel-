<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with role = 'teacher' between ID 53–92
        $teacherUsers = User::where('role', 'teacher')->get();

        foreach ($teacherUsers as $user) {
            Teacher::create([
                'user_id' => $user->id,
                'employee_number' => fake()->unique()->numerify('EMP#####'),
                'department_id' => fake()->numberBetween(1, 5),
            ]);
        }
    }
}
