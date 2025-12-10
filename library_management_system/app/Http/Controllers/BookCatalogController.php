<?php

namespace App\Http\Controllers;

use App\Enums\BorrowTransactionStatus;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Policies\ReservationPolicy;
use App\Enums\ReservationStatus;

class BookCatalogController extends Controller
{
    public function index(Request $request){
        $query = Book::with(['author', 'genre.category', 'copies']);

        // Eager load active reservations and borrows for the current user to show status in grid
        if (auth()->check()) {
            $userId = auth()->id();
            $query->with(['reservations' => function($q) use ($userId) {
                $q->where('borrower_id', $userId)
                  ->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::READY_FOR_PICKUP]);
            }]);
            
            $query->with(['borrowTransactions' => function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereIn('borrow_transactions.status', [BorrowTransactionStatus::BORROWED, BorrowTransactionStatus::OVERDUE])
                  ->whereNull('returned_at');
            }]);
        }

        // Apply genre filter if provided
        if ($request->has('genre') && $request->genre) {
            $query->where('genre_id', $request->genre);
        }

        // Apply category filter if provided
        if ($request->has('category') && $request->category) {
            $query->whereHas('genre', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('author', function($authorQuery) use ($search) {
                      $authorQuery->where('firstname', 'like', "%{$search}%")
                                  ->orWhere('lastname', 'like', "%{$search}%")
                                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        // Apply sorting
        $sort = $request->get('sort', 'title_asc');
        switch($sort) {
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'year_asc':
                $query->orderBy('publication_year', 'asc');
                break;
            case 'year_desc':
                $query->orderBy('publication_year', 'desc');
                break;
            default:
                $query->orderBy('title', 'asc');
        }

        // Apply language filter (multiple)
        if ($request->has('languages') && $request->languages) {
            $languages = explode(',', $request->languages);
            $query->whereIn('language', $languages);
        }

        // Apply year range filter
        if ($request->has('year_from') && $request->year_from) {
            $query->where('publication_year', '>=', $request->year_from);
        }
        
        if ($request->has('year_to') && $request->year_to) {
            $query->where('publication_year', '<=', $request->year_to);
        }

        // Apply availability filter (multiple statuses)
        if ($request->has('availability') && $request->availability) {
            $statuses = explode(',', $request->availability);
            
            $query->where(function($q) use ($statuses) {
                $hasCondition = false;

                if (in_array('available', $statuses)) {
                    $q->whereHas('copies', function($subQ) {
                        $subQ->where('status', 'available');
                    });
                    $hasCondition = true;
                }
                
                if (in_array('borrowed', $statuses)) {
                    if ($hasCondition) {
                        $q->orWhereHas('copies', function($subQ) {
                            $subQ->where('status', 'borrowed');
                        });
                    } else {
                        $q->whereHas('copies', function($subQ) {
                            $subQ->where('status', 'borrowed');
                        });
                        $hasCondition = true;
                    }
                }

                if (in_array('archived', $statuses)) {
                    if ($hasCondition) {
                        $q->orWhereHas('copies', function($subQ) {
                            $subQ->whereIn('status', ['lost', 'damaged', 'withdrawn']);
                        });
                    } else {
                        $q->whereHas('copies', function($subQ) {
                            $subQ->whereIn('status', ['lost', 'damaged', 'withdrawn']);
                        });
                        $hasCondition = true;
                    }
                }

                if (in_array('reserved', $statuses)) {
                    if ($hasCondition) {
                        $q->orWhereHas('reservations', function($subQ) {
                            $subQ->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::READY_FOR_PICKUP]);
                        });
                    } else {
                        $q->whereHas('reservations', function($subQ) {
                            $subQ->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::READY_FOR_PICKUP]);
                        });
                    }
                }

            });
        }


        if (!$request->ajax()) {
            $request->merge(['page' => 1]);
        }

        $books = $query->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.borrowers.book-grid', ['books' => $books])->render(),
                'hasMore' => $books->hasMorePages(),
                'total' => $books->total()
            ]);
        }

        return view('pages.borrowers.book-catalog', compact('books'));
    }

    public function show(Book $book){
        $book->load(['author', 'genre.category', 'copies' => function($query) {
            $query->orderBy('copy_number', 'asc');
        }]);

        $user = auth()->user();

        $user->load('students', 'teachers');
        
        // Check for active borrow
        $activeBorrow = $user->borrowTransactions()
            ->whereHas('bookCopy', function($q) use ($book) {
                $q->where('book_id', $book->id);
            })
            ->whereIn('status', ['borrowed', 'overdue'])
            ->whereNull('returned_at')
            ->first();

        // Check for any active reservation (Pending or Ready for Pickup)
        $activeReservation = $user->reservations()
            ->where('book_id', $book->id)
            ->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::READY_FOR_PICKUP])
            ->first();

        $queuePosition = null;

        if ($activeReservation && $activeReservation->status === ReservationStatus::PENDING) {
            // User has pending reservation: get their current position
            $queuePosition = Reservation::where('book_id', $book->id)
                ->where('status', ReservationStatus::PENDING)
                ->where('id', '<=', $activeReservation->id)
                ->count();
        } elseif (!$activeReservation) {
            // User has no reservation: get next available position
            $queuePosition = Reservation::where('book_id', $book->id)
                ->where('status', ReservationStatus::PENDING)
                ->count() + 1;
        }
        
        $canReserve = ReservationPolicy::canReserve($user, $book);
        
        return view('pages.borrowers.book-info', compact('book', 'canReserve', 'user', 'queuePosition', 'activeReservation', 'activeBorrow'));
    }
}