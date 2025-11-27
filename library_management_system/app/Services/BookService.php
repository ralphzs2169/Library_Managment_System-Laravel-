<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use App\Models\BookCopy;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Enums\BookCopyStatus;
use App\Enums\IssueReportStatus;
use App\Enums\IssueReportType;
use App\Models\Penalty;
use App\Models\Settings;
use App\Enums\PenaltyType;
use App\Enums\PenaltyStatus;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Policies\BookPolicy;
use App\Enums\ReservationStatus;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class BookService {

    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    private const TRANSACTION_TYPES = ['borrow', 'reservation'];

    public function getBooksForBorrowOrReserve($filters, $transactionType = self::TRANSACTION_TYPES[0], $member_id){
        $statusToCheck  = null;

        // Determine status to check based on transaction type
        if ($transactionType === self::TRANSACTION_TYPES[0]) 
            // For borrowing, check for available copies
            $statusToCheck  = BookCopyStatus::AVAILABLE;
        else if ($transactionType === self::TRANSACTION_TYPES[1]) 
            // For reservation, check for borrowed copies
            $statusToCheck  = BookCopyStatus::BORROWED;


        // Query books with copies matching the status and doesnt have a pending issue report
        $query = Book::with('reservations')->whereHas('copies', function ($q) use ($statusToCheck ) {
            $q->where('status', $statusToCheck )
            ->whereDoesntHave('pendingIssueReport');
        });

        // Apply search filter if provided
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('isbn', 'like', '%' . $search . '%')
                  ->orWhereHas('author', function ($authorQuery) use ($search) {
                      $authorQuery->where('firstname', 'like', '%' . $search . '%')
                                  ->orWhere('lastname', 'like', '%' . $search . '%')
                                  ->orWhereRaw("CONCAT(middle_initial, '.' ) LIKE ?", ["%{$search}%"])
                                   ->orWhereRaw("CONCAT(lastname, ', ', firstname, ' ', COALESCE(middle_initial, '')) LIKE ?", ["%{$search}%"])
                                   ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                                   ->orWhereRaw("CONCAT(firstname, ' ', COALESCE(middle_initial, ''), ' ', lastname) LIKE ?", ["%{$search}%"]);
                    });
            });
        }
        
        // Apply sort filter
        if (!empty($filters['sort'])) {
            $sort = $filters['sort'];

            switch ($sort) {
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'year_desc':
                    $query->orderBy('publication_year', 'desc');
                    break;
                case 'year_asc':
                    $query->orderBy('publication_year', 'asc');
                    break;
                case 'author_asc':
                    $query->whereHas('author') // Ensure only books with authors are included
                          ->leftJoin('authors', 'books.author_id', '=', 'authors.id')
                          ->orderBy('authors.lastname', 'asc')
                          ->orderBy('authors.firstname', 'asc')
                          ->select('books.*');
                    break;
                case 'author_desc':
                    $query->whereHas('author') // Ensure only books with authors are included
                          ->leftJoin('authors', 'books.author_id', '=', 'authors.id')
                          ->orderBy('authors.lastname', 'desc')
                          ->orderBy('authors.firstname', 'desc')
                          ->select('books.*');
                    break;
                default: // title_asc
                    $query->orderBy('title', 'asc');
            }
        } else {
            $query->orderBy('title', 'asc'); // default sort
        }

        $member = User::findOrFail($member_id);

        $query->with(['author', 'genre.category', 
            'copies' => function ($q) use ($statusToCheck) {
                $q->where('status', $statusToCheck);
            }
        ]);

        // Determine the appropriate policy method
        $policyMethod = $transactionType === 'borrow'
            ? [BookCopyPolicy::class, 'canBeBorrowed']
            : [BookPolicy::class, 'canBeReserved'];

        // --- MANUAL PAGINATION AND FILTERING LOGIC  ---

        // 1. Get pagination parameters
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($currentPage * $perPage) - $perPage;

        // 2. Execute the full query to get all books matching the search/status criteria (no pagination limit yet)
        $allBooks = $query->get();

        // Ensure all copies are loaded for policy checks
        $allBooks->each(function ($book) {
            $book->load('copies');
        });
         
        Log::info('Total books fetched before policy filtering: ' . $allBooks->count());
        // 3. Filter the collection based on the policy result
        $filteredCollection = $allBooks->filter(function ($book) use ($member, $policyMethod) {
            $result = call_user_func($policyMethod, $book, $member);
            Log::info('Policy check for book: ' . $book->title . ': ' . json_encode($result));
            // Exclude the book if policy fails or no eligible copies are loaded
            if ($result['result'] !== 'success' || $book->copies->isEmpty()) {
                Log::info('Excluding book: ' . $book->title . ' due to policy failure or no eligible copies.');
                return false;
            }

            return true;
        });

        // 4. Transform the remaining, eligible books (calculate final properties)
        $transformedCollection = $filteredCollection->map(function ($book) {
            // These properties are now set ONLY for eligible books
            $book->eligible_copies = $book->copies->count();
            $book->category_name = optional($book->genre->category)->name ?? 'N/A';
            $book->next_queue_position = $book->reservations->where('status', ReservationStatus::PENDING)->count() + 1;

            return $book;
        });
        
        // 5. Slice the collection to get only the items for the current page
        $currentPageItems = $transformedCollection->slice($offset, $perPage)->values();

        // 6. Create a LengthAwarePaginator instance using the filtered count
        $books = new LengthAwarePaginator(
            $currentPageItems,                      // Items for the current page
            $transformedCollection->count(),        // Total count of ELIGIBLE items
            $perPage,                               
            $currentPage,                           
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $books->withQueryString();
    }

    public function createBookWithAuthorAndCopies(Request $request){
        DB::transaction(function () use ($request) {
            $author = Author::create([
                'firstname' => $request->author_firstname,
                'lastname' => $request->author_lastname,
                'middle_initial' => $request->author_middle_initial ?? null,
            ]);

            $book = Book::create([
                'title' => $request->title,
                'isbn' => $request->isbn,
                'description' => $request->description,
                'cover_image' => $request->hasFile('cover') ? $request->file('cover')->store('covers', 'public') : null,
                'publisher' => $request->publisher,
                'publication_year' => $request->publication_year,
                'language' => $request->language,
                'price' => $request->price,
                'genre_id' => $request->genre,
                'author_id' => $author->id,
            ]);

            $nextCopyNumber = BookCopy::where('book_id', $book->id)->max('copy_number') + 1;

            for ($i = 0; $i < $request->copies_available; $i++) {
                BookCopy::create([
                    'book_id' => $book->id,
                    'copy_number' => $nextCopyNumber + $i,
                    'status' => BookCopyStatus::AVAILABLE
                ]);
            }

            ActivityLog::create([
                'action' => 'created',
                'details' => 'Created book: ' . $book->title,
                'entity_type' => 'book',
                'entity_id' => $book->id,
                'user_id' => $request->user()->id
            ]);
        });
    }
    

     /**
     * Main handler for updating a book and related records.
     */
    public function updateBook(Request $request, Book $book)
    {
        $changes = '';

        if ($request->boolean('validate_only')) {

            $changes = $this->detectChanges($request, $book);

            if(empty($changes)){
                 return ['status' => 'unchanged', 'message' => 'No changes detected.'];
            }
            
            // Check for validation errors from detectChanges
            if (isset($changes['error'])) {
                return $changes; // Return error response directly
            }
            
            $this->validateBookData($request, $book);
            return ['status' => 'success', 'message' => 'Validation passed', 'changes' => $changes];
        }

        try {
            DB::transaction(function () use ($request, $book) {
                $this->updateAuthor($request, $book);
                $this->updateBookRecord($request, $book);
                $this->updateCopies($request, $book);
                ActivityLog::create([
                    'action' => 'updated',
                    'details' => 'Updated book: ' . $book->title,
                    'entity_type' => 'Book',
                    'entity_id' => $book->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            return ['status' => 'success', 'changes' => $changes];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' =>  $e->getMessage()];
        }
    }

    /**
     * Validate the request input.
     */
    protected function validateBookData(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|unique:books,isbn,' . $book->id . '|max:13',
            'description' => 'sometimes|nullable|string',
            'cover' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
            'publisher' => 'sometimes|nullable|string|max:255',
            'publication_year' => 'sometimes|nullable|digits:4|integer|min:1901|max:' . date('Y'),
            'language' => 'sometimes|required|in:English,Filipino,Spanish,Chinese,Others',
            'price' => 'sometimes|required|numeric|min:0',
            'genre' => 'sometimes|required|exists:genres,id',
            'category' => 'sometimes|required|exists:categories,id',
            'author_firstname' => 'sometimes|required|string|max:45|regex:/^[A-Za-z\s]+$/',
            'author_lastname' => 'sometimes|required|string|max:45|regex:/^[A-Za-z\s]+$/',
            'author_middle_initial' => 'sometimes|nullable|string|max:1|regex:/^[a-zA-Z]$/'
        ]);
    }

    /**
     * Detect what fields have changed before updating.
     */
    protected function detectChanges(Request $request, Book $book)
    {
        $fields = [
            'title',
            'isbn',
            'description',
            'publisher',
            'publication_year',
            'language',
            'price',
            'genre',
            'category',
            'cover',
            'author_firstname',
            'author_lastname',
            'author_middle_initial'
        ];

        $changes = [];

        $normalize = function ($field, $value) {
            if ($value === '' || $value === null) return null;
            if ($field === 'publication_year') return (int) $value;
            if ($field === 'price') return is_numeric($value) ? (float) $value : null;
            return is_string($value) ? trim($value) : $value;
        };

        foreach ($fields as $field) {
            $possibleKeys = ['edit-' . $field, $field];
            $updatedValue = null;
            $found = false;

            foreach ($possibleKeys as $key) {
                if ($request->exists($key)) {
                    $found = true;
                    $updatedValue = $request->input($key);
                    break;
                }
            }

            if (!$found) continue;

            // Special handling for category and genre
            if ($field === 'category') {
                $oldId = $book->category_id ?? ($book->genre && $book->genre->category ? $book->genre->category->id : null);
                $newId = $updatedValue ? (int) $updatedValue : null;
                if ($oldId !== $newId) $changes[$field] = ['old' => $oldId, 'new' => $newId];
                continue;
            }

            if ($field === 'genre') {
                $oldId = $book->genre_id ?? ($book->genre ? $book->genre->id : null);
                $newId = $updatedValue ? (int) $updatedValue : null;
                if ($oldId !== $newId) $changes[$field] = ['old' => $oldId, 'new' => $newId];
                continue;
            }

            // Cover comparison
            if ($field === 'cover') {
                $uploaded = $request->file('edit-cover') ?? $request->file('cover') ?? null;
                if (!$uploaded) continue;

                $oldCoverPath = $book->cover_image ? storage_path('app/public/' . ltrim($book->cover_image, '/')) : null;
                $oldSize = $oldCoverPath && file_exists($oldCoverPath) ? filesize($oldCoverPath) : null;
                $newSize = $uploaded->getSize();

                $hashChanged = false;
                if ($oldSize === $newSize && $oldCoverPath && file_exists($oldCoverPath)) {
                    $oldHash = @md5_file($oldCoverPath);
                    $newHash = @md5_file($uploaded->getRealPath());
                    $hashChanged = $oldHash !== $newHash;
                }

                if ($oldSize !== $newSize || $hashChanged) {
                    $changes[$field] = ['old' => $book->cover_image ?? null, 'new' => $uploaded->getClientOriginalName()];
                }
                continue;
            }

            // Compare author fields
            if (in_array($field, ['author_firstname', 'author_lastname', 'author_middle_initial'])) {
                $oldAuthor = $book->author;
                $oldValue = $oldAuthor ? $oldAuthor->{$this->mapAuthorField($field)} : null;
            } else {
                $oldValue = $book->$field;
            }

            $old = $normalize($field, $oldValue);
            $new = $normalize($field, $updatedValue);

            if ($old !== $new) {
                $changes[$field] = ['old' => $oldValue, 'new' => $updatedValue];
            }
        }

        // Check for changes in copies status and new copies
        $copiesInput = $request->input('copies', []);

        foreach ($copiesInput as $copyId => $newStatus) {

            // New copies (negative IDs)
            if ($copyId < 0) {
                $changes["copies.new_{$copyId}"] = [
                    'old' => null, 
                    'new' => "New copy with status: {$newStatus}"
                ];
                continue;
            }

            // Existing copies
            $copy = $book->copies->firstWhere('id', $copyId) ?: $book->copies()->find($copyId);
            if (!$copy) {
                continue; // Skip if copy not found
            }

            $currentStatus = $copy->status; // converts enum to string
            $newStatus = $newStatus;

            // Only validate if the status is actually changing
            if ($currentStatus !== $newStatus) {
                   // Track changes
                $changes["copies.{$copyId}"] = [
                    'old' => $currentStatus, 
                    'new' => $newStatus
                ];
            }
        }

        return $changes;
    }

    /**
     * Update the author of the book.
     */
    protected function updateAuthor(Request $request, Book $book)
    {
        $author = $book->author;

        if ($author) {
            $author->update([
                'firstname' => $request->input('author_firstname', $author->firstname),
                'lastname' => $request->input('author_lastname', $author->lastname),
                'middle_initial' => $request->input('author_middle_initial', $author->middle_initial),
            ]);
        }
    }

    /**
     * Update the main book record.
     */
    protected function updateBookRecord(Request $request, Book $book)
    {
        $book->update([
            'title' => $request->input('title', $book->title),
            'isbn' => $request->input('isbn', $book->isbn),
            'description' => $request->input('description', $book->description),
            'cover_image' => $request->hasFile('cover')
                ? $request->file('cover')->store('covers', 'public')
                : $book->cover_image,
            'publisher' => $request->input('publisher', $book->publisher),
            'publication_year' => $request->input('publication_year', $book->publication_year),
            'language' => $request->input('language', $book->language),
            'price' => $request->input('price', $book->price),
            'genre_id' => $request->input('genre', $book->genre_id),
        ]);
    }

    /**
     * Update the status of each book copy if changed, or create new copies.
     */
    protected function updateCopies(Request $request, Book $book)
    {
        DB::transaction(function () use ($request, $book) {
            $copiesInput = $request->input('copies', []);
            $pendingResolved = $request->input('pending_issue_resolved');

            foreach ($copiesInput as $copyId => $newStatus) {
                // Handle new copies (negative IDs)
                if ($copyId < 0) {
                    $nextCopyNumber = BookCopy::where('book_id', $book->id)->max('copy_number') + 1;
                    BookCopy::create([
                        'book_id' => $book->id,
                        'copy_number' => $nextCopyNumber,
                        'status' => $newStatus
                    ]);
                    
                    $this->reservationService->promoteNextPendingReservation($book->copies()->where('status', 'available')->first());
                    continue;
                }

                // Existing copies
                $copy = $book->copies()->find($copyId);
                if (!$copy) continue;

                $currentStatus = $copy->status;

                // Only update if status changed
                if ($currentStatus !== $newStatus) {

                    // Handle pending issue review resolution
                    if ($pendingResolved === 'true' && $currentStatus === BookCopyStatus::PENDING_ISSUE_REVIEW) {
                        $errors = $this->resolvePendingIssueReport($copy, $newStatus, $request->user()->id);

                        if (!empty($errors)) {
                            throw new Exception("Errors occurred: " . implode('; ', $errors));
                        }

                        continue; // Skip normal update since handled
                    }

                    if ($newStatus === BookCopyStatus::BORROWED) {
                        throw new Exception("Cannot manually set a book copy to 'Borrowed' status. Borrowing must be done through the borrowing process.");
                    }

                    if ($currentStatus === BookCopyStatus::WITHDRAWN  && $newStatus !== BookCopyStatus::WITHDRAWN) {
                        throw new Exception("Cannot change status of withdrawn copy. Withdrawn books are permanently removed from circulation and cannot be reactivated."); 
                    }

                    if ($currentStatus === BookCopyStatus::BORROWED && $newStatus === BookCopyStatus::AVAILABLE) {
                        throw new Exception("Cannot directly mark a borrowed book as available. The book must be returned through the proper process before it can be marked as available.");
                    }

                    if ($currentStatus === BookCopyStatus::BORROWED && $newStatus === BookCopyStatus::WITHDRAWN) {
                        throw new Exception("Cannot withdraw a borrowed book. The book must be returned before it can be withdrawn.");
                    }

                    if ($currentStatus === BookCopyStatus::BORROWED && $newStatus === BookCopyStatus::LOST) {
                        throw new Exception("Cannot directly mark a borrowed book as lost. Lost reports must be submitted by staff and approved through the proper process.");
                    }

                    if ($currentStatus === BookCopyStatus::BORROWED && $newStatus === BookCopyStatus::DAMAGED) {
                        throw new Exception("Cannot directly mark a borrowed book as damaged. Damage reports must be submitted by staff and approved through the proper process.");
                    }

                    if($newStatus === BookCopyStatus::AVAILABLE){
                        $this->reservationService->promoteNextPendingReservation($copy);
                    }

                    // if ($currentStatus === BookCopyStatus::LOST && $newStatus === BookCopyStatus::AVAILABLE) {
                    //    throw new Exception("Cannot directly mark a lost book as available. Lost reports must be reviewed and approved through the proper process.");
                    // }

                    // Otherwise, update status normally
                    $copy->update(['status' => $newStatus]);
                }
            }
        });
    }

    /**
     * Resolve a pending issue report for a book copy.
     */
    protected function resolvePendingIssueReport(BookCopy $copy, $newStatus, $userId)
    {
        $errors = [];

        try {
            DB::transaction(function () use ($copy, $newStatus, $userId) {
                $pendingReport = $copy->pendingIssueReport()->first();

                if (!$pendingReport) {
                    $errors[] = "No pending report found for copy ID: {$copy->id}";
                    return;
                }

                // Approve: set status to damaged/lost, create penalty, suspend user
                if ($newStatus === BookCopyStatus::DAMAGED || $newStatus === BookCopyStatus::LOST) {
                    if (!$pendingReport->update([
                        'status' => IssueReportStatus::APPROVED,
                        'approved_by' => $userId,
                        'resolved_at' => now(),
                    ])) {
                        $errors[] = "Failed to update pending report for copy ID: {$copy->id}";
                    }

                    if (!$copy->update(['status' => $newStatus])) {
                        $errors[] = "Failed to update status for copy ID: {$copy->id}";
                    }

                    // Determine penalty type and amount
                    $penaltyType = $newStatus === BookCopyStatus::DAMAGED ? PenaltyType::DAMAGED_BOOK : PenaltyType::LOST_BOOK;
                    $penaltyAmount = $copy->getPenaltyAmountAttribute();

                    $transaction = $copy->borrowTransaction()->latest()->first();
                    if (!$transaction) {
                        $errors[] = "No borrow transaction found for copy ID: {$copy->id}";
                    }
                    // Create penalty record
                    $penalty =Penalty::create([
                        'borrow_transaction_id' => $transaction ? $transaction->id : null,
                        'amount' => $penaltyAmount,
                        'type' => $penaltyType,
                        'status' => PenaltyStatus::UNPAID,
                        'issued_at' => now(),
                    ]);

                    if (!$penalty) {
                        $errors[] = "Failed to create penalty for copy ID: {$copy->id}";
                    }

                    // Suspend user if transaction exists
                    if ($transaction && $transaction->user) {
                        if(!$transaction->user->update(['library_status' => 'suspended'])) {
                            $errors[] = "Failed to suspend user ID: {$transaction->user->id}";
                        }
                    }
                }
                // Reject: set status to available
                elseif ($newStatus === BookCopyStatus::AVAILABLE) {
                    if (!$pendingReport->update([
                        'status' => IssueReportStatus::REJECTED,
                        'approved_by' => $userId,
                        'resolved_at' => now(),
                    ])) {
                        $errors[] = "Failed to update pending report for copy ID: {$copy->id}";
                    }

                    if (!$copy->update(['status' => $newStatus])) {
                        $errors[] = "Failed to update status for copy ID: {$copy->id}";
                    }

                    $this->reservationService->promoteNextPendingReservation($copy);
                }
            });
        } catch (\Exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
        return $errors;
    }

    protected function mapAuthorField($field)
    {
        return match ($field) {
            'author_firstname' => 'firstname',
            'author_lastname' => 'lastname',
            'author_middle_initial' => 'middle_initial',
        };
    }
}