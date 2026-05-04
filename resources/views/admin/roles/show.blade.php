@extends('layouts.admin')

@section('title', 'Role: ' . $roleName)

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $roleName }} Role</h1>
            <p class="mt-2 text-sm text-gray-600">Detailed view of permissions for the {{ $roleName }} role.</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
            Back to Roles
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Assigned Permissions -->
        <div class="rounded border border-green-200 bg-green-50 p-6">
            <h2 class="mb-4 text-lg font-semibold text-green-900">Assigned Permissions ({{ count($permissions) }})</h2>

            @if ($roleKey === 'admin')
                <div class="rounded bg-green-100 p-4 text-center text-sm text-green-800">
                    <p class="font-semibold">✓ Full System Access</p>
                    <p class="mt-1 text-xs">This role has access to all features and functionality.</p>
                </div>
            @else
                <div class="space-y-2">
                    @forelse ($permissions as $permission)
                        <div class="flex items-start gap-3 rounded bg-white p-3">
                            <span class="mt-1 inline-block h-2 w-2 rounded-full bg-green-600"></span>
                            <div>
                                <p class="font-medium text-gray-900">{{ $allPermissions[$permission] ?? $permission }}</p>
                                <p class="text-xs text-gray-600">{{ $permission }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-600">No permissions assigned</p>
                    @endforelse
                </div>
            @endif
        </div>

        <!-- Not Assigned Permissions -->
        <div class="rounded border border-red-200 bg-red-50 p-6">
            <h2 class="mb-4 text-lg font-semibold text-red-900">Not Assigned Permissions ({{ count(array_diff(array_keys($allPermissions), $permissions)) }})</h2>

            @if ($roleKey === 'admin')
                <div class="rounded bg-red-100 p-4 text-center text-sm text-red-800">
                    <p class="font-semibold">No restricted permissions</p>
                    <p class="mt-1 text-xs">This role has full access to all system features.</p>
                </div>
            @else
                <div class="space-y-2">
                    @forelse (array_diff(array_keys($allPermissions), $permissions) as $permission)
                        <div class="flex items-start gap-3 rounded bg-white p-3">
                            <span class="mt-1 inline-block h-2 w-2 rounded-full bg-red-600"></span>
                            <div>
                                <p class="font-medium text-gray-900">{{ $allPermissions[$permission] ?? $permission }}</p>
                                <p class="text-xs text-gray-600">{{ $permission }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-600">All permissions assigned</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    <!-- Permission Categories Summary -->
    <div class="mt-8">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Permission Summary by Category</h2>
        
        <div class="grid gap-4 lg:grid-cols-3">
            @php
                $categories = [
                    'admin' => ['admin.access'],
                    'dashboard' => ['dashboard.view'],
                    'posts' => ['posts.view', 'posts.create', 'posts.update', 'posts.delete'],
                    'pages' => ['pages.view', 'pages.create', 'pages.update', 'pages.delete'],
                    'media' => ['media.view', 'media.upload', 'media.delete'],
                    'categories' => ['categories.view', 'categories.create'],
                    'tags' => ['tags.view', 'tags.create'],
                    'settings' => ['settings.manage'],
                    'users' => ['users.manage'],
                    'activity' => ['activity.view'],
                ];
            @endphp

            @foreach ($categories as $category => $perms)
                @php
                    $hasAll = count(array_intersect($perms, $permissions)) === count($perms);
                    $hasSome = count(array_intersect($perms, $permissions)) > 0;
                    $granted = count(array_intersect($perms, $permissions));
                    $total = count($perms);
                @endphp

                <div class="rounded border p-4 {{ $hasAll ? 'border-green-200 bg-green-50' : ($hasSome ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-gray-50') }}">
                    <div class="flex items-center justify-between">
                        <h3 class="font-medium text-gray-900 capitalize">{{ $category }}</h3>
                        <span class="rounded text-xs font-semibold {{ $hasAll ? 'bg-green-200 text-green-800' : ($hasSome ? 'bg-yellow-200 text-yellow-800' : 'bg-gray-200 text-gray-800') }}">
                            {{ $granted }}/{{ $total }}
                        </span>
                    </div>
                    <div class="mt-2 text-xs text-gray-600">
                        @if ($hasAll)
                            <p class="text-green-700">✓ Full access to {{ $category }}</p>
                        @elseif ($hasSome)
                            <p class="text-yellow-700">⚠ Partial access to {{ $category }}</p>
                        @else
                            <p class="text-red-700">✗ No access to {{ $category }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-8 rounded bg-blue-50 p-4 text-sm text-blue-800">
        <p><strong>Note:</strong> This role configuration is defined in the application code. To modify permissions for a role, please contact your system administrator or update the role permissions in the application settings.</p>
    </div>
</div>
@endsection
