<?php

namespace App\Services;

use App\Models\Clearance;
use App\Models\ActivityLog;
use App\Enums\ClearanceStatus;
use App\Enums\ActivityLogActions;
use Illuminate\Support\Facades\DB;
use App\Services\SemesterService;

class ClearanceService
{
    public $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    public function approveClearance($clearanceId, $librarianId)
    {
        return DB::transaction(function () use ($clearanceId, $librarianId) {
            $clearance = Clearance::findOrFail($clearanceId);

            if ($clearance->status !== ClearanceStatus::PENDING) {
                throw new \Exception('Clearance request is not pending.');
            }

            $clearance->update([
                'status' => ClearanceStatus::APPROVED,
                'approved_by_id' => $librarianId, // Fixed column name
            ]);

            $clearance->user->update([
                'library_status' => 'cleared',
            ]);

            ActivityLog::create([
                'action' => ActivityLogActions::CLEARANCE_APPROVED,
                'details' => 'User ID ' . $clearance->user_id . ' marked as cleared by Librarian ID ' . $librarianId,
                'entity_type' => 'Clearance',
                'entity_id' => $clearance->id,
                'user_id' => $librarianId,
            ]);

            return $clearance;
        });
    }

    public function rejectClearance($clearanceId, $librarianId, $remarks = null)
    {
        return DB::transaction(function () use ($clearanceId, $librarianId, $remarks) {
            $clearance = Clearance::findOrFail($clearanceId);

            if ($clearance->status !== ClearanceStatus::PENDING) {
                throw new \Exception('Clearance request is not pending.');
            }

            $clearance->update([
                'status' => ClearanceStatus::REJECTED,
                'approved_by_id' => $librarianId, // Fixed column name (using approved_by_id for the processor)
                'remarks' => $remarks,
            ]);

            ActivityLog::create([
                'action' => ActivityLogActions::CLEARANCE_REJECTED,
                'details' => 'Clearance for User ID ' . $clearance->user_id . ' rejected by Librarian ID ' . $librarianId . '. Remarks: ' . $remarks,
                'entity_type' => 'Clearance',
                'entity_id' => $clearance->id,
                'user_id' => $librarianId,
            ]);

            return $clearance;
        });
    }

    public function createClearanceRequest($userId, $requestorId = null)
    {
        return DB::transaction(function () use ($userId, $requestorId) {
            
            $activeSemester = $this->semesterService->getActiveSemester();
            $semesterId = $activeSemester ? $activeSemester->id : null;

            $clearance = Clearance::create([
                'user_id' => $userId,
                'semester_id' => $semesterId,
                'status' => ClearanceStatus::PENDING,
                'requested_by_id' => $requestorId,
            ]);

            if ($requestorId !== $userId && $requestorId !== null) {
                $requestorText = ' by User ID ' . $requestorId;
            } else {
                $requestorText = '';
            }
            ActivityLog::create([
                'action' => ActivityLogActions::CLEARANCE_REQUESTED,
                'details' => 'Clearance requested' . $requestorText . ' for User ID ' . $userId,
                'entity_type' => 'Clearance',
                'entity_id' => $clearance->id,
                'user_id' => $userId,
            ]);

            return $clearance;
        });
    }
}