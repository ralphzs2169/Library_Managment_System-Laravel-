<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Teacher;
use App\Models\Student;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        User::factory()->count(50)->create();

        // Create custom users with specific passwords
        $customUsers = [
            [
                'firstname' => 'Nina Mae',
                'lastname' => 'Damaolao',
                'middle_initial' => 'S',
                'role' => 'librarian',
                'email' => 'admin@example.com',
                'username' => 'ninamae',
                'password' => Hash::make('09232929'), // hashed automatically
                'library_status' => 'active'
            ],
            [
                'firstname' => 'Karne Marie',
                'lastname' => 'Guiral',
                'middle_initial' => 'C',
                'role' => 'staff',
                'email' => 'staff@example.com',
                'username' => 'karenmarie',
                'password' => Hash::make('09232929'), // hashed automatically
                'library_status' => 'active'
            ],
        ];

        foreach ($customUsers as $user) {
            User::factory()->create($user);
        }
    }
}
