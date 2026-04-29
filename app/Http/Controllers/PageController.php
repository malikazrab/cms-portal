<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Http\Requests\StorePageRequest;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request)
    {
        Page::create([
            'user_id' => request()->user()->id,       // ✅ Fix 1: proper key add ki
            'title'            => $request->input('title'),  // ✅ Fix 2: input() use kiya
            'slug'             => $request->input('slug'),
            'content'          => $request->input('content'), // ✅ content missing tha
            'status'           => $request->input('status'),
            'template'         => $request->input('template'),
            'meta_title'       => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
        ]);

        return redirect()->route('admin.pages.index')
                         ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(StorePageRequest $request, Page $page)
    {
        $page->update([
            'title'            => $request->input('title'),  // ✅ Fix 3: input() use kiya
            'slug'             => $request->input('slug'),
            'content'          => $request->input('content'),
            'status'           => $request->input('status'),
            'template'         => $request->input('template'),
            'meta_title'       => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
        ]);

        return redirect()->route('admin.pages.index')
                         ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')
                         ->with('success', 'Page deleted successfully.');
    }
}