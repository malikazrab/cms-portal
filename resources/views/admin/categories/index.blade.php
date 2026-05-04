@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1fr_360px]">
    <div class="rounded bg-white p-6 shadow-sm">
        <h1 class="mb-4 text-2xl font-semibold text-gray-900">Categories</h1>
        <div class="divide-y">
            @forelse ($categories as $category)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <div class="font-medium text-gray-900">{{ $category->name }}</div>
                        <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                        @if ($category->description)
                            <div class="mt-1 text-sm text-gray-600">{{ $category->description }}</div>
                        @endif
                    </div>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700">Delete</button>
                    </form>
                </div>
            @empty
                <p class="py-8 text-center text-gray-500">No categories yet.</p>
            @endforelse
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="rounded bg-white p-6 shadow-sm">
        @csrf
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add Category</h2>
        <div class="space-y-4">
            <input name="name" value="{{ old('name') }}" class="w-full rounded border-gray-300" placeholder="Category name" required>
            <textarea name="description" rows="4" class="w-full rounded border-gray-300" placeholder="Description">{{ old('description') }}</textarea>
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Category</button>
        </div>
    </form>
</div>
@endsection
