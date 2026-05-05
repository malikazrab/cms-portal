<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ActivityLogger;
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

        $category = Category::create([
            'name'        => $request->name,
            'slug'        => generateSlug($request->name),
            'description' => $request->description
        ]);

        ActivityLogger::log(
            action: 'category.created',
            subject: $category,
            description: 'Category created',
            properties: ['name' => $category->name],
            user: $request->user()
        );

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category added successfully.');
    }

    public function destroy(Category $category)
    {
        // Optionally reassign posts to uncategorized or prevent delete if used
        $name = $category->name;
        $category->delete();

        ActivityLogger::log(
            action: 'category.deleted',
            description: 'Category deleted',
            properties: ['name' => $name],
            user: request()->user()
        );

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}
