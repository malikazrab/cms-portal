@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="rounded bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage every draft and published page or post from the tabs below.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <a href="{{ route('admin.pages.index') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Pages</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalPages }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $publishedPages }} published, {{ $draftPages }} draft</p>
        </a>
        <a href="{{ route('admin.posts.index') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Posts</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalPosts }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $publishedPosts }} published, {{ $draftPosts }} draft</p>
        </a>
        <a href="{{ route('admin.pages.create') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Create</p>
            <p class="mt-2 text-lg font-semibold text-blue-700">New Page</p>
            <p class="mt-1 text-xs text-gray-500">Open the page builder</p>
        </a>
        <a href="{{ route('admin.posts.create') }}" class="rounded bg-white p-5 shadow-sm hover:shadow">
            <p class="text-sm text-gray-500">Create</p>
            <p class="mt-2 text-lg font-semibold text-blue-700">New Post</p>
            <p class="mt-1 text-xs text-gray-500">Write a blog post</p>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Latest Pages</h2>
                <a href="{{ route('admin.pages.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View all</a>
            </div>
            <div class="space-y-3">
                @forelse ($latestPages as $page)
                    <div class="flex items-center justify-between gap-4 border-b pb-3 last:border-b-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $page->title }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($page->status) }} · /pages/{{ $page->slug }}</p>
                        </div>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No pages yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Latest Posts</h2>
                <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View all</a>
            </div>
            <div class="space-y-3">
                @forelse ($latestPosts as $post)
                    <div class="flex items-center justify-between gap-4 border-b pb-3 last:border-b-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $post->title }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($post->status) }} · /blog/{{ $post->slug }}</p>
                        </div>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-blue-600 hover:text-blue-700">Edit</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
