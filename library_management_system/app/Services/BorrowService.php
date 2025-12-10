<?php

namespace App\Services;

use App\Models\BookCopy;
use App\Models\BorrowTransaction;
use App\Models\Semester;
use App\Models\User;
use App\Models\ActivityLog;
use App\Policies\BookPolicy;
use Illuminate\Support\Facades\DB;
use App\Policies\BorrowPolicy;
use Carbon\Carbon;
use App\Enums\BookCopyStatus;
use App\Enums\ReservationStatus;
use App\Models\Book;

class BorrowService
{
    public function borrowBook($request, $bookCopy, $isFromReservation = false)
    {
         return DB::transaction(function () use ($request, $bookCopy, $isFromReservation) {
            $bookCopy->refresh(); // reload latest status

            if ($bookCopy->status !== BookCopyStatus::AVAILABLE && $bookCopy->status !== BookCopyStatus::ON_HOLD_FOR_PICKUP || $bookCopy->pendingIssueReport()->exists()) {
                throw new \Exception('Book is no longer available.');
            }

            $borrower = User::findOrFail($request->input('borrower_id'));
            $bookCopy = BookCopy::with('book')->findOrFail($request->input('book_copy_id'));
            $book = $bookCopy->book; // Get book from book copy

            $activeSemester = Semester::where('status', 'active')->first();
            
            if (!$activeSemester) {
                throw new \Exception('No active semester found. Cannot proceed with borrowing.');
            }
            // Create the borrow transaction
            $transaction = BorrowTransaction::create([
                'user_id' => $borrower->id,
                'book_copy_id' => $bookCopy->id,
                'semester_id' => $activeSemester->id,
                'borrowed_at' => now(),
                'due_at' => $request->input('due_date'),
                'status' => 'borrowed'
            ]);

            // Update book copy status
            $bookCopy->update(['status' => 'borrowed']);

            // Create activity log
            ActivityLog::create([
                'action' => 'Book Issued',
                'details' => $borrower->full_name . ' borrowed "' . $book->title . '" (Copy #' . $bookCopy->copy_number . ') issued by ' . $request->user()->full_name,
                'entity_type' => 'BorrowTransaction',
                'entity_id' => $transaction->id,
                'user_id' => $request->user()->id,
            ]);

            if ($isFromReservation) {
                $reservation = $borrower->reservations()
                    ->where('book_id', $book->id)
                    ->where('status', ReservationStatus::READY_FOR_PICKUP)
                    ->latest()
                    ->first();

                
                if (!$reservation) {
                    throw new \Exception('No active reservation found for this book.');
                }
          
                $reservation->update(['status' => ReservationStatus::COMPLETED, 'completed_at' => now()]);
                
            }

            return ['transaction' => $transaction, 'action_performer_role' => $request->user()->role];
        });
    }
}