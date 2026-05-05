@extends('layouts.admin')

@section('title', 'Edit Post')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Post</h1>
        <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Back to posts</a>
    </div>

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Title</label>
            <input name="title" value="{{ old('title', $post->title) }}" class="w-full rounded border-gray-300" placeholder="Post title" required>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">URL Slug</label>
            <input name="slug" value="{{ old('slug', $post->slug) }}" class="w-full rounded border-gray-300" placeholder="post-slug" required>
            <p class="mt-1 text-xs text-gray-500">Published post URL: <a href="{{ route('public.post', old('slug', $post->slug)) }}" target="_blank" class="text-blue-600 hover:text-blue-700">/blog/{{ old('slug', $post->slug) }}</a></p>
        </div>
        <textarea name="content" rows="12" class="w-full rounded border-gray-300">{{ old('content', $post->content) }}</textarea>
        <textarea name="excerpt" rows="3" class="w-full rounded border-gray-300" placeholder="Excerpt">{{ old('excerpt', $post->excerpt) }}</textarea>

        <div class="grid gap-4 md:grid-cols-2">
            <select name="status" class="rounded border-gray-300">
                <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                <option value="archived" @selected(old('status', $post->status) === 'archived')>Archived</option>
            </select>
            <select name="category_id" class="rounded border-gray-300">
                <option value="">No category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Tags</label>
            <div class="grid gap-2 md:grid-cols-3">
                @foreach ($tags as $tag)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tags', $selectedTags)))>
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <input name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="w-full rounded border-gray-300" placeholder="Meta title">
        <textarea name="meta_description" rows="3" class="w-full rounded border-gray-300" placeholder="Meta description">{{ old('meta_description', $post->meta_description) }}</textarea>
        <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" class="block w-full text-sm">
        <p class="mt-1 text-xs text-gray-500">Allowed: JPG, PNG, WEBP, GIF, AVIF. Current upload limit: 100MB.</p>

        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Update Post</button>
    </form>
</div>
@endsection
