<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\AuthorBook; // added import for AuthorBook
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Genre;
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
        $books->load('authors', 'genre', 'copies');

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
        if ($request->boolean('validate_only')) {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'isbn' => 'required|string|unique:books,isbn|max:13',
                'description' => 'nullable|string',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                'authors' => 'required|array|min:1',
                'authors.*.firstname' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z]+$/'],
                'authors.*.lastname' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z]+$/'],
                'authors.*.middle_initial' => ['nullable', 'string', 'max:1', 'regex:/^[a-zA-Z]$/'],

                'publisher' => 'nullable|string|max:255',
                'publication_year' => 'nullable|digits:4|integer|min:1901|max:' . date('Y'),
                'copies_available' => 'required|integer|min:1',
                'language' => 'required|in:English,Filipino,Spanish,Chinese,Others',
                'price' => 'nullable|numeric|min:0',
                'genre_id' => 'required|exists:genres,id',
                'category_id' => 'required|exists:categories,id',
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
                $book = Book::create([
                    'title' => $request->title,
                    'isbn' => $request->isbn,
                    'description' => $request->description,
                    'cover_image' => $request->hasFile('cover') ? $request->file('cover')->store('covers', 'public') : null,
                    'publisher' => $request->publisher,
                    'publication_year' => $request->publication_year,
                    'language' => $request->language,
                    'price' => $request->price,
                    'genre_id' => $request->genre_id,
                    'category_id' => $request->category_id,
                ]);

                // Create all authors and link to the book
                foreach ($request->input('authors') as $authorData) {
                    $author = Author::create([
                        'firstname' => $authorData['firstname'],
                        'lastname' => $authorData['lastname'],
                        'middle_initial' => $authorData['middle_initial'] ?? null,
                    ]);

                    AuthorBook::create([
                        'book_id' => $book->id,
                        'author_id' => $author->id,
                    ]);
                }

                for ($i = 0; $i < $request->copies_available; $i++) {
                    BookCopy::create([
                        'book_id' => $book->id,
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
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
                'genre_id',
                'category_id'
            ];

            $normalize = function ($field, $value) {
                if ($value === '' || $value === null) return null;
                if (str_ends_with($field, '_id')) return (int)$value;
                if ($field === 'publication_year') return (int)$value;
                if ($field === 'price') return is_numeric($value) ? (float)$value : null;
                return is_string($value) ? trim($value) : $value;
            };

            foreach ($fields as $field) {
                // detect presence in request (handles updated_*, edit-*, edit_-, or raw keys)
                $possibleKeys = [
                    'updated_' . $field,
                    'updated-' . $field,
                    $field
                ];

                $found = false;
                $newRaw = null;
                foreach ($possibleKeys as $key) {
                    if ($request->exists($key)) {
                        $found = true;
                        $newRaw = $request->input($key);
                        break;
                    }
                }

                // if not provided, skip comparison for this field
                if (! $found) {
                    continue;
                }

                // derive old raw value, with special handling for category_id:
                $oldRaw = $book->$field;
                if ($field === 'category_id' && ($oldRaw === null || $oldRaw === '')) {
                    $oldRaw = $book->genre && $book->genre->category ? $book->genre->category->id : null;
                }
                // similarly, derive genre_id from relations if needed (optional)
                if ($field === 'genre_id' && ($oldRaw === null || $oldRaw === '')) {
                    $oldRaw = $book->genre ? $book->genre->id : null;
                }

                $old = $normalize($field, $oldRaw);
                $new = $normalize($field, $newRaw);

                if ($old !== $new) {
                    $changes[$field] = [
                        'old' => $oldRaw,
                        'new' => $newRaw
                    ];
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
            'genre_id' => 'sometimes|required|exists:genres,id',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        return response()->json(['status' => 'success', 'changes' => $changes]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //
    }
}
