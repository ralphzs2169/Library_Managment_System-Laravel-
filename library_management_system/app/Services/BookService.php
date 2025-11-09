<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use App\Models\BookCopy;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BookService {

    public function showAvailableBooks($filters){
        $query = Book::whereHas('copies', function ($q) {
            $q->where('status', 'available');
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

        // Paginate and eager load relations
        $books = $query->with(['author', 'genre.category', 'copies'])
                    ->paginate(10)
                    ->withQueryString();

        // Compute copies_available for each book
        $books->getCollection()->transform(function ($book) {
            $book->copies_available = $book->copies->where('status', 'available')->count();
            $book->category_name = optional($book->genre->category)->name ?? 'N/A';
            return $book;
        });

        return $books;
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
                    'status' => 'available'
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
            
            $this->validateBookData($request, $book);
            return ['status' => 'success', 'message' => 'Validation passed'];
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
            'price' => 'sometimes|nullable|numeric|min:0',
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

        // Check for changes in copies status
        $copiesInput = $request->input('copies', []);
        foreach ($copiesInput as $copyId => $newStatus) {
            $copy = $book->copies()->find($copyId);
            if ($copy && $copy->status !== $newStatus) {
                $changes["copies.$copyId"] = ['old' => $copy->status, 'new' => $newStatus];
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
     * Update the status of each book copy if changed.
     */
    protected function updateCopies(Request $request, Book $book)
    {
        $copiesInput = $request->input('copies', []);
        foreach ($copiesInput as $copyId => $newStatus) {
            $copy = $book->copies()->find($copyId);
            if ($copy && $copy->status !== $newStatus) {
                $copy->update(['status' => $newStatus]);
            }
        }
    }

    /**
     * Helper: map author field names.
     */
    protected function mapAuthorField($field)
    {
        return match ($field) {
            'author_firstname' => 'firstname',
            'author_lastname' => 'lastname',
            'author_middle_initial' => 'middle_initial',
        };
    }
}