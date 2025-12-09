<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BorrowTransaction;
use Carbon\Carbon;
use App\Enums\PenaltyStatus;
use App\Models\Reservation;
use App\Models\Semester;
use App\Enums\ReservationStatus;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class LibrarianSectionsController extends Controller
{

    public function dashboard(Request $request)
    {
        // Return the librarian section dashboard view. Adjust view name if your project uses a different path.
        return view('pages.librarian.dashboard');
    }
    public function borrowingRecords(Request $request)
    {
         $query = BorrowTransaction::with(['semester', 'bookCopy.book.author', 'bookCopy.book.genre.category', 'user.students.department', 'user.teachers.department']);

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

        // Status filter (support borrowed, due_soon, overdue, returned, returned_late)
        if ($request->has('status') && $request->input('status') !== null && $request->input('status') !== '') {
            $today = now()->startOfDay();
            $reminderDays = (int) config('settings.notifications.reminder_days_before_due', 3);

            if ($request->status === 'overdue') {
                $query->where('borrow_transactions.status', 'borrowed')
                      ->whereDate('due_at', '<', $today);
            } else if ($request->status === 'due_soon') {
                $query->where('borrow_transactions.status', 'borrowed')
                      ->whereDate('due_at', '>=', $today)
                      ->whereDate('due_at', '<=', $today->copy()->addDays($reminderDays));
            } else if ($request->status === 'borrowed') {
                $query->where('borrow_transactions.status', 'borrowed')
                      ->whereDate('due_at', '>', $today->copy()->addDays($reminderDays));
            } else if ($request->status === 'returned_late') {
                $query->whereNotNull('returned_at')
                      ->whereRaw('DATE(returned_at) > DATE(due_at)');
            } else if ($request->status === 'returned') {
                $query->whereNotNull('returned_at')
                      ->whereRaw('DATE(returned_at) <= DATE(due_at)');
            }
        }


        if ($request->filled('semester')) {
            $query->where('semester_id', $request->input('semester'));
        }

        // Semester filter (default to active semester if not set)
        $semesterId = $request->input('semester');
        if ($semesterId === null || $semesterId === '') {
            $activeSemester = \App\Models\Semester::where('status', 'active')->first();
            if ($activeSemester) {
                $semesterId = $activeSemester->id;
            }
        }
        // FIX: Use whereRaw to avoid join ambiguity and always filter correctly
        if ($semesterId) {
            $query->whereRaw('borrow_transactions.semester_id = ?', [$semesterId]);
        }

        // Sort filter with default priority order
        $reminderDays = (int) config('settings.notifications.reminder_days_before_due', 3);
        $defaultSort = "
            CASE 
                WHEN borrow_transactions.status = 'borrowed' AND due_at < NOW() THEN 0
                WHEN borrow_transactions.status = 'borrowed' AND due_at >= NOW() AND due_at <= DATE_ADD(NOW(), INTERVAL {$reminderDays} DAY) THEN 1
                WHEN borrow_transactions.status = 'borrowed' THEN 2
                WHEN returned_at > due_at THEN 3
                WHEN returned_at <= due_at THEN 4
                ELSE 5
            END
        ";

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
                case '':
                    // Priority order
                    $query->orderByRaw($defaultSort)->orderBy('due_at', 'asc');
                    break;
                default:
                    $query->orderBy('id', 'asc');
            }
        } else {
            $query->orderByRaw($defaultSort)->orderBy('due_at', 'asc');
        }

        $borrowTransactions = $query->paginate(20)->withQueryString();

        $today = now()->startOfDay();

        $borrowTransactions->transform(function ($borrow) use ($today) {
            $dueDate = Carbon::parse($borrow->due_at)->startOfDay();
            $isOverdue = $dueDate->lt($today);

            if ($isOverdue) {
                $borrow->days_overdue = $dueDate->diffInDays($today);
                $borrow->days_until_due = 0;
            } else {
                $borrow->days_overdue = null;
                $borrow->days_until_due = $today->diffInDays($dueDate);
            }

            $borrow->status = $isOverdue ? 'overdue' :  $borrow->status;
            return $borrow;
        });

        if ($request->ajax()) {
            $html = view('partials.librarian.circulation-records.borrowing-records-table', [
                'borrowTransactions' => $borrowTransactions,
            ])->render();
            
            return response()->json([
                'html' => $html,
                'count' => $borrowTransactions->total() // Return the count separately
            ]);
        }

         $semesters = Semester::orderBy('start_date', 'desc')->get();
        $activeSemesterId = Semester::where('status', 'active')?->value('id');

        return view('pages.librarian.circulation-records.borrowing-records', 
                   ['borrowTransactions' => $borrowTransactions, 
                    'totalBorrowRecords' =>$borrowTransactions->total(),
                    'semesters' => $semesters,
                    'activeSemesterId' => $activeSemesterId ]);
    }

     public function reservationRecords(Request $request)
    {
        $query = Reservation::with([
            'borrower.students.department',
            'bookCopy',
            'borrower.teachers.department',
            'book.author',
            'createdBy' 
        ]);

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

        // Semester filter (default to active semester if not set)
        $requestedSemesterId = $request->input('semester');
        $applySemesterFilter = false;
        if ($requestedSemesterId !== null && $requestedSemesterId !== '' && $requestedSemesterId !== 'all') {
            $applySemesterFilter = true;
        } else if ($requestedSemesterId === null || $requestedSemesterId === '') {
            $activeSemester = Semester::where('status', 'active')->first();
            if ($activeSemester) {
                $requestedSemesterId = $activeSemester->id;
                $applySemesterFilter = true;
            }
        }
        if ($applySemesterFilter && $requestedSemesterId) {
            $query->where('semester_id', $requestedSemesterId);
        }


        // Sort filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'position_asc':
                    // Manual sort by queue_position after fetching
                    // We'll sort after fetching all reservations below
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'deadline_asc':
                    // Prioritize ready_for_pickup, then sort by pickup_deadline ascending, then others
                    $query->orderByRaw("
                        FIELD(status, 'ready_for_pickup', 'pending', 'completed', 'expired', 'cancelled')
                    ")->orderBy('pickup_start_date', 'asc')->orderBy('created_at', 'asc');
                    break;
                default:
                    // Custom default sort: ready_for_pickup > pending > completed > expired > cancelled
                    $query->orderByRaw("
                        FIELD(status, 'ready_for_pickup', 'pending', 'completed', 'expired', 'cancelled')
                    ")->orderBy('created_at', 'asc');
            }
        } else {
            // Custom default sort: ready_for_pickup > pending > completed > expired > cancelled
            $query->orderByRaw("
                FIELD(status, 'ready_for_pickup', 'pending', 'completed', 'expired', 'cancelled')
            ")->orderBy('created_at', 'asc');
        }

        // Get ALL sorted results for accurate queue calculation
        $reservations = $query->get();

        $pendingReservations = $reservations->where('status', ReservationStatus::PENDING);

        $positionsByBook = $pendingReservations
            ->groupBy('book_id')
            ->map(fn($reservationsForBook) => $reservationsForBook->pluck('id')->flip());

        $reservations->each(function ($reservation) use ($positionsByBook) {
            if ($reservation->status === ReservationStatus::PENDING) {
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

        // Manual sort by queue_position if requested
        if ($request->filled('sort') && $request->sort === 'position_asc') {
            $reservations = $reservations->sort(function ($a, $b) {
                // Pending reservations: sort by queue_position asc, then created_at asc
                if ($a->status === ReservationStatus::PENDING && $b->status === ReservationStatus::PENDING) {
                    return $a->queue_position <=> $b->queue_position ?: $a->created_at <=> $b->created_at;
                }
                // Pending comes before others
                if ($a->status === ReservationStatus::PENDING) return -1;
                if ($b->status === ReservationStatus::PENDING) return 1;
                // Otherwise, sort by created_at asc
                return $a->created_at <=> $b->created_at;
            })->values();
        }

        // Log::info('Queue Reservations Retrieved: ', ['count' => $reservations]);
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
        // Log::info('Paginated Queue Reservations: ', ['count' => $paginatedReservations]);
        if ($request->ajax()) {
            $html = view('partials.librarian.circulation-records.reservation-records-table', ['reservations' => $paginatedReservations])->render();
            return response()->json([
                'html' => $html,
                'count' => $paginatedReservations->total()
            ]);
        }

        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $activeSemesterId = Semester::where('status', 'active')?->value('id');
      
        return view('pages.librarian.circulation-records.reservation-records', 
                ['reservations' => $paginatedReservations, 
                 'semesters' => $semesters, 
                 'activeSemesterId' => $activeSemesterId,
                 'totalReservationRecords' => $paginatedReservations->total()]);
    }


    public function penaltyRecords(Request $request)
    {
        $query = BorrowTransaction::with(['bookCopy.book.author', 'penalties.payments', 'user.students.department', 'user.teachers.department']);

        // Search filter (Borrower name, book title, id/employee)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('firstname', 'like', "%{$search}%")
                       ->orWhere('lastname', 'like', "%{$search}%")
                       ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                       ->orWhereRaw("CONCAT(firstname, ' ', middle_initial, '.', ' ', lastname) LIKE ?", ["%{$search}%"]);
                });

                $q->orWhereHas('bookCopy.book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%");
                });

                $q->orWhereHas('user.students', function ($uq) use ($search) {
                    $uq->where('student_number', 'like', "%{$search}%");
                });
                $q->orWhereHas('user.teachers', function ($uq) use ($search) {
                    $uq->where('employee_number', 'like', "%{$search}%");
                });
            });
        }

        // Role filter (student/teacher)
        if ($request->filled('role')) {
            $query->whereHas('user', function ($uq) use ($request) {
                $uq->where('role', $request->role);
            });
        }

        // Semester filter (default to active semester if not set)
        $requestedSemesterId = $request->input('semester');
        $applySemesterFilter = false;
        if ($requestedSemesterId !== null && $requestedSemesterId !== '' && $requestedSemesterId !== 'all') {
            $applySemesterFilter = true;
        } else if ($requestedSemesterId === null || $requestedSemesterId === '') {
            $activeSemester = Semester::where('status', 'active')->first();
            if ($activeSemester) {
                $requestedSemesterId = $activeSemester->id;
                $applySemesterFilter = true;
            }
        }
        if ($applySemesterFilter && $requestedSemesterId) {
            $query->whereRaw('borrow_transactions.semester_id = ?', [$requestedSemesterId]);
        }

        // Get all transactions and flatten penalties
        $transactions = $query->get();

        $transactionsWithPenalties = $transactions->flatMap(function ($borrowTransaction) use ($request) {
            if ($borrowTransaction->penalties->count()) {
                $penalties = $borrowTransaction->penalties;

                // Apply penalty type filter at the penalty level
                if ($request->filled('type')) {
                    $penalties = $penalties->where('type', $request->type);
                }
                // Apply status filter at the penalty level
                if ($request->filled('status')) {
                    if ($request->status === 'due') {
                        // Only unpaid and partially_paid
                        $penalties = $penalties->whereIn('status', ['unpaid', 'partially_paid']);
                    } else {
                        $penalties = $penalties->where('status', $request->status);
                    }
                }

                if ($penalties->count()) {
                    return $penalties->map(function ($singlePenalty) use ($borrowTransaction) {
                        $transactionCopy = clone $borrowTransaction;
                        unset($transactionCopy->penalties);

                        $singlePenalty->remaining_amount = $singlePenalty->status === PenaltyStatus::PARTIALLY_PAID
                            ? (float) $singlePenalty->amount - (float) $singlePenalty->payments->sum(fn($payment) => (float) $payment->amount)
                            : (float) $singlePenalty->amount;

                        $transactionCopy->penalty = $singlePenalty;

                        return $transactionCopy;
                    });
                }
            }
            return collect([]);
        });

        // --- Move sorting logic here, after $transactionsWithPenalties is defined ---
        $prioritySortUnpaid = "
            CASE 
                WHEN penalties.status = 'unpaid' THEN 0
                WHEN penalties.status = 'partially_paid' THEN 1
                WHEN penalties.status = 'paid' THEN 2
                WHEN penalties.status = 'cancelled' THEN 3
                ELSE 4
            END
        ";
        $prioritySortPartial = "
            CASE 
                WHEN penalties.status = 'partially_paid' THEN 0
                WHEN penalties.status = 'unpaid' THEN 1
                WHEN penalties.status = 'paid' THEN 2
                WHEN penalties.status = 'cancelled' THEN 3
                ELSE 4
            END
        ";

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case '':
                    // Priority: unpaid > partially_paid > paid > cancelled
                    $transactionsWithPenalties = $transactionsWithPenalties->sortBy(function ($item) {
                        $statusOrder = [
                            'unpaid' => 0,
                            'partially_paid' => 1,
                            'paid' => 2,
                            'cancelled' => 3,
                        ];
                        return $statusOrder[$item->penalty->status] ?? 4;
                    })->values();
                    break;
                case 'priority_partial':
                    // Priority: partially_paid > unpaid > paid > cancelled
                    $transactionsWithPenalties = $transactionsWithPenalties->sortBy(function ($item) {
                        $statusOrder = [
                            'partially_paid' => 0,
                            'unpaid' => 1,
                            'paid' => 2,
                            'cancelled' => 3,
                        ];
                        return $statusOrder[$item->penalty->status] ?? 4;
                    })->values();
                    break;
                case 'amount_desc':
                    $transactionsWithPenalties = $transactionsWithPenalties->sortByDesc(function ($item) {
                        return $item->penalty->amount;
                    })->values();
                    break;
                case 'amount_asc':
                    $transactionsWithPenalties = $transactionsWithPenalties->sortBy(function ($item) {
                        return $item->penalty->amount;
                    })->values();
                    break;
                case 'date_desc':
                    $transactionsWithPenalties = $transactionsWithPenalties->sortByDesc(function ($item) {
                        return $item->penalty->created_at;
                    })->values();
                    break;
                case 'date_asc':
                    $transactionsWithPenalties = $transactionsWithPenalties->sortBy(function ($item) {
                        return $item->penalty->created_at;
                    })->values();
                    break;
                default:
                    // Fallback to unpaid first
                    $transactionsWithPenalties = $transactionsWithPenalties->sortBy(function ($item) {
                        $statusOrder = [
                            'unpaid' => 0,
                            'partially_paid' => 1,
                            'paid' => 2,
                            'cancelled' => 3,
                        ];
                        return $statusOrder[$item->penalty->status] ?? 4;
                    })->values();
            }
        } else {
            // Default: unpaid > partially_paid > paid > cancelled
            $transactionsWithPenalties = $transactionsWithPenalties->sortBy(function ($item) {
                $statusOrder = [
                    'unpaid' => 0,
                    'partially_paid' => 1,
                    'paid' => 2,
                    'cancelled' => 3,
                ];
                return $statusOrder[$item->penalty->status] ?? 4;
            })->values();
        }

        // Calculate total flattened count before pagination
        $totalFlattened = $transactionsWithPenalties->count();

        // Paginate the flattened collection
        $page = $request->input('page', 1);
        $perPage = 20;
        $paginatedPenalties = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactionsWithPenalties->forPage($page, $perPage),
            $totalFlattened,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            $html = view('partials.librarian.circulation-records.penalty-records-table', ['penalties' => $paginatedPenalties])->render();
            return response()->json([
                'html' => $html,
                'count' => $totalFlattened
            ]);
        }
            
        Log::info('Total Penalty Records: ', ['count' => $totalFlattened]);
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $activeSemesterId = Semester::where('status', 'active')?->value('id');

        return view('pages.librarian.circulation-records.penalty-records', [
            'penalties' => $paginatedPenalties,
            'totalPenaltyRecords' => $totalFlattened,
            'semesters' => $semesters,
            'activeSemesterId' => $activeSemesterId
        ]);
    }

     public function borrowers(Request $request)
    {
        $query = User::with(['students.department', 'teachers.department'])
            ->whereIn('role', ['student', 'teacher']);

        // Add counts for sorting and filtering
        $query->withCount(['borrowTransactions as active_borrowings_count' => function ($q) {
            $q->whereNull('returned_at');
        }]);

        $query->withCount(['reservations as active_reservations_count' => function ($q) {
            $q->whereIn('status', ['pending', 'ready_for_pickup']);
        }]);

        // Subquery for total fines
        $finesSubquery = \App\Models\Penalty::selectRaw('COALESCE(SUM(amount), 0)')
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->whereHas('borrowTransaction', function ($q) {
                $q->whereColumn('user_id', 'users.id');
            });
        
        $query->select('users.*')->selectSub($finesSubquery, 'total_fines_amount');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(firstname, ' ', middle_initial, '.', ' ', lastname) LIKE ?", ["%{$search}%"]);
                
                // Search by ID
                $q->orWhereHas('students', function ($sq) use ($search) {
                    $sq->where('student_number', 'like', "%{$search}%");
                });
                $q->orWhereHas('teachers', function ($tq) use ($search) {
                    $tq->where('employee_number', 'like', "%{$search}%");
                });
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('library_status', $request->status);
        }

        // Active Borrows Filter
        if ($request->filled('active_borrows')) {
            if ($request->active_borrows === 'has_borrows') {
                $query->whereHas('borrowTransactions', function ($q) {
                    $q->whereNull('returned_at');
                });
            } elseif ($request->active_borrows === 'no_borrows') {
                $query->whereDoesntHave('borrowTransactions', function ($q) {
                    $q->whereNull('returned_at');
                });
            }
        }

        // Reservation Status Filter
        if ($request->filled('reservation_status')) {
            if ($request->reservation_status === 'has_pending') {
                $query->whereHas('reservations', function ($q) {
                    $q->where('status', 'pending');
                });
            } elseif ($request->reservation_status === 'ready_for_pickup') {
                $query->whereHas('reservations', function ($q) {
                    $q->where('status', 'ready_for_pickup');
                });
            } elseif ($request->reservation_status === 'no_reservations') {
                $query->whereDoesntHave('reservations', function ($q) {
                    $q->whereIn('status', ['pending', 'ready_for_pickup']);
                });
            }
        }

        // Fines Filter
        if ($request->filled('fines')) {
            if ($request->fines === 'has_fines') {
                $query->whereHas('borrowTransactions.penalties', function ($q) {
                    $q->whereIn('status', ['unpaid', 'partially_paid']);
                });
            } elseif ($request->fines === 'no_fines') {
                $query->whereDoesntHave('borrowTransactions.penalties', function ($q) {
                    $q->whereIn('status', ['unpaid', 'partially_paid']);
                });
            }
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
                    break;
                case 'borrows_desc':
                    $query->orderBy('active_borrowings_count', 'desc');
                    break;
                case 'reservations_desc':
                    $query->orderBy('active_reservations_count', 'desc');
                    break;
                case 'fines_desc':
                    $query->orderBy('total_fines_amount', 'desc');
                    break;
                case 'latest_activity':
                    $query->orderBy('updated_at', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'asc');
        }

        $borrowers = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            $html = view('partials.librarian.user-management.borrowers-table', [
                'borrowers' => $borrowers,
            ])->render();
            
            return response()->json([
                'html' => $html,
                'count' => $borrowers->total()
            ]);
        }

        return view('pages.librarian.user-management.borrowers', [
            'borrowers' => $borrowers,
            'totalBorrowersCount' => $borrowers->total()
        ]);
    }


    public function personnelAccounts(Request $request)
    {
        $query = User::whereIn('role', ['staff', 'librarian']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"]);
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('firstname', 'desc')->orderBy('lastname', 'desc');
                    break;
                case 'role_asc':
                    $query->orderBy('role', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $personnel = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            $html = view('partials.librarian.user-management.personnel-accounts-table', [
                'personnel' => $personnel,
            ])->render();
            
            return response()->json([
                'html' => $html,
                'count' => $personnel->total()
            ]);
        }

        return view('pages.librarian.user-management.personnel-accounts', [
            'personnel' => $personnel,
            'totalPersonnelCount' => $personnel->total()
        ]);
    }

    public function storePersonnel(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:45',
            'lastname' => 'required|string|max:45',
            'middle_initial' => 'nullable|string|max:1',
            'email' => 'required|string|email|max:255|unique:users',
            'contact_number' => ['nullable', 'string', 'max:13', 'regex:/^(09\d{9}|\+639\d{9})$/'],
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:staff,librarian',
        ]);

        User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'middle_initial' => $validated['middle_initial'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'library_status' => 'active',
        ]);

        return response()->json(['message' => 'Personnel account created successfully.']);
    }
}