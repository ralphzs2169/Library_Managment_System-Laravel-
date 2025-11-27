<?php

namespace App\Policies;

use App\Enums\ReservationStatus;
use App\Models\Semester;

class ReservationPolicy
{
    public static function canReserve($user, $book, $checkWithBookPolicy = true)
    {   
        // 1. Check user status
        if ($user->library_status === 'suspended') {
            return ['result' => 'business_rule_violation', 'message' => 'Reservation suspended due to an outstanding penalty.'];
        }

        // 2. If student, check active semester
        if ($user->role === 'student') {
            $hasActive = Semester::where('status', 'active')->exists();
            if (!$hasActive) {
                return ['result' => 'business_rule_violation', 'message' => 'No active semester found. Students can only reserve during an active semester.'];
            }
        }

        // 3. Check user max pending reservations
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

        // 4. Check if the book can be reserved (business rule)
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