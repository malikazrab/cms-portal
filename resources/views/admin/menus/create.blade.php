@extends('layouts.admin')

@section('title', 'Create Menu')

@section('content')
<div class="space-y-6">
    <div class="rounded bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Create New Menu</h1>
                <p class="mt-2 text-gray-600">Create a new navigation menu.</p>
            </div>
            <a href="{{ route('admin.menus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Menus
            </a>
        </div>
    </div>

    <div class="rounded bg-white p-6 shadow-sm">
        <form action="{{ route('admin.menus.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Menu Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                           placeholder="e.g., Main Menu, Footer Menu, Mobile Menu" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Set as default menu</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Only one menu can be default at a time.</p>
                </div>
                
                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                        Create Menu
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection