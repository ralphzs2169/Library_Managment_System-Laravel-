<?php

namespace App\Http\Controllers;

use App\Enums\LibraryStatus;
use App\Enums\PenaltyStatus;
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

     public function updatePenalty(Request $request, $penaltyId)
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
            $penalty = $this->userService->updatePenalty($request, $penaltyId);
            return $this->jsonResponse('success', 'Penalty updated successfully', 200, ['penalty' => $penalty]);
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
            $penalty->save();

            $borrower = User::findOrFail($borrowerId);
            $this->userService->resetToActiveIfClear($borrower);

            return $this->jsonResponse('success', 'Penalty cancelled successfully', 200, ['penalty' => $penalty]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The penalty could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while cancelling the penalty. Please try again later.', 500);
        }
    }
}
