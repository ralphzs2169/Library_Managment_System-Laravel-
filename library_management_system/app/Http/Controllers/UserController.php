<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BorrowTransaction;
use App\Models\Book;
use App\Services\UserService;
use App\Services\ActivityLogger;

class UserController extends Controller
{
    protected $userService;
    protected $activityLogger;

    public function __construct(UserService $userService, ActivityLogger $activityLogger)
    {
        $this->userService = $userService;
        $this->activityLogger = $activityLogger;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function borrowerDetails(Request $request, $userId)
    {
        // Explicitly differentiate the requestor (Staff/Librarian) from the user being viewed (Student)
        $requestorId = $request->user()->id;
        $targetUserId = $userId;

        // Service methods typically expect the Target ID first, then the Actor ID
        $data = $this->userService->getBorrowerDetails($targetUserId, $requestorId);

        // Return both the user model (for server rendering) and explicit collections for API clients
        return response()->json(['borrower' => $data['borrower'], 'action_performer' => $request->user()]);
    }

     public function suspendUser(Request $request, $userId)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $user = User::findOrFail($userId);

        $user->library_status = 'suspended';
        $user->save();

        $this->activityLogger->logSuspension($user, $request->reason);

        return response()->json(['success' => true, 'message' => 'User suspended successfully.']);
    }

    public function liftSuspension(Request $request, $userId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $user = User::findOrFail($userId);
        $user->library_status = 'active';
        $user->save();

        $reason = $request->reason ? " Reason: {$request->reason}" : "";

        $this->activityLogger->logLiftSuspension($user, $request->reason);

        return response()->json(['success' => true, 'message' => 'Suspension lifted successfully.']);
    }



}
