<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\BookCopy;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Services\BorrowService;
use App\Policies\BorrowPolicy;
use App\Policies\ClearancePolicy;
use App\Services\ClearanceService;
use App\Models\Clearance; // Add this import

class ClearanceController extends Controller
{
    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }

    public function validateClearanceRequest(Request $request)
    {
        $requestorId = $request->input('requestor_id');
        $userToBeClearedId = $request->input('user_id'); 

        $result = ClearancePolicy::canRequestClearance($requestorId, $userToBeClearedId);

        switch ($result['result']) {
            case 'success': 
                return $this->jsonResponse('success', 'Clearance can be requested', 200, [
                    'borrower_fullname' => $result['borrower_fullname'] ?? null
                ]);
            case 'not_found': return $this->jsonResponse('not_found', $result['message'], 404);
            case 'invalid_input': return $this->jsonResponse('invalid_input', $result['message'], 422, ['errors' => $result['errors']]);
            case 'business_rule_violation': return $this->jsonResponse('business_rule_violation', $result['message'], 400);
            default: return $this->jsonResponse('error', 'Unknown error while requesting clearance', 500);
        }
    }

    public function performClearanceRequest(Request $request)
    {
        try {
            $requestorId = $request->input('requestor_id');
            $userToBeClearedId = $request->input('user_id');
            
            $result = ClearancePolicy::canRequestClearance($requestorId, $userToBeClearedId);

            if ($result['result'] !== 'success') {
                return $this->jsonResponse('error', $result['message'], 400);
            }

            // Use the ID that was validated (userToBeClearedId might be null if student is requesting for self)
            // If userToBeClearedId is null, it means requestor is the user.
            $targetUserId = $userToBeClearedId ?? $requestorId;

            $clearance = $this->clearanceService->createClearanceRequest($targetUserId, $requestorId);

            return $this->jsonResponse('success', 'Clearance requested successfully', 201, ['clearance' => $clearance]);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while requesting clearance. Please try again later.', 500);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The user could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while requesting clearance. Please try again later.', 500);
        }
    }
   
    public function approveClearance(Request $request, $clearanceId)
    {
        try {
            $result = ClearancePolicy::canPerformClearance($request->user());
            if ($result['result'] !== 'success') {
                return $this->jsonResponse('error', 'Unauthorized to approve clearance', 403);
            }

            // Find the clearance first to get the student/user
            $clearanceRequest = Clearance::with('user')->findOrFail($clearanceId);

            // Check if the STUDENT can be cleared (not the librarian)
            $result = ClearancePolicy::canBeCleared($clearanceRequest->user);
            if ($result['result'] !== 'success') {
                return $this->jsonResponse('business_rule_violation', $result['message'], 400);
            }
            
            // Pass the ID to the service
            $clearance = $this->clearanceService->approveClearance($clearanceId, $request->user()->id);

            return $this->jsonResponse('success', 'Clearance approved successfully', 200, ['clearance' => $clearance]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The clearance request could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while approving clearance. Please try again later.', 500);
        }
    }

}