<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;
use App\Models\BorrowTransaction;
use Illuminate\Support\Facades\Validator;
use App\Models\Semester;

class BorrowPolicy
{
    /**
     * Check if a user can borrow a book.
     * Returns an array with 'result' key (success, not_found, invalid_input, business_rule_violation)
     */

    // Add logic to check if the book is available
    public static function canBorrow($includeInputValidation = true, array $data = [])
    {
        // 1. Ensure borrower exists
        $borrower = User::find($data['borrower_id']);

        if (!$borrower) {
            return ['result' => 'not_found', 'message' => 'Borrower not found.'];
        }

        // 2. Check for outstanding penalties
        if ($borrower->library_status === 'suspended') {
            return ['result' => 'business_rule_violation', 'message' => 'Borrowing suspended due to an outstanding penalty.'];
        }

        // 2. For students, check active semester and borrow limit
        if ($borrower->role === 'student') {
            $hasActive = Semester::where('status', 'active')->exists();
            if (!$hasActive) {
                return ['result' => 'business_rule_violation', 'message' => 'No active semester found. Students can only borrow during an active semester.'];
            }

            $borrowCount = BorrowTransaction::where('user_id', $borrower->id)
                ->where('status', 'borrowed')
                ->count();

            $maxBooks = (int) config('settings.borrowing.max_books_per_student', 3);
            if ($borrowCount >= $maxBooks) {
                return ['result' => 'business_rule_violation', 'message' => "Borrower has reached the maximum limit of {$maxBooks} borrowed books."];
            }
        } else if ($borrower->role === 'teacher') {
            $borrowCount = BorrowTransaction::where('user_id', $borrower->id)
                ->where('status', 'borrowed')
                ->count();

            $maxBooks = (int) config('settings.borrowing.max_books_per_teacher', 5);
            if ($borrowCount >= $maxBooks) {
                return ['result' => 'business_rule_violation', 'message' => "Borrower has reached the maximum limit of {$maxBooks} borrowed books."];
            }
        }

        // 3. Field validation
        if ($includeInputValidation) {
            $duration = $borrower->role === 'student'
                ? (int) config('settings.borrowing.student_duration', 7)
                : (int) config('settings.borrowing.teacher_duration', 14); // default 20 for teachers

            $validator = Validator::make($data, [
                'book_copy_id' => 'required|exists:book_copies,id',
                'borrower_id' => 'required|exists:users,id',
                'due_date' => [
                    'required',
                    'date',
                    'after:today',
                    function ($attribute, $value, $fail) use ($duration) {
                        // Borrow start date: today (or could be future if using borrow_date field)
                        $borrowDate = now()->startOfDay();
                        
                        // Expected maximum due date
                        $expectedDue = $borrowDate->copy()->addDays($duration)->startOfDay();

                        // Input date
                        $inputDue = \Carbon\Carbon::parse($value)->startOfDay();

                        // Compare dates inclusively
                        if ($inputDue->gt($expectedDue)) {
                            $fail("Due date cannot exceed {$duration} days from today ({$expectedDue->format('M d, Y')}).");
                        }
                    },
                ],
                'semester_id' => 'nullable|exists:semesters,id',
            ]);

            if ($validator->fails()) {
                return [
                    'result' => 'invalid_input',
                    'message' => 'Invalid input data.',
                    'errors' => $validator->errors()
                ];
            }
        }

        return ['result' => 'success', 'borrower_fullname' => $borrower->full_name];
    }
}