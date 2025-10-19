<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $categories = Category::all();
        $genres = Genre::all();

        if ($user->role === 'librarian') {
            // $categories = Category::all();
            return view('pages.librarian.category-management', ['categories' => $categories, 'genres' => $genres]);
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
                'category_name' => 'required|string|max:50|unique:categories,name',
            ]);

            return $this->jsonResponse('valid', 'Validation passed');
        }

        try {

            DB::transaction(function () use ($request) {

                $category = Category::create([
                    'name' => $request->category_name
                ]);

                ActivityLog::create([
                    'action' => 'created',
                    'details' => 'Created category: ' . $request->category_name,
                    'entity_type' => 'Category',
                    'entity_id' => $category->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            return $this->jsonResponse('success', 'Category created successfully', 201);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Unable to create category', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if ($request->validate_only) {
            if ($category->name === $request->updated_category_name) {
                return $this->jsonResponse('unchanged', 'No changes detected', 200);
            }

            $request->validate([
                'updated_category_name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            ]);

            return $this->jsonResponse('success', 'Category valid', 200, ['old_category_name' => $category->name]);
        }

        try {
            $oldCategoryName = $category->name;

            DB::transaction(function () use ($category, $request, $oldCategoryName) {
                $category->update([
                    'name' => $request->updated_category_name
                ]);

                ActivityLog::create([
                    'action' => 'updated',
                    'details' => 'Updated category: From "' . $oldCategoryName . '" to "' . $request->updated_category_name . '"',
                    'entity_type' => 'Category',
                    'entity_id' => $category->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            // return using the saved old name
            return $this->jsonResponse('success', 'Category updated successfully', 200, ['old_category_name' => $oldCategoryName]);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        try {
            DB::transaction(function () use ($category, $request) {
                $category->delete();

                ActivityLog::create([
                    'action' => 'deleted',
                    'details' => 'Deleted category: ' . $category->name,
                    'entity_type' => 'Category',
                    'entity_id' => $category->id,
                    'user_id' => $request->user()->id,
                ]);
            });

            return $this->jsonResponse('success', 'Category deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Unable to delete category', 500);
        }
    }
}
