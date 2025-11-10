<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BorrowTransaction;
use App\Models\User;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Semester;

class UserService
{
    public function borrowBook(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $borrower = User::findOrFail($request->input('borrower_id'));
            $bookCopy = BookCopy::with('book')->findOrFail($request->input('book_copy_id'));
            $book = $bookCopy->book; // Get book from book copy

            $activeSemester = Semester::where('status', 'active')->first();
            
            // Create the borrow transaction
            $transaction = BorrowTransaction::create([
                'user_id' => $borrower->id,
                'book_copy_id' => $bookCopy->id,
                'semester_id' => $activeSemester ? $activeSemester->id : null,
                'borrowed_at' => now(),
                'due_at' => $request->input('due_date'),
                'status' => 'borrowed'
            ]);

            // Update book copy status
            $bookCopy->update(['status' => 'borrowed']);

            // Create activity log
            ActivityLog::create([
                'action' => 'borrowed',
                'details' => $borrower->full_name . ' borrowed "' . $book->title . '" (Copy #' . $bookCopy->copy_number . ')',
                'entity_type' => 'BorrowTransaction',
                'entity_id' => $transaction->id,
                'user_id' => $request->user()->id,
            ]);

            return $transaction;
        });
    }
}