@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Roles & Permissions</h1>
        <p class="mt-2 text-sm text-gray-600">View and manage what each user role can do in the system.</p>
    </div>

    <div class="grid gap-6">
        @foreach ($rolePermissions as $roleKey => $roleData)
            <div class="rounded border border-gray-200 p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $roleData['name'] }}</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            <span class="font-medium">{{ count($roleData['permissions']) }}</span> permission(s) assigned
                        </p>
                    </div>
                    <a href="{{ route('admin.roles.show', $roleKey) }}" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        View Details
                    </a>
                </div>

                @if ($roleKey === 'admin')
                    <div class="rounded bg-blue-50 p-3 text-sm text-blue-700">
                        ✓ Full access to all features
                    </div>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($roleData['permissions'] as $permission)
                            @if (isset($allPermissions[$permission]))
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                    ✓ {{ $allPermissions[$permission] }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800">
                                    {{ $permission }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8 rounded bg-blue-50 p-4">
        <h3 class="font-semibold text-blue-900">Available Roles:</h3>
        <ul class="mt-2 space-y-1 text-sm text-blue-800">
            <li><strong>Admin:</strong> Full system access, can manage everything including users and roles.</li>
            <li><strong>Editor:</strong> Can manage posts, pages, media, and categories.</li>
            <li><strong>Post Editor:</strong> Can only manage posts, media, and categories.</li>
            <li><strong>Page Editor:</strong> Can only manage pages and media.</li>
        </ul>
    </div>
</div>
@endsection
