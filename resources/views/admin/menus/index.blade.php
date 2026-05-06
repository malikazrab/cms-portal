@extends('layouts.admin')

@section('title', 'Menus')

@section('content')
<div class="space-y-6">
    <div class="rounded bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Menus</h1>
                <p class="mt-2 text-gray-600">Manage navigation menus for your website.</p>
            </div>
            <a href="{{ route('admin.menus.create') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Menu
            </a>
        </div>
    </div>

    <div class="rounded bg-white shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse ($menus as $menu)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-medium text-gray-900">{{ $menu->name }}</h3>
                                @if($menu->is_default)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Default
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                                <span>{{ $menu->items_count }} items</span>
                                <span>Slug: {{ $menu->slug }}</span>
                                <span>Created: {{ $menu->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$menu->is_default)
                                <form action="{{ route('admin.menus.set-default', $menu) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-500 hover:text-green-600 transition-colors" title="Set as Default">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="p-2 text-gray-500 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline" onsubmit="return confirm('Delete this menu? All menu items will also be deleted.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-500 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <p class="text-gray-500">No menus created yet.</p>
                    <a href="{{ route('admin.menus.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-700">
                        Create your first menu →
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection