<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BorrowTransaction;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Enums\ReservationStatus;

class RenewalPolicy
{
    /**
     * Check if a user can renew a borrow transaction.
     * Returns an array with 'result' key (success, not_found, invalid_input, business_rule_violation)
     */
    public static function canRenew(User $renewer, BorrowTransaction $transaction, $includeInputValidation = true, array $data = [])
    {
        // 1. Check borrower
        if (!$renewer) {
            return ['result' => 'not_found', 'message' => 'No renewer found for this transaction.'];
        }

        // 2. Renewal limit
        $timesRenewed = $transaction->times_renewed;
        $renewalLimit = $renewer->role === 'student'
            ? (int) config('settings.renewing.student_renewal_limit')
            : (int) config('settings.renewing.teacher_renewal_limit');

        if ($timesRenewed >= $renewalLimit) {
            return [
                'result' => 'business_rule_violation',
                'message' => "Borrower has reached the maximum number of renewals ({$renewalLimit}) for this book"
            ];
        }

        // 3. Check transaction
        if (!$transaction) {
            return ['result' => 'not_found', 'message' => 'No active borrow transaction found for this book.'];
        }

        // 3a. Check if overdue
        if($transaction->status === 'overdue') {
            return ['result' => 'business_rule_violation', 'message' => 'Overdue books cannot be renewed.'];
        }

        // 3b. Check if status is 'borrowed'
        if($transaction->status !== 'borrowed') {
            return ['result' => 'business_rule_violation', 'message' => 'Only borrowed books can be renewed.'];
        }

        // 3c. Check if already returned
        if($transaction->returned_at !== null) {
            return ['result' => 'business_rule_violation', 'message' => 'This book has already been returned.'];
        }

        // 4. Check active semester for students
        if ($renewer->role === 'student') {
            $hasActive = Semester::where('status', 'active')->exists();
            if (!$hasActive) {
                return ['result' => 'business_rule_violation', 'message' => 'No active semester found. Students can only borrow during an active semester.'];
            }
        }

        // 5. Check penalties
        if ($renewer->library_status === 'suspended') {
            return ['result' => 'business_rule_violation', 'message' => 'Renewal suspended due to an outstanding penalty.'];
        }

        // 6. Minimum days before due date
        $minDaysBeforeRenewal = $renewer->role === 'student'
            ? (int) config('settings.renewing.student_min_days_before_renewal')
            : (int) config('settings.renewing.teacher_min_days_before_renewal');

        $currentDueDate = Carbon::parse($transaction->due_at)->startOfDay();
        $today = now()->startOfDay();
        $daysBeforeDue = $today->diffInDays($currentDueDate);

        $role = $renewer->role;   
        $capitalizedRole = ucfirst($role); 

        if ($daysBeforeDue > $minDaysBeforeRenewal) {
            return [
                'result' => 'business_rule_violation',
                'message' => "{$capitalizedRole}s can only renew {$minDaysBeforeRenewal} day(s) before the due date or later."
            ];
        }

        // 7.  Check for any pending reservations under this book title
        $hasPendingReservations = $transaction->bookCopy->book->reservations()
                ->where('status', ReservationStatus::PENDING)
                ->exists();

        if ($hasPendingReservations){
            return [
                'result' => 'business_rule_violation',
                'message' => "This title has a pending reservation by another member."
            ];
        }

        // 7. Validate new due date
        if ($includeInputValidation) {
            $renewDuration = $renewer->role === 'student'
                ? (int) config('settings.renewing.student_duration', 7)
                : (int) config('settings.renewing.teacher_duration', 10);

            $validator = Validator::make($data, [
                'new-due-date' => [
                    'required',
                    'date',
                    'after:today',
                    function ($attribute, $value, $fail) use ($transaction, $renewDuration) {
                        $newDueDate = Carbon::parse($value);
                        $maxDueDate = Carbon::parse($transaction->due_at)->addDays($renewDuration);

                        if ($newDueDate->gt($maxDueDate)) {
                            $fail("The new due date cannot exceed {$renewDuration} day(s) from the current due date.");
                        }
                    },
                ],
            ]);

            if ($validator->fails()) {
                return [
                    'result' => 'invalid_input',
                    'message' => 'Invalid input found',
                    'errors' => $validator->errors()
                ];
            }
        }


        return ['result' => 'success', 'renewer_fullname' => $renewer->full_name];
    }
}
