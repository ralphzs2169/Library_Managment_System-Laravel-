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
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")
                ->orWhereHas('author', function ($q2) use ($search) {
                    $q2->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('middle_initial', 'like', "%{$search}%")
                        ->orWhereRaw(
                            "CONCAT(lastname, ', ', firstname, ' ', COALESCE(CONCAT(middle_initial, '.'), '')) LIKE ?",
                            ["%{$search}%"]
                        );
                });
            });
        }


        // Apply category filter
        if ($request->filled('category')) {
            $query->whereHas('genre', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('copies', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Apply sort
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'author_asc':
                $query->join('authors', 'books.author_id', '=', 'authors.id')
                      ->orderBy('authors.lastname', 'asc')
                      ->orderBy('authors.firstname', 'asc')
                      ->select('books.*');
                break;
            case 'author_desc':
                $query->join('authors', 'books.author_id', '=', 'authors.id')
                      ->orderBy('authors.lastname', 'desc')
                      ->orderBy('authors.firstname', 'desc')
                      ->select('books.*');
                break;
            case 'copies_asc':
                $query->withCount('copies')
                      ->orderBy('copies_count', 'asc');
                break;
            case 'copies_desc':
                $query->withCount('copies')
                      ->orderBy('copies_count', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        $books = $query->with(['author', 'genre.category', 'copies'])
                       ->paginate(10)
                       ->withQueryString();

        if ($request->ajax()) {
            // Return only the table partial for dynamic updates
            return view('partials.librarian.book-catalog-table', compact('books'))->render();
        }

        return view('pages.librarian.book-catalog', compact('books', 'categories'));
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
        
        // Handle invalid status (business rule violations)
        if (isset($result['status']) && $result['status'] === 'invalid') {
            return response()->json($result, 422);
        }
        
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
