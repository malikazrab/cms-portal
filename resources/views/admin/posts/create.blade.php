@extends('layouts.admin')

@section('title', 'New Post')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">New Post</h1>
        <button type="submit" form="postForm" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Post</button>
    </div>

    <form id="postForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1fr_320px]">
        @csrf

        <div class="space-y-4">
            <input id="titleInput" name="title" value="{{ old('title') }}" class="w-full rounded border-gray-300" placeholder="Post title" required>

            <div class="flex gap-2">
                <input id="slugInput" name="slug" value="{{ old('slug') }}" class="flex-1 rounded border-gray-300" placeholder="post-slug" required>
                <button type="button" id="generateSlugBtn" class="rounded bg-gray-100 px-3 text-sm hover:bg-gray-200">Auto</button>
            </div>

            <textarea name="content" rows="12" class="w-full rounded border-gray-300" placeholder="Content" required>{{ old('content') }}</textarea>
            <textarea name="excerpt" rows="3" class="w-full rounded border-gray-300" placeholder="Excerpt">{{ old('excerpt') }}</textarea>
            <input name="meta_title" value="{{ old('meta_title') }}" class="w-full rounded border-gray-300" placeholder="Meta title">
            <textarea name="meta_description" rows="3" class="w-full rounded border-gray-300" placeholder="Meta description">{{ old('meta_description') }}</textarea>
        </div>

        <aside class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full rounded border-gray-300">
                    <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status') === 'published')>Published</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" class="w-full rounded border-gray-300" required>
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tags</label>
                <div class="space-y-1">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tags', [])))>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Featured Image</label>
                <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" class="w-full text-sm">
                <p class="mt-1 text-xs text-gray-500">Allowed: JPG, PNG, WEBP, GIF, AVIF. Current upload limit: 100MB.</p>
            </div>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('generateSlugBtn').addEventListener('click', async () => {
    const title = document.getElementById('titleInput').value;
    const response = await fetch('{{ route('admin.slug.generate') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({title})
    });
    const data = await response.json();
    document.getElementById('slugInput').value = data.slug;
});
</script>
@endpush
