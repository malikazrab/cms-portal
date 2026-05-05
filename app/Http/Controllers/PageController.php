<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Models\Page;
use App\Services\ActivityLogger;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('user')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create', ['page' => null]);
    }

    public function store(StorePageRequest $request)
    {
        $page = Page::create([
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => $request->input('content') ?? '',
            'status' => $request->input('status'),
            'template' => $request->input('template'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
        ]);

        ActivityLogger::log(
            action: 'page.created',
            subject: $page,
            description: 'Page created',
            properties: ['title' => $page->title, 'status' => $page->status],
            user: $request->user()
        );

        // Return JSON for fetch requests, redirect for form submissions
        if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
            return response()->json([
                'success' => true,
                'page_id' => $page->id,
                'message' => 'Page created successfully.',
            ]);
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.create', compact('page'));
    }

    public function update(StorePageRequest $request, Page $page)
    {
        $page->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => $request->input('content') ?? '',
            'status' => $request->input('status'),
            'template' => $request->input('template'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
        ]);

        ActivityLogger::log(
            action: 'page.updated',
            subject: $page,
            description: 'Page updated',
            properties: ['title' => $page->title, 'status' => $page->status],
            user: $request->user()
        );

        // Return JSON for fetch requests, redirect for form submissions
        if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
            return response()->json([
                'success' => true,
                'page_id' => $page->id,
                'message' => 'Page updated successfully.',
            ]);
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $pageId = $page->id;
        $title = $page->title;

        $page->delete();

        ActivityLogger::log(
            action: 'page.deleted',
            description: 'Page deleted',
            properties: ['page_id' => $pageId, 'title' => $title],
            user: request()->user()
        );

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
