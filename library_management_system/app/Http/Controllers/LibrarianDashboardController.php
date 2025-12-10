<?php

namespace App\Http\Controllers;

use App\Enums\BookCopyStatus;
use App\Enums\ClearanceStatus;
use App\Enums\IssueReportStatus;
use App\Enums\PenaltyStatus;
use App\Enums\ReservationStatus;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BorrowTransaction;
use App\Models\Penalty;
use App\Models\Semester;
use App\Models\User;

class LibrarianDashboardController extends Controller
{
    /**
     * Show librarian dashboard.
     */
    public function index()
    {
        // Get total books (titles)
        $totalBooks = Book::count();

        // Get total active borrowers with currently borrowed books
        $totalActiveBorrowers = User::whereIn('role', ['student', 'teacher'])
            ->whereHas('borrowTransactions', function ($query) {
                $query->whereNull('returned_at');
            })
            ->count();

        // Get currently borrowed copies count
        $totalBorrowedCopies = BookCopy::where('status', BookCopyStatus::BORROWED)->count();

        // get total unpaid fines
        $totalUnpaidFines = Penalty::where('status', 'unpaid')
            ->whereHas('borrowTransaction.user', function ($query) {
                $query->whereIn('role', ['student', 'teacher']);
            })
            ->sum('amount');

        // Get top 4 most borrowed books
        $topBooks = Book::with(['author', 'genre.category'])
            ->withCount('borrowTransactions')
            ->orderBy('borrow_transactions_count', 'desc')
            ->take(4)
            ->get();

        // Get recent activities
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
        
        // Get books due soon (within threshold days)
        $dueSoonThreshold = (int) config('settings.notifications.reminder_days_before_due', 3);
        $today = now()->startOfDay();
        $dueSoon = BorrowTransaction::with(['user', 'bookCopy.book'])
            ->whereNull('returned_at')
            ->whereDate('due_at', '>=', $today)
            ->whereDate('due_at', '<=', $today->copy()->addDays($dueSoonThreshold))
            ->orderBy('due_at', 'asc')
            ->take(5)
            ->get();
           
        $totalCopies = BookCopy::count();
        $totalAvailableCopies = BookCopy::where('status', BookCopyStatus::AVAILABLE)->
            count();
        $totalReservedCopies = Book::with('reservations')
            ->whereHas('reservations', function ($query) {
                $query->where('status', 'pending')
                      ->orWhere('status', 'ready_for_pickup');
            })
            ->count();

        $totalDamagedCopies = BookCopy::where('status', BookCopyStatus::DAMAGED)
            ->count();
        
        // Count pending book reviews
        $pendingBookReviews = BookCopy::with('issueReport')
            ->whereHas('issueReport', function ($query) {
                $query->where('status', IssueReportStatus::PENDING);
            })
            ->count();
        
        // Pending Clearance Requests
        $pendingClearanceRequests = User::whereHas('clearances', function ($query) {
            $query->where('status', ClearanceStatus::PENDING);
        })->count();

        // Ready for Pickup Reservations
        $readyForPickupReservations = BookCopy::whereHas('reservations', function ($query) {
            $query->where('status', ReservationStatus::READY_FOR_PICKUP);
        })->count();

        // Pending Penalty Payments
        $pendingPenaltyPayments = Penalty::where('status', PenaltyStatus::UNPAID)
            ->whereHas('borrowTransaction.user', function ($query) {
                $query->whereIn('role', ['student', 'teacher']);
            })
            ->count();

        // Active Semester
        $activeSemester = Semester::where('status', 'active')->first();
        
        return view('pages.librarian.dashboard', [
            'activeSemester' => $activeSemester,

            // Dashboard Summary Counts
            'totalBooks' => $totalBooks,
            'totalActiveBorrowers' => $totalActiveBorrowers,
            'totalBorrowedCopies' => $totalBorrowedCopies,
            'totalUnpaidFines' => $totalUnpaidFines,
            'topBooks' => $topBooks,
            'recentActivities' => $recentActivities,
            'dueSoon' => $dueSoon,

            // Quick Actions Badge Counts
            'pendingClearanceRequests' => $pendingClearanceRequests,
            'pendingBookReviews' => $pendingBookReviews,
            'readyForPickupReservations' => $readyForPickupReservations,
            'pendingPenaltyPayments' => $pendingPenaltyPayments,

            // Library Statistics
            'totalCopies' => $totalCopies,
            'totalAvailableCopies' => $totalAvailableCopies,
            'totalReservedCopies' => $totalReservedCopies,
            'totalDamagedCopies' => $totalDamagedCopies,
        ]);
    }
}