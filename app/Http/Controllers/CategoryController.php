<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create([
            'name'        => $request->name,
            'slug'        => generateSlug($request->name),
            'description' => $request->description
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category added successfully.');
    }

    public function destroy(Category $category)
    {
        // Optionally reassign posts to uncategorized or prevent delete if used
        $category->delete();
        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}