<?php

namespace App\Http\Controllers;

use App\Enums\BookCopyStatus;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Penalty;
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
        
        return view('pages.librarian.dashboard', compact(
            'totalBooks', 
            'totalActiveBorrowers', 
            'totalBorrowedCopies', 
            'totalUnpaidFines',
            'topBooks'
        ));
    }

    public function show(){
        // ...
    }
}