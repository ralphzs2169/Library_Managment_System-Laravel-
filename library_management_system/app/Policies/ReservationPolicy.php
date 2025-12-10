<?php

namespace App\Policies;

use App\Enums\ReservationStatus;
use App\Models\Semester;
use App\Enums\PenaltyStatus;
use App\Enums\ClearanceStatus;

class ReservationPolicy
{
    public static function canReserve($user, $book, $checkWithBookPolicy = true)
    {   
       
        // 1. Check active semester
        $hasActive = Semester::where('status', 'active')->exists();
        if (!$hasActive) {
            return ['result' => 'business_rule_violation', 'message' => 'No active semester found. User can only reserve during an active semester.'];
        }

        // 2. Check if borrower is inactive (cleared)
        if ($user->library_status === 'inactive') {
            return ['result' => 'business_rule_violation', 'message' => 'Reservation privileges revoked: User has been officially cleared.'];
        }

        // 3. Check if borrower already has a pending clearance request
        $hasPendingClearance = $user->clearances()
            ->where('status', ClearanceStatus::PENDING)
            ->exists();

        if ($hasPendingClearance) {
            return ['result' => 'business_rule_violation', 'message' => 'Borrower already has a pending clearance request.'];
        }
    
         // 4. Check for outstanding penalties
        $hasOutstandingPenalties = $user->penalties()
            ->whereIn('penalties.status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID])
            ->exists();
        if ($hasOutstandingPenalties) {
            return ['result' => 'business_rule_violation', 'message' => 'Reservation suspended due to an outstanding penalty.'];
        }

        // 5. Check if suspended
        if ($user->library_status === 'suspended') {
            return ['result' => 'business_rule_violation', 'message' => 'Borrower\'s library privileges are suspended.'];
        }
        
        // 6. Check user max pending reservations
        $maxPending = (int) config('settings.reservation.' . $user->role . '_max_pending_reservations');

        $pendingCount = $user->reservations()
            ->where('status', ReservationStatus::PENDING)
            ->count();

        if ($pendingCount >= $maxPending) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'Member has reached the maximum number of pending reservations allowed.'
            ];
        }

        // 7. Check if the book can be reserved (business rule)
        if ($checkWithBookPolicy) {
            $bookReservationCheck = BookPolicy::canBeReserved($book, $user);

            if ($bookReservationCheck['result'] !== 'success') {
                return $bookReservationCheck;
            }
        }

        return [
            'result' => 'success',
            'reserver_fullname' => $user->full_name
        ];
    }

    public static function canBeCancelled($reservation)
    {
        // Only PENDING or READY_FOR_PICKUP reservations can be cancelled
        if (!in_array($reservation->status, [ReservationStatus::PENDING, ReservationStatus::READY_FOR_PICKUP])) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'Only pending or ready for pickup reservations can be cancelled.'
            ];
        }

        return [
            'result' => 'success'
        ];
    }
}