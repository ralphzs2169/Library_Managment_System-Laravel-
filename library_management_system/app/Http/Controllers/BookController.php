<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();
        $categories = Category::with('genres')->get();


        // Apply filters based on request parameters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $books->load('author', 'genre', 'copies');

        if ($request->ajax()) {
            return view('pages.librarian.books-list', compact('books', 'categories'))->render();
        }

        return view('pages.librarian.books-list', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $genres = Genre::all();
        return view('pages.librarian.add-new-book', compact('categories', 'genres'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Accept edit-prefixed author fields (from edit form) and normalize to expected keys


        if ($request->boolean('validate_only')) {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'isbn' => 'required|string|unique:books,isbn|max:13',
                'description' => 'nullable|string',
                'cover' => 'nullable|mimetypes:image/jpeg,image/png,image/webp,image/gif,image/bmp,image/svg+xml|max:2048',

                'author_firstname' => ['required', 'string', 'max:45', 'regex:/^[A-Za-z\s]+$/'],
                'author_lastname' => ['required', 'string', 'max:45', 'regex:/^[A-Za-z\s]+$/'],
                'author_middle_initial' => ['nullable', 'string', 'max:1', 'regex:/^[a-zA-Z]$/'],


                'publisher' => 'nullable|string|max:255',
                'publication_year' => 'nullable|digits:4|integer|min:1901|max:' . date('Y'),
                'copies_available' => 'required|integer|min:1',
                'language' => 'required|in:English,Filipino,Spanish,Chinese,Others',
                'price' => 'nullable|numeric|min:0',
                'genre' => 'required|exists:genres,id',
                'category' => 'required|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json(['status' => 'success', 'message' => 'Validation passed']);
        }

        try {
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
                    'details' => 'Added a new book: ' . $request->category_name,
                    'entity_type' => 'Book',
                    'entity_id' => $book->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            return response()->json(['message' => 'Book added successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $book->load('author', 'genre', 'copies');
        $categories = Category::all();
        $genres = Genre::all();

        return response()->json(['status' => 'success', 'book' => $book, 'categories' => $categories, 'genres' => $genres]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        // Accept edit-prefixed author fields and normalize to expected keys before any validation/diffing

        $changes = [];

        if ($request->boolean('validate_only')) {
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

            $normalize = function ($field, $value) {
                if ($value === '' || $value === null) return null;
                if ($field === 'publication_year') return (int)$value;
                if ($field === 'price') return is_numeric($value) ? (float)$value : null;
                return is_string($value) ? trim($value) : $value;
            };

            foreach ($fields as $field) {
                // detect presence in request (handles edited keys)
                $possibleKeys = [
                    'edit-' . $field,
                    $field
                ];
                $found = false;
                $updatedValue = null;

                foreach ($possibleKeys as $key) {
                    if ($request->exists($key)) {
                        $found = true;
                        $updatedValue = $request->input($key);
                        break;
                    }
                }
                if (! $found) continue;

                // Special handling for category and genre: compare by id
                if ($field === 'category') {
                    $oldId = $book->category_id ?? ($book->genre && $book->genre->category ? $book->genre->category->id : null);
                    $newId = ($updatedValue === '' || $updatedValue === null) ? null : (int)$updatedValue;
                    if ($oldId !== $newId) {
                        $changes[$field] = ['old' => $oldId, 'new' => $newId];
                    }
                    continue;
                }

                if ($field === 'genre') {
                    $oldId = $book->genre_id ?? ($book->genre ? $book->genre->id : null);
                    $newId = ($updatedValue === '' || $updatedValue === null) ? null : (int)$updatedValue;
                    if ($oldId !== $newId) {
                        $changes[$field] = ['old' => $oldId, 'new' => $newId];
                    }
                    continue;
                }

                if ($field === 'cover') {
                    $uploaded = $request->file('edit-cover') ?? $request->file('cover') ?? null;

                    if (!$uploaded) {
                        continue; // no new file, skip
                    }

                    $oldCoverPath = $book->cover_image ? storage_path('app/public/' . ltrim($book->cover_image, '/')) : null;
                    $oldSize = $oldCoverPath && file_exists($oldCoverPath) ? filesize($oldCoverPath) : null;
                    $newSize = $uploaded->getSize();

                    $sizeChanged = $oldSize !== $newSize;
                    $hashChanged = false;

                    if (!$sizeChanged) {
                        // Only compute hash if file sizes are equal
                        $oldHash = $oldCoverPath && file_exists($oldCoverPath) ? @md5_file($oldCoverPath) : null;
                        $newHash = @md5_file($uploaded->getRealPath());
                        $hashChanged = $oldHash !== $newHash;
                    }

                    if ($sizeChanged || $hashChanged) {
                        $changes[$field] = [
                            'old' => $book->cover_image ?? null,
                            'new' => $uploaded->getClientOriginalName()
                        ];
                    }

                    continue;
                }

                $copiesInput = $request->input('copies', []); // e.g. ['1'=>'lost','2'=>'damaged']

                foreach ($copiesInput as $copyId => $newStatus) {
                    $copy = $book->copies()->find($copyId);
                    if (!$copy) continue; // skip invalid copy IDs

                    $oldStatus = $copy->status;

                    if ($oldStatus !== $newStatus) {
                        $changes["copies.$copyId"] = ['old' => $oldStatus, 'new' => $newStatus];
                    }
                }

                // Normalize and compare other fields
                if (in_array($field, ['author_firstname', 'author_lastname', 'author_middle_initial'])) {
                    $oldAuthor = $book->author; // get related Author record
                    if ($oldAuthor) {
                        $oldValue = match ($field) {
                            'author_firstname' => $oldAuthor->firstname,
                            'author_lastname' => $oldAuthor->lastname,
                            'author_middle_initial' => $oldAuthor->middle_initial,
                        };
                    } else {
                        $oldValue = null;
                    }
                } else {
                    $oldValue = $book->$field;
                }

                $old = $normalize($field, $oldValue);
                $new = $normalize($field, $updatedValue);
                if ($old !== $new) {
                    $changes[$field] = ['old' => $oldValue, 'new' => $updatedValue];
                }
            }

            if (empty($changes)) {
                return response()->json(['status' => 'unchanged', 'message' => 'No changes detected.']);
            }
        }


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

        try {
            DB::transaction(function () use ($request, $book) {

                // Update Author
                $author = $book->author;
                if ($author) {
                    $author->update([
                        'firstname' => $request->input('author_firstname', $author->firstname),
                        'lastname' => $request->input('author_lastname', $author->lastname),
                        'middle_initial' => $request->input('author_middle_initial', $author->middle_initial),
                    ]);
                }

                // Update Book
                $book->update([
                    'title' => $request->input('title', $book->title),
                    'isbn' => $request->input('isbn', $book->isbn),
                    'description' => $request->input('description', $book->description),
                    'cover_image' => $request->hasFile('cover') ? $request->file('cover')->store('covers', 'public') : $book->cover_image,
                    'publisher' => $request->input('publisher', $book->publisher),
                    'publication_year' => $request->input('publication_year', $book->publication_year),
                    'language' => $request->input('language', $book->language),
                    'price' => $request->input('price', $book->price),
                    'genre_id' => $request->input('genre', $book->genre_id),
                ]);

                // Update Book Copies status
                $copiesInput = $request->input('copies', []);

                foreach ($copiesInput as $copyId => $newStatus) {
                    $copy = $book->copies()->find($copyId);
                    if ($copy && $copy->status !== $newStatus) {
                        $copy->update(['status' => $newStatus]);
                    }
                }
            });
            return response()->json(['status' => 'success', 'changes' => $changes]);
        } catch (\Exception $e) {
            Log::error('Book update failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update book.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //
    }
}
