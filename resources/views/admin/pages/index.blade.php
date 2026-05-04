@extends('layouts.admin')

@section('title', 'Pages')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Pages</h1>
        <a href="{{ route('admin.pages.create') }}" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">New Page</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-3 py-2">Title</th>
                    <th class="px-3 py-2">Public URL</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Author</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($pages as $page)
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $page->title }}</td>
                        <td class="px-3 py-2 text-gray-600">
                            @if ($page->status === 'published')
                                <a href="{{ route('public.page', $page->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-700">/pages/{{ $page->slug }}</a>
                            @else
                                <span>/pages/{{ $page->slug }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <span class="rounded px-2 py-1 text-xs font-medium {{ $page->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($page->status) }}</span>
                        </td>
                        <td class="px-3 py-2 text-gray-600">{{ $page->user->name ?? '-' }}</td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Delete this page?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-gray-500">No pages yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $pages->links() }}</div>
</div>
@endsection
