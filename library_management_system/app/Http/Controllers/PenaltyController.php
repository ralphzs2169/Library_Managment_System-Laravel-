<?php

namespace App\Http\Controllers;

use App\Enums\LibraryStatus;
use App\Enums\PenaltyStatus;
use App\Models\ActivityLog;
use App\Models\Penalty;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;
use App\Models\User;

class PenaltyController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

     public function processPenalty(Request $request, $penaltyId)
    {
        if ($request->validate_only) {
            $result = $this->userService->validatePenaltyUpdate($request);

            if ($result['status'] === 'invalid') {
                return response()->json([
                    'status' => 'error',
                    'errors' => $result['errors']
                ], 422);
            }

            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $penalty = $this->userService->processPenalty($request, $penaltyId);
            return $this->jsonResponse('success', 'Penalty updated successfully', 200, ['penalty' => $penalty, 'action_performer_role' => $request->user()->role]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The penalty could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while processing the penalty. Please try again later.', 500);
        }
    }

    public function cancelPenalty(Request $request, $borrowerId, $penaltyId)
    {
        try {
            $penalty = Penalty::findOrFail($penaltyId);

            $penalty->status = PenaltyStatus::CANCELLED;
            $penalty->cancelled_at = now();
            $penalty->save();

            $borrower = User::findOrFail($borrowerId);
            $this->userService->resetToActiveIfClear($borrower);

            ActivityLog::create([
                'action' => 'cancelled_penalty',
                'user_id' => $request->user()->id,
                'entity_type' => 'Penalty',
                'entity_id' => $penalty->id,
                'details' => 'Cancelled penalty ID: ' . $penalty->id . ' for borrower ID: ' . $borrowerId,
            ]);

            return $this->jsonResponse('success', 'Penalty cancelled successfully', 200, ['penalty' => $penalty, 'action_performer_role' => $request->user()->role]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The penalty could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while cancelling the penalty. Please try again later.', 500);
        }
    }
}
