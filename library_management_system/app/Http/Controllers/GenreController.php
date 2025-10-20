<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Genre;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'genre_name' => 'required|string|max:50|unique:categories,name|unique:genres,name',
                'category_id' => 'required|string|exists:categories,id'
            ]);

            return $this->jsonResponse('valid', 'Validation passed');
        }

        try {
            $category_name = Category::findOrFail($request->category_id)->name;

            DB::transaction(function () use ($request, $category_name) {
                $genre = Genre::create([
                    'name' => $request->genre_name,
                    'category_id' => $request->category_id
                ]);

                ActivityLog::create([
                    'action' => 'created',
                    'details' => 'Created genre "' . $request->genre_name . '" for the category: ' . $category_name,
                    'entity_type' => 'Genre',
                    'entity_id' => $genre->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            return $this->jsonResponse('success', 'Genre created successfully', 201);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Unable to create genre', 500);
        }
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

        try {
            $oldName = $genre->name;

            DB::transaction(function () use ($request, $genre, $oldName) {
                $genre->update([
                    'name' => $request->updated_genre_name
                ]);

                ActivityLog::create([
                    'action' => 'updated',
                    'details' => 'Updated genre from: "' . $oldName . '" to: "' . $request->updated_genre_name . '" 
                                  under the category: ' . $genre->category->name,
                    'entity_type' => 'Genre',
                    'entity_id' => $genre->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            return $this->jsonResponse('success', 'Genre updated successfully', 200, ['old_genre_name' => $genre->name]);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Unable to update genre', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Genre $genre)
    {
        try {
            $category_name = $genre->category->name;
            DB::transaction(function () use ($genre, $request, $category_name) {
                $genre->delete();

                ActivityLog::create([
                    'action' => 'deleted',
                    'details' => 'Deleted genre: "' . $genre->name . '" under the category: ' . $category_name,
                    'entity_type' => 'Genre',
                    'entity_id' => $genre->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            return $this->jsonResponse('success', 'Genre deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Unable to delete genre', 500);
        }
    }
}
