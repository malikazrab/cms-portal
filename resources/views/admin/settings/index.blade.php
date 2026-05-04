@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <h1 class="mb-4 text-2xl font-semibold text-gray-900">Settings</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700" for="site_name">Site Name</label>
            <input id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}" class="w-full rounded border-gray-300" required>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700" for="site_description">Site Description</label>
            <textarea id="site_description" name="site_description" rows="3" class="w-full rounded border-gray-300">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="posts_per_page">Posts Per Page</label>
                <input id="posts_per_page" name="posts_per_page" type="number" min="1" max="100" value="{{ old('posts_per_page', $settings['posts_per_page'] ?? 10) }}" class="w-full rounded border-gray-300">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="admin_email">Admin Email</label>
                <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email', $settings['admin_email'] ?? '') }}" class="w-full rounded border-gray-300">
            </div>
        </div>

        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save Settings</button>
    </form>
</div>
@endsection
