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
use App\Enums\LibraryStatus;
use App\Enums\PenaltyStatus;
use App\Models\Payment;
use App\Enums\ReservationStatus;
use App\Policies\BorrowPolicy;
use App\Policies\RenewalPolicy;
use App\Policies\ReservationPolicy;


class UserService
{

public function getBorrowerDetails(int $userId)
{
    // Load user with student/teacher relationships
    $borrower = User::with(['students.department', 'teachers.department'])
        ->findOrFail($userId);

    // Get active borrow transactions (not yet returned, status borrowed/overdue)
    $activeBorrows = BorrowTransaction::with(['bookCopy.book.author'])
        ->where('user_id', $userId)
        ->whereNull('returned_at')
        ->whereIn('status', ['borrowed', 'overdue'])
        ->get();

    foreach ($activeBorrows as $transaction) {
        $transaction->can_renew = RenewalPolicy::canRenew($borrower, $transaction, false);
    }

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

                    // Remove the full penalties collection to avoid duplication
                    unset($transactionCopy->penalties);

                    // Attach the full penalty info, plus remaining_amount calculation
                    $singlePenalty->remaining_amount = $singlePenalty->status === PenaltyStatus::PARTIALLY_PAID
                        ? (float) $singlePenalty->amount - (float) $singlePenalty->payments->sum(fn($payment) => (float) $payment->amount)
                        : (float) $singlePenalty->amount;

                    $transactionCopy->penalty = $singlePenalty;

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


        $activeReservations = $borrower->activeReservations()->get();

        // Separate pending for queue calculation
        $pendingReservations = $activeReservations->where('status', ReservationStatus::PENDING);

        $positionsByBook = $pendingReservations
            ->groupBy('book_id')
            ->map(fn($reservationsForBook) => $reservationsForBook->pluck('id')->flip());

        // Assign queue position and date_expired
        $activeReservations->each(function ($reservation) use ($positionsByBook) {
            if ($reservation->status === ReservationStatus::PENDING) {
                $reservation->queue_position = $positionsByBook[$reservation->book_id][$reservation->id] + 1;
                $reservation->pickup_deadline_date = null;
            } else { // READY_FOR_PICKUP
                $reservation->queue_position = 0;
                $reservation->pickup_deadline_date = $reservation->pickup_deadline;
            }
        });

        // Assign to user
        $borrower->total_unpaid_fines;
        $borrower->full_name;
        $borrower->active_borrows = $activeBorrows->values();
        $borrower->can_borrow = BorrowPolicy::canBorrow(false, ['borrower_id' => $userId]);
        $borrower->can_reserve = ReservationPolicy::canReserve($borrower, null, false );
        $borrower->transactions_with_penalties = $transactionsWithActivePenalties->values();
        $borrower->active_reservations = $activeReservations;

        return ['borrower' => $borrower ];
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

    public function processPenalty(Request $request, $penaltyId)
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

    public function resetToActiveIfClear(User $user){
        
        $hasRemainingPenalties = $user->borrowTransactions()
        ->whereHas('penalties', function ($q) {
            $q->whereIn('status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID]);
        })
        ->exists();

        if (!$hasRemainingPenalties) {
            $user->library_status = LibraryStatus::ACTIVE;
            $user->save();
        }
    }

}