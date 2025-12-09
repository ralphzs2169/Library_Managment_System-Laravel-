<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('query')) {
            $query = $request->get('query');
            $books = $this->bookService->searchPublicBooks($query);
            return response()->json(['data' => $books]);
        }

        $newArrivals = $this->bookService->getNewArrivals(10);
        
        return view('pages.welcome', compact('newArrivals'));
    }
}
