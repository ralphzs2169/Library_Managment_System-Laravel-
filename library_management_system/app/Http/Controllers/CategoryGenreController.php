<?php

namespace App\Http\Controllers;

use App\Models\CategoryGenre;
use Illuminate\Http\Request;
use App\Models\User;

class CategoryGenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'librarian') {
            // $categories = CategoryGenre::all();
            return view('pages.librarian.categories-genres');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryGenre $categoryGenre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryGenre $categoryGenre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoryGenre $categoryGenre)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryGenre $categoryGenre)
    {
        //
    }
}
