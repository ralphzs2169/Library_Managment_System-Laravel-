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
use App\Enums\BookCopyStatus;
use App\Enums\PenaltyType;
use App\Enums\PenaltyStatus;
use App\Enums\BorrowTransactionStatus;
use App\Models\IssueReport;
use App\Enums\IssueReportType;
use App\Enums\IssueReportStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function borrowBook(Request $request, $bookCopy)
    {
        return DB::transaction(function () use ($request, $bookCopy) {
            $bookCopy->refresh(); // reload latest status

            if ($bookCopy->status !== 'available' || $bookCopy->pendingIssueReport()->exists()) {
                throw new \Exception('Book is no longer available.');
            }

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

public function getBorrowerDetails(int $userId)
{
    // Load user with student/teacher relationships
    $user = User::with(['students.department', 'teachers.department'])
        ->findOrFail($userId);

    // Get active borrow transactions (not yet returned, status borrowed/overdue)
    $activeBorrows = BorrowTransaction::with(['bookCopy.book.author'])
        ->where('user_id', $userId)
        ->whereNull('returned_at')
        ->whereIn('status', ['borrowed', 'overdue'])
        ->get();

    // Get all transactions that have unpaid or partially paid penalties
    $transactionsWithActivePenalties = BorrowTransaction::with(['bookCopy.book.author', 'penalties.payments'])
        ->where('user_id', $userId)
        ->whereHas('penalties', fn($query) => $query->whereIn('status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID]))
        ->get()
        ->flatMap(function ($borrowTransaction) {
            return $borrowTransaction->penalties
                ->whereIn('status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID])
                ->map(function ($singlePenalty) use ($borrowTransaction) {

                    // Clone the borrow transaction so we can attach a single penalty
                    $transactionCopy = clone $borrowTransaction;
                    $transactionCopy->penalty = $singlePenalty;

                    // Calculate remaining amount safely as float
                    if ($singlePenalty->status === PenaltyStatus::PARTIALLY_PAID) {
                        $totalPaid = (float) $singlePenalty->payments->sum(fn($payment) => (float) $payment->amount);
                        $transactionCopy->penalty->remaining_amount = (float) $singlePenalty->amount - $totalPaid;
                    } else {
                        $transactionCopy->penalty->remaining_amount = (float) $singlePenalty->amount;
                    }

                    return $transactionCopy;
                });
        });

    $today = now()->startOfDay();

    // Compute overdue and days until due for active borrows
    $activeBorrows->transform(function ($borrow) use ($today) {
        $dueDate = Carbon::parse($borrow->due_at)->startOfDay();

        if ($borrow->status === 'overdue') {
            $borrow->days_overdue = $dueDate->diffInDays($today);
            $borrow->days_until_due = null;
        } else {
            $borrow->days_overdue = null;
            $borrow->days_until_due = $dueDate->gt($today) ? $today->diffInDays($dueDate) : 0;
        }

        return $borrow;
    });

    // Compute days overdue for transactions with penalties
    $transactionsWithActivePenalties->transform(function ($transaction) {
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

    // Config values
    $dueReminderThreshold = (int) config('settings.notifications.reminder_days_before_due', 3);
    $borrowDurationDays = (int) config('settings.borrowing.borrow_duration', 14);

    // Assign to user
    $user->total_unpaid_fines = $user->getTotalUnpaidFinesAttribute();
    $user->full_name = $user->getFullnameAttribute();
    $user->active_borrows = $activeBorrows->values();
    $user->transactions_with_penalties = $transactionsWithActivePenalties->values();

    return [
        'user' => $user,
        'due_reminder_threshold' => $dueReminderThreshold,
        'borrow_duration' => $borrowDurationDays,
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

            // Determine final transaction status and copy status
            if($isLate){
                $transactionStatus = BorrowTransactionStatus::RETURNED;
                $copyStatus = BookCopyStatus::AVAILABLE;

                $penalty = Penalty::create([
                    'borrow_transaction_id' => $transaction->id,
                    'amount' => min(round($dueDate->diffInDays($returnedDate) * (float)config('settings.penalty.rate_per_day', 0), 2), (float)config('settings.penalty.max_amount', 0)),
                    'type' => PenaltyType::LATE_RETURN,
                    'status' => PenaltyStatus::UNPAID,
                    'issued_at' => now(),
                ]);

                if (!$penalty) {
                    throw new \Exception('Failed to create penalty record.');
                }

                if ($reportDamaged) {
                    // If also damaged, override statuses
                    $transactionStatus = BorrowTransactionStatus::RETURNED;
                    $copyStatus = BookCopyStatus::PENDING_ISSUE_REVIEW;

                    IssueReport::create([
                        'book_copy_id' => $bookCopy->id,
                        'borrower_id' => $borrower->id,
                        'reported_by' => $request->user()->id,
                        'report_type' => IssueReportType::DAMAGE,
                        'description' => $request->input('damage_description', 'Reported damaged upon return.'),
                        'status' => IssueReportStatus::PENDING,
                    ]);
                }
                
            } else if ($reportDamaged) {
                // If damaged, status is 'damaged' regardless of being late
                $transactionStatus = BorrowTransactionStatus::RETURNED;
                $copyStatus = BookCopyStatus::PENDING_ISSUE_REVIEW;

                    IssueReport::create([
                        'book_copy_id' => $bookCopy->id,
                        'borrower_id' => $borrower->id,
                        'reported_by' => $request->user()->id,
                        'report_type' => IssueReportType::DAMAGE,
                        'description' => $request->input('damage_description', 'Reported damaged upon return.'),
                        'status' => IssueReportStatus::PENDING,
                    ]);
            } else {
                // Not damaged - mark as 'returned'
                $transactionStatus = BorrowTransactionStatus::RETURNED;
                $copyStatus = BookCopyStatus::AVAILABLE;
            }

            // Update transaction
             $transaction->update([
                'returned_at' => now(),
                'status' => $transactionStatus,
            ]);

            // Update book copy status
            $bookCopy->update(['status' => $copyStatus]);

            // Create activity log
            $damagedText = $reportDamaged ? ' (reported as damaged)' : '';
            $lateText = $isLate && !$reportDamaged ? ' (returned late)' : '';
            ActivityLog::create([
                'action' => 'returned',
                'details' => $borrower->full_name . ' returned "' . $book->title . '" (Copy #' . $bookCopy->copy_number . ')' . $damagedText . $lateText,
                'entity_type' => 'Borrow Transaction',
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

    public function validatePenaltyUpdate(Request $request)
    {
        // Check if penalty is already paid
        $penalty = Penalty::find($request->input('penalty_id'));

        if (!$penalty) {
            return ['status' => 'invalid', 'message' => 'Penalty not found.'];
        }

        if ($penalty->status === PenaltyStatus::PAID) {
            return ['status' => 'invalid', 'message' => 'Penalty has already been paid.'];
        }

        $paidAmount = $penalty->payments->sum('amount');
    $remainingAmount = $penalty->amount - $paidAmount;

        // Validate input
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'required|numeric|min:0.01|max:' . $remainingAmount,
            ],
            [
                'amount.max' => "The payment cannot exceed the penalty amount",
                'amount.min' => "The payment must be at least ₱0.01",
            ]
        );


        if ($validator->fails()) {
            return ['status' => 'invalid', 'errors' => $validator->errors()];
        }

        return ['status' => 'valid'];
    }

    public function updatePenalty(Request $request, $penaltyId)
    {
        return DB::transaction(function () use ($request, $penaltyId) {
            $penalty = Penalty::with('payments', 'borrowTransaction')
                ->findOrFail($penaltyId);

            // Add new payment
            Payment::create([
                'penalty_id' => $penalty->id,
                'amount' => $request->input('amount'),
                'paid_by_id' => $penalty->borrowTransaction->user_id,
                'processed_by_id' => $request->user()->id,
                'paid_at' => now(),
            ]);

            // Calculate updated paid amount
            $currentAmountPaid = $penalty->payments->sum('amount');
            $amountPaid = $request->input('amount');
            $newAmountTotal = $currentAmountPaid + $amountPaid;

            // Determine new status
            $penalty->status = $newAmountTotal < $penalty->amount
                ? PenaltyStatus::PARTIALLY_PAID
                : PenaltyStatus::PAID;

            $penalty->save();

            // If penalty is fully paid, check if borrower can be reactivated
            if ($penalty->status === PenaltyStatus::PAID) {

                $userId = $penalty->borrowTransaction->user_id;

                $hasRemainingPenalties = Penalty::whereHas('borrowTransaction', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->where('status', '!=', PenaltyStatus::PAID)
                ->exists();

                // If NO remaining penalties → activate user
                if (!$hasRemainingPenalties) {
                    User::where('id', $userId)->update([
                        'library_status' => 'active'
                    ]);
                }
            }

            // Log activity
            ActivityLog::create([
                'action' => 'paid',
                'details' => "Updated penalty ID: {$penalty->id} to {$penalty->status} with paid amount {$request->amount}",
                'entity_type' => 'Penalty',
                'entity_id' => $penalty->id,
                'user_id' => $request->user()->id,
            ]);

            return $penalty;
        });
    }

}