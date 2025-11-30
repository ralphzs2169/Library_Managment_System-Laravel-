<?php

namespace App\Http\Controllers;

use App\Enums\PenaltyStatus;
use App\Models\BorrowTransaction;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StaffDashboardController extends Controller
{
     public function membersList(Request $request)
    {
        $query = User::with(['students.department', 'teachers.department'])
            ->whereIn('role', ['student', 'teacher']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('middle_initial', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                  ->orWhereHas('students', function ($q) use ($search) {
                      $q->where('student_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teachers', function ($q) use ($search) {
                      $q->where('employee_number', 'like', "%{$search}%");
                  });
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply stabtus filter
        if ($request->filled('status')) {
            $query->where('library_status', $request->status);
        }

        // Default sort: active first, then suspended, then cleared, then firstname descending
        $query->orderByRaw("
            FIELD(library_status, 'active', 'suspended', 'cleared')
        ")->orderBy('firstname', 'asc');

        $users = $query->paginate(20)->withQueryString();

        // If AJAX request, return only the table partial
        if ($request->ajax()) {
            return view('partials.staff.members-table', compact('users'))->render();
        }

        return view('pages.staff.dashboard', compact('users'));
    }

    public function activeBorrowsList(Request $request)
    {
        $query = BorrowTransaction::with(['bookCopy.book.author', 'user.students.department', 'user.teachers.department'])
            ->whereNull('returned_at')
            ->whereIn('status', ['borrowed', 'overdue']);

        // Search filter
       // In your activeBorrowsList method within the search filter block:

    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            
            // --- Custom logic to handle "Copy#" prefix ---
            $copySearch = null;
            // Check if the search string starts with 'Copy#' (case-insensitive)
            if (Str::startsWith(strtolower($search), 'copy#')) {
                // Strip 'Copy#' and trim any leading/trailing spaces left by the user
                $copySearch = trim(substr($search, 5)); 
            }
            
            // A. Search by Borrower Name (Standard logic)
            $q->whereHas('user', function ($uq) use ($search) {
                $uq->where('firstname', 'like', "%{$search}%")
                ->orWhere('lastname', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"]);
            });
            
            // B. Search by Book Title (Standard logic)
            $q->orWhereHas('bookCopy.book', function ($bq) use ($search) {
                $bq->where('title', 'like', "%{$search}%");
            });

            // C. Search by Copy Number (CONDITIONAL Logic)
            if ($copySearch) {
                // If the user typed "Copy#", we use the stripped number
                $q->orWhereHas('bookCopy', function ($bq) use ($copySearch) {
                    // Search only for the stripped number
                    $bq->where('copy_number', 'like', "%{$copySearch}%");
                });
            } else {
                // If the user DID NOT type "Copy#", they are searching for a copy number 
                // that might also match a title/name, so we use the whole string.
                $q->orWhereHas('bookCopy', function ($bq) use ($search) {
                    $bq->where('copy_number', 'like', "%{$search}%");
                });
            }

            // D. Search by ID/Employee Number (Standard logic)
            $q->orWhereHas('user.students', function ($uq) use ($search) {
                $uq->where('student_number', 'like', "%{$search}%");
            });
            $q->orWhereHas('user.teachers', function ($uq) use ($search) {
                $uq->where('employee_number', 'like', "%{$search}%");
            });
        });
    }
        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('user', function ($uq) use ($request) {
                $uq->where('role', $request->role);
            });
        }

        // Status filter (support 'due_soon')
        if ($request->filled('status')) {
            if ($request->status === 'due_soon') {
                // Filter for borrowed and due soon
                $reminderDays = config('settings.notifications.reminder_days_before_due', 3);
                $query->where('status', 'borrowed')
                    ->whereRaw('DATEDIFF(due_at, NOW()) <= ?', [$reminderDays])
                    ->whereRaw('DATEDIFF(due_at, NOW()) >= 0');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'due_asc':
                    $query->orderBy('due_at', 'asc');
                    break;
                case 'due_desc':
                    $query->orderBy('due_at', 'desc');
                    break;
                case 'title_asc':
                    $query->join('book_copies', 'borrow_transactions.book_copy_id', '=', 'book_copies.id')
                          ->join('books', 'book_copies.book_id', '=', 'books.id')
                          ->orderBy('books.title', 'asc');
                    break;
                case 'borrower_asc':
                    $query->join('users', 'borrow_transactions.user_id', '=', 'users.id')
                          ->orderBy('users.firstname', 'asc')
                          ->orderBy('users.lastname', 'asc');
                    break;
                default:
                    $query->orderBy('due_at', 'asc');
            }
        } else {
            $query->orderBy('due_at', 'asc');
        }

        $activeBorrows = $query->paginate(20)->withQueryString();

        $today = now()->startOfDay();

        $activeBorrows->transform(function ($borrow) use ($today) {
            $dueDate = Carbon::parse($borrow->due_at)->startOfDay();
            $isOverdue = $dueDate->lt($today);

            if ($isOverdue) {
                $borrow->days_overdue = $dueDate->diffInDays($today);
                $borrow->days_until_due = 0;
            } else {
                $borrow->days_overdue = null;
                $borrow->days_until_due = $today->diffInDays($dueDate);
            }

            $borrow->status = $isOverdue ? 'overdue' : 'borrowed';
            return $borrow;
        });

        Log::info('Active Borrows Retrieved: ', ['count' => $activeBorrows]);
        if ($request->ajax()) {
            return view('partials.staff.active-borrows.table', compact('activeBorrows'))->render();
        }

        return view('pages.staff.active-borrows', compact('activeBorrows'));
    }

    public function unpaidPenaltiesList(Request $request)
    {
        $query = BorrowTransaction::with(['bookCopy.book.author', 'penalties.payments', 'user.students.department', 'user.teachers.department'])
            ->whereHas('penalties', fn($q) => $q->whereIn('status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID]));

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('middle_initial', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"]);
                });
                $q->orWhereHas('user.students', function ($uq) use ($search) {
                    $uq->where('student_number', 'like', "%{$search}%");
                });
                $q->orWhereHas('user.teachers', function ($uq) use ($search) {
                    $uq->where('employee_number', 'like', "%{$search}%");
                });
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('user', function ($uq) use ($request) {
                $uq->where('role', $request->role);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get all transactions with active penalties and flatten them
        $transactionsWithUnpaidPenalties = $query->get()
            ->flatMap(function ($borrowTransaction) {
                return $borrowTransaction->penalties
                    ->whereIn('status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID])
                    ->map(function ($singlePenalty) use ($borrowTransaction) {
                        $transactionCopy = clone $borrowTransaction;
                        unset($transactionCopy->penalties);

                        $singlePenalty->remaining_amount = $singlePenalty->status === PenaltyStatus::PARTIALLY_PAID
                            ? (float) $singlePenalty->amount - (float) $singlePenalty->payments->sum(fn($payment) => (float) $payment->amount)
                            : (float) $singlePenalty->amount;

                        $transactionCopy->penalty = $singlePenalty;

                        return $transactionCopy;
                    });
            });

        // Paginate the flattened collection
        $page = $request->input('page', 1);
        $perPage = 20;
        $paginatedPenalties = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactionsWithUnpaidPenalties->forPage($page, $perPage),
            $transactionsWithUnpaidPenalties->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            return view('partials.staff.unpaid-penalties.table', ['unpaidPenalties' => $paginatedPenalties])->render();
        }

        return view('pages.staff.unpaid-penalties', ['unpaidPenalties' => $paginatedPenalties]);
    }

   public function queueReservationsList(Request $request)
{
    // 1. Initial Query and Filters (Your original code here, which is fine)
    $query = \App\Models\Reservation::with(['user.students.department', 'user.teachers.department', 'book.author'])
        ->whereIn('status', [\App\Enums\ReservationStatus::PENDING, \App\Enums\ReservationStatus::READY_FOR_PICKUP]);


    $query->orderBy('created_at', 'asc');

    // 3. Get ALL sorted results for accurate queue calculation
    $reservations = $query->get();

    $pendingReservations = $reservations->where('status', \App\Enums\ReservationStatus::PENDING);

    $positionsByBook = $pendingReservations
        ->groupBy('book_id')
        ->map(fn($reservationsForBook) => $reservationsForBook->pluck('id')->flip());

    $reservations->each(function ($reservation) use ($positionsByBook) {
        if ($reservation->status === \App\Enums\ReservationStatus::PENDING) {
            if (isset($positionsByBook[$reservation->book_id][$reservation->id])) {
                $reservation->queue_position = $positionsByBook[$reservation->book_id][$reservation->id] + 1;
            } else {
                 $reservation->queue_position = 1; // Should not happen if query/filter is correct
            }
            $reservation->date_expired = null;
        } else { // READY_FOR_PICKUP
            $reservation->date_expired = $reservation->pickup_deadline;
        }
    });

    Log::info('Queue Reservations Retrieved: ', ['count' => $reservations]);
    // 5. Manual Pagination of the fully calculated collection (Your existing logic)
    $page = $request->input('page', 1);
    $perPage = 20;
    
    // Note: The manual pagination is inefficient but works with your in-memory calculation
    $paginatedReservations = new \Illuminate\Pagination\LengthAwarePaginator(
        $reservations->forPage($page, $perPage),
        $reservations->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );
    
    if ($request->ajax()) {
        return view('partials.staff.queue-reservations.table', ['queueReservations' => $paginatedReservations])->render();
    }

    return view('pages.staff.queue-reservations', ['queueReservations' => $paginatedReservations]);
}
}