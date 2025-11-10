<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Semester;

class SemesterPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'librarian';
    }

    public function create(User $user)
    {
        return $user->role === 'librarian';
    }

    public function activate(User $user, Semester $semester)
    {
        return $user->role === 'librarian';
    }

    public function deactivate(User $user, Semester $semester)
    {
        return $user->role === 'librarian';
    }

    public function update(User $user, Semester $semester)
    {
        return $user->role === 'librarian';
    }
}
