<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BorrowTransaction;
use App\Models\User;
use App\Models\Book;
use Carbon\Carbon;
use App\Models\BookCopy;
use App\Models\Penalty;
use App\Models\Semester;
use Illuminate\Support\Facades\Validator;

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

    public function getBorrowerDetails($userId)
    {
        $user = User::with([
            'students.department',
            'teachers.department',
        ])->findOrFail($userId);

        // Active borrows: not yet returned and status borrowed/overdue
        $activeBorrows = BorrowTransaction::with(['bookCopy.book.author'])
            ->where('user_id', $userId)
            ->whereNull('returned_at')
            ->whereIn('status', ['borrowed', 'overdue'])
            ->get();

        // Transactions with unpaid penalties (DB-driven)
        $unpaidPenaltyTransactions = BorrowTransaction::with(['bookCopy.book.author'])
            ->where('user_id', $userId)
            ->whereHas('penalties', fn($q) => $q->where('status', 'unpaid'))
            ->get()
            ->flatMap(function ($borrowRecord) {
                return $borrowRecord->penalties
                    ->where('status', 'unpaid')
                    ->map(function ($singlePenalty) use ($borrowRecord) {
                        $borrowRecordCopy = clone $borrowRecord;
                        $borrowRecordCopy->penalty = $singlePenalty; // attach only the single unpaid penalty
                        return $borrowRecordCopy;
                    });
            });



        $today = now()->startOfDay();

        $activeBorrows->transform(function ($tx) use ($today) {
            $dueDate = Carbon::parse($tx->due_at)->startOfDay();

            if ($tx->status === 'overdue') {
                $tx->days_overdue = $dueDate->diffInDays($today);
                $tx->days_until_due = null;
            } else {
                $tx->days_overdue = null;
                $tx->days_until_due = $dueDate->gt($today) ? $today->diffInDays($dueDate) : 0;
            }
            return $tx;
        });

        $totalFines = 0;

        $unpaidPenaltyTransactions->transform(function ($transaction) use (&$totalFines) {
            $unpaid = $transaction->penalties->where('status', 'unpaid');
            $amount = (float) $unpaid->sum('amount');
            $totalFines += $amount;

            if ($transaction->returned_at) {
                $dueDate = Carbon::parse($transaction->due_at)->startOfDay();
                $returnedDate = Carbon::parse($transaction->returned_at)->startOfDay();
                $transaction->days_overdue = $returnedDate->gt($dueDate) ? $dueDate->diffInDays($returnedDate) : 0;
            } else {
                $transaction->days_overdue = null;
            }
            $transaction->days_until_due = null;

            return $transaction;
        });

        $dueReminderThreshold = config('settings.notifications.reminder_days_before_due', 3);
        $user->total_unpaid_fines = $totalFines;
        $user->full_name = $user->getFullnameAttribute();
        $user->active_borrows = $activeBorrows->values();
        $user->transactions_with_unpaid_penalties = $unpaidPenaltyTransactions->values();

        // Return explicit arrays for the API consumer (avoid relying on dynamic model properties)
        return [
            'user' => $user,
            'due_reminder_threshold' => $dueReminderThreshold
        ];
    }

    
    public function validateBorrowRequest(Request $request)
    {
        // Ensure borrower exists
        $borrower = User::find($request->input('borrower_id'));
        if (!$borrower) {
            return ['status' => 'invalid', 'message' => 'Borrower not found.'];
        }

        // 1. Check for outstanding penalties
        if ($borrower->library_status === 'suspended') {
            return ['status' => 'invalid', 'message' => 'Borrowing suspended due to an outstanding penalty.'];
        }

        // 2. For students, check active semester and borrow limit
        if ($borrower->role === 'student') {
            $hasActive = Semester::where('status', 'active')->exists();
            if (!$hasActive) {
                return ['status' => 'invalid', 'message' => 'No active semester found. Students can only borrow during an active semester.'];
            }

            $borrowCount = BorrowTransaction::where('user_id', $borrower->id)
                ->where('status', 'borrowed')
                ->count();

            if ($borrowCount >= 3) {
                return ['status' => 'invalid', 'message' => 'Borrower has reached the maximum limit of 3 borrowed books.'];
            }
        }

        // 3. Field validation
        $validator = Validator::make($request->all(), [
            'book_copy_id' => 'required|exists:book_copies,id',
            'borrower_id' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
            'semester_id' => 'nullable|exists:semesters,id',
        ]);

        if ($validator->fails()) {
            return ['status' => 'invalid', 'errors' => $validator->errors()];
        }

        return ['status' => 'valid'];
    }

    public function returnBook(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $borrower = User::findOrFail($request->input('borrower_id'));
            $bookCopyId = $request->input('book_copy_id');
            $reportDamaged = $request->boolean('report_damaged');

            $transaction = BorrowTransaction::where('user_id', $borrower->id)
                ->where('book_copy_id', $bookCopyId)
                ->whereNull('returned_at')
                ->firstOrFail();

            $bookCopy = BookCopy::with('book')->findOrFail($bookCopyId);
            $book = $bookCopy->book;

            // Check if returned late
            $dueDate = \Carbon\Carbon::parse($transaction->due_at)->startOfDay();
            $returnedDate = now()->startOfDay();
            $isLate = $returnedDate->gt($dueDate);

            // Determine final status and copy status
            if($isLate){
                $finalStatus = 'returned';
                $copyStatus = 'available';

                $penalty = Penalty::create([
                    'borrow_transaction_id' => $transaction->id,
                    'amount' => min(round($dueDate->diffInDays($returnedDate) * (float)config('settings.penalty.rate_per_day', 0), 2), (float)config('settings.penalty.max_amount', 0)),
                    'type' => 'late_return',
                    'status' => 'unpaid',
                    'issued_at' => now(),
                ]);

                if (!$penalty) {
                    throw new \Exception('Failed to create penalty record.');
                }
            } else if ($reportDamaged) {
                // If damaged, status is 'damaged' regardless of being late
                $finalStatus = 'damaged';
                $copyStatus = 'damaged';
            } else {
                // Not damaged - mark as 'returned'
                $finalStatus = 'returned';
                $copyStatus = 'available';
            }

            // Update transaction
             $transaction->update([
                'returned_at' => now(),
                'status' => $finalStatus,
            ]);

            // Update book copy status
            $bookCopy->update(['status' => $copyStatus]);

            // Create activity log
            $damagedText = $reportDamaged ? ' (reported as damaged)' : '';
            $lateText = $isLate && !$reportDamaged ? ' (returned late)' : '';
            ActivityLog::create([
                'action' => 'returned',
                'details' => $borrower->full_name . ' returned "' . $book->title . '" (Copy #' . $bookCopy->copy_number . ')' . $damagedText . $lateText,
                'entity_type' => 'BorrowTransaction',
                'entity_id' => $transaction->id,
                'user_id' => $request->user()->id,
            ]);

            return $transaction;
        });
    }

    public function validateReturnRequest(Request $request)
    {
        // Ensure borrower exists
        $borrower = User::find($request->input('borrower_id'));
        if (!$borrower) {
            return ['status' => 'invalid', 'message' => 'Borrower not found.'];
        }

        $bookCopyId = $request->input('book_copy_id');
        if (!$bookCopyId) {
            return ['status' => 'invalid', 'message' => 'Missing book copy identifier.'];
        }

        // Check if active transaction exists
        $transaction = BorrowTransaction::where('user_id', $borrower->id)
            ->where('book_copy_id', $bookCopyId)
            ->whereNull('returned_at')
            ->first();

        if (!$transaction) {
            return ['status' => 'invalid', 'message' => 'No active borrow transaction found for this book.'];
        }

        return ['status' => 'valid'];
    }
}