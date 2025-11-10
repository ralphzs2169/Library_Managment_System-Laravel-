<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BorrowTransaction;
use App\Models\Book;
use App\Services\SemesterService;

class BorrowTransactionPolicy
{
    public function view(User $user, BorrowTransaction $transaction)
    {
        // Librarians can view all
        if ($user->role === 'librarian' || $user->role === 'staff') {
            return true;
        }

        // Borrowers can view only their own
        return $user->id === $transaction->user_id;
    }

     // A user can view their transaction history (past semesters)
    public function viewOwnHistory(User $user)
    {
        return $user->role === 'student' || $user->role === 'teacher';
    }
    
    public function create(User $user, Book $book)
    {
        // Only students, teachers, or staff can borrow
        if (!in_array($user->role, ['student', 'teacher'])) {
            return false;
        }

        // For students, must have an active semester
        if ($user->role === 'student' && !SemesterService::getActiveSemesterId()) {
            return false;
        }

        return true;
    }

    public function update(User $user, BorrowTransaction $transaction)
    {
        // Only staff can mark as returned or modify transactions
        return $user->role === 'staff';
    }
}
