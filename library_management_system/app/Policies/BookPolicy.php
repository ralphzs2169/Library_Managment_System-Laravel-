<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function borrow(User $user, Book $book) {
        return $user->role === 'student' || $user->role === 'teacher';
    }

    public function return(User $user, Book $book) {
        return $user->role === 'student' || $user->role === 'teacher';
    }

    public function reserve(User $user, Book $book) {
        return $user->role === 'student' || $user->role === 'teacher';
    }
}
