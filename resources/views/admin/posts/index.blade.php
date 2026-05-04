@extends('layouts.admin')

@section('title', 'Posts')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Posts</h1>
        <a href="{{ route('admin.posts.create') }}" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">New Post</a>
    </div>

    <form method="GET" class="mb-4">
        <input name="search" value="{{ request('search') }}" placeholder="Search posts" class="w-full rounded border-gray-300 md:w-80">
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-3 py-2">Title</th>
                    <th class="px-3 py-2">Category</th>
                    <th class="px-3 py-2">Public URL</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $post->title }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $post->category->name ?? '-' }}</td>
                        <td class="px-3 py-2 text-gray-600">
                            @if ($post->status === 'published')
                                <a href="{{ route('public.post', $post->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-700">/blog/{{ $post->slug }}</a>
                            @else
                                <span>/blog/{{ $post->slug }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <span class="rounded px-2 py-1 text-xs font-medium {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : ($post->status === 'archived' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($post->status) }}</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-gray-500">No posts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
</div>
@endsection
