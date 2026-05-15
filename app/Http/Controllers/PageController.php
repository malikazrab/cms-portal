<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Models\FooterTemplate;
use App\Models\HeaderTemplate;
use App\Models\Menu;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\ActivityLogger;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('user')->orderBy('created_at', 'desc')->paginate(15);

        // Add latest version to each page
        foreach ($pages as $page) {
            $page->latest_version = $page->versions()->max('version_number') ?? 1.0;
        }

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $page = null;
        $editMode = false;

        // Check if we're editing an existing page
        if (request()->has('edit') && request()->edit) {
            $page = Page::findOrFail(request()->edit);
            $editMode = true;
        }

        return view('admin.pages.create', [
            'page' => $page,
            'editMode' => $editMode,
            'headerTemplates' => HeaderTemplate::query()->latest()->get(),
            'footerTemplates' => FooterTemplate::query()->latest()->get(),
            'menus' => Menu::with('topLevelItems')->orderBy('name')->get(),
        ]);
    }

    public function edit(Page $page)
    {
        return redirect()->route('admin.pages.create', ['edit' => $page->id]);
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

        // Create initial version 1.0
        PageVersion::create([
            'page_id' => $page->id,
            'version_number' => 1.0,
            'content' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'status' => $page->status,
                'template' => $page->template,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
            ],
            'change_note' => 'Initial page creation',
            'saved_by' => $request->user()->id,
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

    public function update(StorePageRequest $request, Page $page)
    {
        $oldData = $page->only(['title', 'slug', 'content', 'status', 'template', 'meta_title', 'meta_description']);
        $newData = [
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => $request->input('content') ?? '',
            'status' => $request->input('status'),
            'template' => $request->input('template'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
        ];

        $hasChanges = $oldData != $newData;
        if ($hasChanges) {
            $latestVersion = $page->versions()->max('version_number') ?? 1.0;
            $newVersionNumber = round($latestVersion + 0.1, 1);

            PageVersion::create([
                'page_id' => $page->id,
                'version_number' => $newVersionNumber,
                'content' => $newData,
                'change_note' => 'Page updated',
                'saved_by' => $request->user()->id,
            ]);
        }

        $page->update($newData);

        ActivityLogger::log(
            action: 'page.updated',
            subject: $page,
            description: 'Page updated',
            properties: ['title' => $page->title, 'status' => $page->status],
            user: $request->user()
        );

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
