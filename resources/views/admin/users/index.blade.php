@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Users</h1>
        <p class="mt-2 text-sm text-gray-600">Manage each user's role and permissions.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Name</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Email</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Current Role</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Change Role</th>
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
                            @if (auth()->id() === $user->id)
                                <span class="text-xs text-gray-500">You cannot change your own role.</span>
                            @else
                                <form action="{{ route('admin.users.role.update', $user) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="rounded border-gray-300 text-sm">
                                        @foreach ($roles as $value => $label)
                                            <option value="{{ $value }}" @selected($user->role === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">Update</button>
                                </form>
                            @endif
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
