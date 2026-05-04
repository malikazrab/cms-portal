@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Users</h1>
            <p class="mt-2 text-sm text-gray-600">Manage users, roles, and permissions.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Add User</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-50 p-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 rounded bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Email</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Role</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-3 py-3 text-gray-900">{{ $user->name }}</td>
                        <td class="px-3 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-3 py-3">
                            <span class="rounded bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex gap-2">
                                @if (auth()->id() === $user->id)
                                    <span class="text-xs text-gray-500">Your account</span>
                                @else
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-200">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded bg-red-100 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-200">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
