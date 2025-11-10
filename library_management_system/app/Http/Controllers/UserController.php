<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BorrowTransaction;
use App\Models\Book;
use App\Services\UserService;
use App\Models\Semester;

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
        $user = User::with([
            'students.department',
            'teachers.department',
            'borrowTransactions' => function ($query) {
                $query->with(['bookCopy.book.author'])
                      ->orderBy('borrowed_at', 'desc');
            }
        ])->findOrFail($userId);
        
        $daysBeforeDue = config('settings.notifications.reminder_days_before_due', 3);
        $user->full_name = $user->full_name; 

        return response()->json([
            'user' => $user,
            'days_before_due' => $daysBeforeDue,
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
            // Get the borrower to check their role
            $borrower = User::findOrFail($request->borrower_id);
            
            // 1. Check for penalties first
            $hasPenalty = BorrowTransaction::where('user_id', $borrower->id)
                ->whereIn('status', ['overdue', 'lost', 'damaged'])
                ->exists();
            if ($hasPenalty) {
                return $this->jsonResponse('invalid', 'Borrowing suspended due to an outstanding penalty.', 422);
            }

            // 2. If student, check active semester
            if ($borrower->role === 'student') {
                $hasActive = Semester::where('status', 'active')->exists();
                if (!$hasActive) {
                    return $this->jsonResponse('invalid', 'No active semester found. Students can only borrow during an active semester.', 422);
                }

                // 3. Check borrow limit
                $borrowTransactions = BorrowTransaction::where('user_id', $borrower->id)
                    ->where('status', 'borrowed')
                    ->count();  

                if ($borrowTransactions >= 3) {
                    return $this->jsonResponse('invalid', 'Borrower has reached the maximum limit of 3 borrowed books.', 422);
                }
            }


            $request->validate([
                'book_copy_id' => 'required|exists:book_copies,id',
                'borrower_id' => 'required|exists:users,id',
                'due_date' => 'required|date|after:today',
                'semester_id' => 'nullable|exists:semesters,id',
            ]);



            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $transaction = $this->userService->borrowBook($request);
            return $this->jsonResponse('success', 'Book borrowed successfully', 201, ['transaction' => $transaction]);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Failed to borrow book: ' . $e->getMessage(), 500);
        }
    }

    public function returnBook(Request $request)
    {
        $transaction = BorrowTransaction::where('user_id', $request->user()->id)
            ->where('book_id', $request->input('book_id'))
            ->whereNull('returned_at')
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'No active borrow transaction found'], 404);
        }

        $transaction->markAsReturned();

        return response()->json(['message' => 'Book returned successfully'], 200);
    }
}
