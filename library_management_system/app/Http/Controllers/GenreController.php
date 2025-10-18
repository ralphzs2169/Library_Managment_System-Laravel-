<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Category;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'librarian') {
            // Eager load genres to avoid N+1 queries
            $categories = Category::with('genres')->get();
            $genres = Genre::all(); // if you still need all genres

            return view('pages.librarian.category-management', [
                'categories' => $categories,
                'genres' => $genres,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->validate_only) {
            $request->validate([
                'genre_name' => 'required|string|max:50|unique:categories,name',
                'category_id' => 'required|string|exists:categories,id'
            ]);

            return $this->jsonResponse('valid', 'Validation passed');
        }

        Genre::create([
            'name' => $request->genre_name,
            'category_id' => $request->category_id
        ]);

        return $this->jsonResponse('success', 'Genre created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Genre $genre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Genre $genre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Genre $genre)
    {
        if ($request->validate_only) {
            if ($genre->name === $request->updated_genre_name) {
                return $this->jsonResponse('unchanged', 'No changes detected', 200);
            }

            $request->validate([
                'updated_genre_name' => 'required|string|max:50|unique:categories,name,' . $genre->id,
            ]);

            return $this->jsonResponse('success', 'Genre valid', 200, ['old_genre_name' => $genre->name]);
        }
        $genre->update([
            'name' => $request->updated_genre_name
        ]);

        return $this->jsonResponse('success', 'Genre updated successfully', 200, ['old_genre_name' => $genre->name]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();

        return $this->jsonResponse('success', 'Genre deleted successfully', 200);
    }
}
