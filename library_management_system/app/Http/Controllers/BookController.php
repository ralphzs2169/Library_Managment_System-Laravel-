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
use App\Services\BookService;

use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

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

    public function showAvailableBooks(Request $request)
    {
        $filters = $request->only(['search', 'sort']);
        $books = $this->bookService->showAvailableBooks($filters);

        return response()->json([
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
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
            $this->bookService->createBookWithAuthorAndCopies($request);
            return response()->json(['status' => 'success', 'message' => 'Book added successfully.'], 201);

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
        $result = $this->bookService->updateBook($request, $book);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //
    }
}
