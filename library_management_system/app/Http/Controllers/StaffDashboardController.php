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

                // Search by Borrower Name
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('middle_initial', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(firstname, ' ', middle_initial, '.', ' ', lastname) LIKE ?", ["%{$search}%"])
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

        // Apply sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('firstname', 'desc')->orderBy('lastname', 'desc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderByRaw("FIELD(library_status, 'active', 'suspended', 'cleared')")
                          ->orderBy('firstname', 'asc');
            }
        } else {
            // Default sort: active first, then suspended, then cleared, then firstname ascending
            $query->orderByRaw("FIELD(library_status, 'active', 'suspended', 'cleared')")
                  ->orderBy('firstname', 'asc');
        }

        $users = $query->paginate(20)->withQueryString();

        // If AJAX request, return only the table partial
        if ($request->ajax()) {
            $html = view('partials.staff.members.table', compact('users'))->render();
            return response()->json([
                'html' => $html,
                'count' => $users->total()
            ]);
        }

        return view('pages.staff.dashboard', compact('users'));
    }

    public function activeBorrowsList(Request $request)
    {
        $query = BorrowTransaction::with(['bookCopy.book.author', 'user.students.department', 'user.teachers.department'])
            ->whereNull('returned_at')
            ->whereIn('borrow_transactions.status', ['borrowed', 'overdue']); // <-- Fix ambiguous column

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // A. Search by Borrower Name 
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(firstname, ' ', middle_initial, '.', ' ', lastname) LIKE ?", ["%{$search}%"]);
                });
                
                // B. Search by Book Title
                $q->orWhereHas('bookCopy.book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%");
                });

                // D. Search by ID/Employee Number 
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
                $reminderDays = config('settings.notifications.reminder_days_before_due', 3);
                $query->where('borrow_transactions.status', 'borrowed')
                    ->whereRaw('DATEDIFF(due_at, NOW()) <= ?', [$reminderDays])
                    ->whereRaw('DATEDIFF(due_at, NOW()) >= 0');
            } else {
                $query->where('borrow_transactions.status', $request->status);
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
            $html = view('partials.staff.active-borrows.table', [
                'activeBorrows' => $activeBorrows,
            ])->render();
            
            return response()->json([
                'html' => $html,
                'count' => $activeBorrows->total() // Return the count separately
            ]);
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
                // A. Search by Borrower Name
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('middle_initial', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(firstname, ' ', middle_initial, '.', ' ', lastname) LIKE ?", ["%{$search}%"]);
                });

                // B. Search by Book Title
                 $q->orWhereHas('bookCopy.book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%");
                });

                // C. Search by ID/Employee Number
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

        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'amount_desc':
                    $query->orderByDesc(
                        BorrowTransaction::selectRaw('MAX(amount)')
                            ->from('penalties')
                            ->whereColumn('penalties.borrow_transaction_id', 'borrow_transactions.id')
                    );
                    break;
                case 'amount_asc':
                    $query->orderBy(
                        BorrowTransaction::selectRaw('MIN(amount)')
                            ->from('penalties')
                            ->whereColumn('penalties.borrow_transaction_id', 'borrow_transactions.id')
                    );
                    break;
                case 'date_desc':
                    $query->orderByDesc(
                        BorrowTransaction::selectRaw('MAX(created_at)')
                            ->from('penalties')
                            ->whereColumn('penalties.borrow_transaction_id', 'borrow_transactions.id')
                    );
                    break;
                case 'date_asc':
                    $query->orderBy(
                        BorrowTransaction::selectRaw('MIN(created_at)')
                            ->from('penalties')
                            ->whereColumn('penalties.borrow_transaction_id', 'borrow_transactions.id')
                    );
                    break;
                default:
                    // Default sort: amount descending
                    $query->orderByDesc(
                        BorrowTransaction::selectRaw('MAX(amount)')
                            ->from('penalties')
                            ->whereColumn('penalties.borrow_transaction_id', 'borrow_transactions.id')
                    );
            }
        } else {
            // Default sort: amount descending
            $query->orderByDesc(
                BorrowTransaction::selectRaw('MAX(amount)')
                    ->from('penalties')
                    ->whereColumn('penalties.borrow_transaction_id', 'borrow_transactions.id')
            );
        }

        // Get all transactions with active penalties and flatten them
        $transactionsWithUnpaidPenalties = $query->get()
            ->flatMap(function ($borrowTransaction) use ($request) {
                $penalties = $borrowTransaction->penalties
                    ->whereIn('status', [PenaltyStatus::UNPAID, PenaltyStatus::PARTIALLY_PAID]);

                // Apply penalty type filter at the penalty level (not just at the transaction level)
                if ($request->filled('type')) {
                    $penalties = $penalties->where('type', $request->type);
                }
                // Apply status filter at the penalty level
                if ($request->filled('status')) {
                    $penalties = $penalties->where('status', $request->status);
                }

                return $penalties->map(function ($singlePenalty) use ($borrowTransaction) {
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
            $html = view('partials.staff.unpaid-penalties.table', ['unpaidPenalties' => $paginatedPenalties])->render();
            return response()->json([
                'html' => $html,
                'count' => $paginatedPenalties->total()
            ]);
        }

        return view('pages.staff.unpaid-penalties', ['unpaidPenalties' => $paginatedPenalties]);
    }

   public function queueReservationsList(Request $request)
{
    $query = \App\Models\Reservation::with(['borrower.students.department', 'borrower.teachers.department', 'book.author'])
        ->whereIn('status', [\App\Enums\ReservationStatus::PENDING, \App\Enums\ReservationStatus::READY_FOR_PICKUP]);

    // Apply search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('borrower', function ($uq) use ($search) {
                $uq->where('firstname', 'like', "%{$search}%")
                   ->orWhere('lastname', 'like', "%{$search}%")
                   ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                   ->orWhereRaw("CONCAT(firstname, ' ', middle_initial, '.', ' ', lastname) LIKE ?", ["%{$search}%"]);

            });
            $q->orWhereHas('borrower.students', function ($uq) use ($search) {
                $uq->where('student_number', 'like', "%{$search}%");
            });
            $q->orWhereHas('borrower.teachers', function ($uq) use ($search) {
                $uq->where('employee_number', 'like', "%{$search}%");
            });
            $q->orWhereHas('book', function ($bq) use ($search) {
                $bq->where('title', 'like', "%{$search}%");
            });
        });
    }

    // Role filter
    if ($request->filled('role')) {
        $query->whereHas('borrower', function ($uq) use ($request) {
            $uq->where('role', $request->role);
        });
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Sort filter
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'position_asc':
                $query->orderByRaw("FIELD(status, 'pending', 'ready_for_pickup'), created_at ASC");
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'deadline_asc':
                $query->orderBy('pickup_start_date', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'asc');
        }
    } else {
        $query->orderBy('created_at', 'asc');
    }

    // Get ALL sorted results for accurate queue calculation
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
                $reservation->queue_position = 1;
            }
            $reservation->pickup_deadline_date = null;
        } else { // READY_FOR_PICKUP
            $reservation->pickup_deadline_date = $reservation->pickup_deadline;
        }
    });

    Log::info('Queue Reservations Retrieved: ', ['count' => $reservations]);
    // Manual Pagination
    $page = $request->input('page', 1);
    $perPage = 20;
    $paginatedReservations = new \Illuminate\Pagination\LengthAwarePaginator(
        $reservations->forPage($page, $perPage),
        $reservations->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );
    Log::info('Paginated Queue Reservations: ', ['count' => $paginatedReservations]);
    if ($request->ajax()) {
        $html = view('partials.staff.queue-reservations.table', ['queueReservations' => $paginatedReservations])->render();
        return response()->json([
            'html' => $html,
            'count' => $paginatedReservations->total()
        ]);
    }

    return view('pages.staff.queue-reservations', ['queueReservations' => $paginatedReservations]);
}
}