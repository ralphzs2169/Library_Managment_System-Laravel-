<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BorrowTransaction;
use App\Models\Book;
use App\Services\UserService;
use App\Models\Semester;
use App\Models\BookCopy;
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
        $data = $this->userService->getBorrowerDetails($userId);

        // Return both the user model (for server rendering) and explicit collections for API clients
        return response()->json([
            'user' => $data['user'], // model (will be serialized)
            'due_reminder_threshold' => $data['due_reminder_threshold'] ?? null,
            'borrow_duration' => $data['borrow_duration'] ?? null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function borrowBook(Request $request)
    {
        if ($request->validate_only) {
            $result = $this->userService->validateBorrowRequest($request);

            if ($result['status'] === 'invalid') {
                // business rule failure with message
                if (isset($result['message'])) {
                    return $this->jsonResponse('invalid', $result['message'], 422);
                }

                // validation errors
                return response()->json([
                    'status' => 'error',
                    'errors' => $result['errors'] ?? []
                ], 422);
            }

            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $bookCopy = BookCopy::findOrFail($request->input('book_copy_id'));
            $transaction = $this->userService->borrowBook($request, $bookCopy);
            return $this->jsonResponse('success', 'Book borrowed successfully', 201, ['transaction' => $transaction]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The borrower or book copy could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while borrowing the book. Please try again later.', 500);
        }
    }

    public function returnBook(Request $request)
    {
        if ($request->validate_only) {
            $result = $this->userService->validateReturnRequest($request);

            if ($result['status'] === 'invalid') {
                return $this->jsonResponse('invalid', $result['message'], 422);
            }

            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $transaction = $this->userService->returnBook($request);
            return $this->jsonResponse('success', 'Book returned successfully', 200, ['transaction' => $transaction]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The book or transaction could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while returning the book. Please try again later.', 500);
        }
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
}
