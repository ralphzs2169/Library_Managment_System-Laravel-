<?php
// app/Services/ActivityLogger.php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{

    protected $semesterService;
    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    public function logBorrowTranscation($borrowTransaction)
    {
        $performerId = Auth::id();
        $activeSemester = $this->semesterService->getActiveSemester();

        ActivityLog::create([
            'action' => 'Borrowed a Book',
            'details' => json_encode([
                'borrow_transaction_id' => $borrowTransaction->id,
                'book_id' => $borrowTransaction->book_id,
                'borrowed_by_user_id' => $borrowTransaction->user_id,
                'borrowed_by_user_name' => $borrowTransaction->user->firstname . ' ' . $borrowTransaction->user->lastname,
                'action_by_user_id' => $performerId,
            ]),
            'entity_type' => 'Borrow Transaction',
            'entity_id' => $borrowTransaction->id,
            'user_id' => $performerId,
            'semester_id' => $activeSemester ? $activeSemester->id : null,
        ]);
    }
    public function logSuspension($suspendedUser, $reason)
    {
        $librarianId = Auth::id();
        $activeSemester = $this->semesterService->getActiveSemester();

        ActivityLog::create([
            'action' => 'Suspended Member',
            'details' => json_encode([
                'suspended_user_id' => $suspendedUser->id,
                'suspended_user_name' => $suspendedUser->name,
                'reason' => $reason,
                'action_by_user_id' => $librarianId,
            ]),
            'entity_type' => 'User',
            'entity_id' => $suspendedUser->id,
            'user_id' => $librarianId,
            'semester_id' => $activeSemester ? $activeSemester->id : null,
        ]);
    }

    public function logLiftSuspension($user, $reason)
    {
        $librarianId = Auth::id();
        $activeSemester = $this->semesterService->getActiveSemester();

        ActivityLog::create([
            'action' => 'Lifted Suspension',
            'details' => json_encode([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'reason' => $reason,
                'action_by_user_id' => $librarianId,
            ]),
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'user_id' => $librarianId,
            'semester_id' => $activeSemester ? $activeSemester->id : null,
        ]);
    }

    
    
}