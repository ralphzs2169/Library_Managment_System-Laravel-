<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BorrowTransaction;
use App\Models\Book;
use App\Services\UserService;
use App\Models\Semester;
use App\Models\BookCopy;
use App\Policies\BorrowPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Policies\RenewalPolicy;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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


}
