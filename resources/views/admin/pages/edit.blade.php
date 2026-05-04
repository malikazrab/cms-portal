@extends('layouts.admin')

@section('title', 'Edit Page')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Page</h1>
        <a href="{{ route('admin.pages.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Back to pages</a>
    </div>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="mb-1 block text-sm font-medium text-gray-700">Title</label>
            <input id="title" name="title" value="{{ old('title', $page->title) }}" class="w-full rounded border-gray-300" required>
        </div>

        <div>
            <label for="slug" class="mb-1 block text-sm font-medium text-gray-700">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $page->slug) }}" class="w-full rounded border-gray-300" required>
        </div>

        <div>
            <label for="content" class="mb-1 block text-sm font-medium text-gray-700">Content</label>
            <textarea id="content" name="content" rows="14" class="w-full rounded border-gray-300 font-mono text-sm">{{ old('content', $page->content) }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Pages created with the v3 builder are stored here as builder JSON.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="w-full rounded border-gray-300">
                    <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $page->status) === 'published')>Published</option>
                </select>
            </div>
            <div>
                <label for="template" class="mb-1 block text-sm font-medium text-gray-700">Template</label>
                <input id="template" name="template" value="{{ old('template', $page->template) }}" class="w-full rounded border-gray-300">
            </div>
        </div>

        <div>
            <label for="meta_title" class="mb-1 block text-sm font-medium text-gray-700">Meta Title</label>
            <input id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="w-full rounded border-gray-300">
        </div>

        <div>
            <label for="meta_description" class="mb-1 block text-sm font-medium text-gray-700">Meta Description</label>
            <textarea id="meta_description" name="meta_description" rows="3" class="w-full rounded border-gray-300">{{ old('meta_description', $page->meta_description) }}</textarea>
        </div>

        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Update Page</button>
    </form>
</div>
@endsection
