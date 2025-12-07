<?php

namespace App\Policies;

use App\Enums\ClearanceStatus;
use App\Models\User;    
use App\Enums\PenaltyStatus;
use App\Enums\IssueReportStatus;
use App\Enums\ReservationStatus;
use App\Models\Semester;

class ClearancePolicy
{
    public static function canBeCleared(User $user, $context = 'request')
    {
        // 1. Check for unreturned books
        $hasUnreturnedBooks = $user->borrowTransactions()->whereNull('returned_at')->exists();

        if ($hasUnreturnedBooks) {
            return ['result' => 'business_rule_violation', 'message' => 'Borrower has unreturned books.'];
        }

        // 2. Check for unpaid fines
        $hasUnpaidFines = $user->penalties()->whereIn('penalties.status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID])->exists();
        if ($hasUnpaidFines) {
            return ['result' => 'business_rule_violation', 'message' => 'Borrower has unpaid fines.'];
        }

        //3. Check for pending issue reports
        $hasPendingIssues = $user->issueReports()->where('status', IssueReportStatus::PENDING)->exists();
        if ($hasPendingIssues) {
            return ['result' => 'business_rule_violation', 'message' => 'Borrower has pending book issue reports.'];
        }

        // 4. Check for active reservations
        $hasActiveReservations = $user->reservations()->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::READY_FOR_PICKUP])->exists();
        if ($hasActiveReservations) {
            return ['result' => 'business_rule_violation', 'message' => 'Borrower has active reservations.'];
        }

        // 5. Check borrower's library status
        if ($user->library_status === 'suspended') {
            return ['result' => 'business_rule_violation', 'message' => 'Borrower\'s library privileges are suspended.'];
        }

        // 6. if request context, check for existing pending clearance requests
        if ($context === 'request') {
            $hasPendingClearance = $user->clearances()->where('status', ClearanceStatus::PENDING)->exists();
            if ($hasPendingClearance) {
                return ['result' => 'business_rule_violation', 'message' => 'Borrower already has a pending clearance request.'];
            }
        }

        return ['result' => 'success', 'borrower_fullname' => $user->fullname];
    }

    public static function canPerformClearance(User $user)
    {
        // Only librarians can perform clearance
        if ($user->role !== 'librarian') {
            return ['result' => 'business_rule_violation', 'message' => 'Only librarians can perform clearance.'];
        }

        return ['result' => 'success'];
    }

    public static function canRequestClearance($requestorId, $targetUserId = null)
    {
        $requestor = User::find($requestorId);
        if (!$requestor) {
            return ['result' => 'not_found', 'message' => 'Requestor not found.'];
        }

        // Only students, teachers, and staff can request clearance
        if (!in_array($requestor->role, ['student', 'teacher', 'staff'])) {
            return ['result' => 'business_rule_violation', 'message' => 'Only students, teachers, and staff can request clearance.'];
        }

        // If staff, they are requesting for a specific user
        if ($requestor->role === 'staff') {
            if (!$targetUserId) {
                return ['result' => 'not_found', 'message' => 'User to be cleared must be specified.'];
            }

            $userToBeCleared = User::find($targetUserId);
            if (!$userToBeCleared) {
                return ['result' => 'not_found', 'message' => 'User not found.'];
            }

             return self::canBeCleared($userToBeCleared);
        }
        
        // If student/teacher, they are requesting for themselves
        return self::canBeCleared($requestor);
    }
}