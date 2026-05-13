@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="rounded bg-white p-6 shadow-sm">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edit User</h1>
            <p class="mt-2 text-sm text-gray-600">Update user information, role, and password.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-50 p-4 text-sm text-red-700">
            <p class="font-medium">Please fix the following errors:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="w-full rounded border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                placeholder="John Doe"
                required
                autofocus
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                class="w-full rounded border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                placeholder="user@example.com"
                required
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-sm font-medium text-gray-900 mb-4">Change Password (Optional)</h3>
            <p class="text-sm text-gray-600 mb-4">Leave password fields empty to keep the current password.</p>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full rounded border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Enter new password (minimum 8 characters)"
                >
                <p class="mt-1 text-xs text-gray-500">Minimum 8 characters required</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="w-full rounded border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Confirm new password"
                >
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select
                id="role"
                name="role"
                class="w-full rounded border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                required
            >
                <option value="">Select a role</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Custom Permissions Section -->
        <div id="permissionsSection" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-3">Custom Permissions</label>
            <p class="text-sm text-gray-600 mb-4">Choose only the permissions this user should have within their editor role. If none are selected, the role's default permissions will be used.</p>

            <div id="permissionGroupsContainer" class="grid gap-4 md:grid-cols-2"></div>

            @error('custom_permissions')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4 pt-4">
            <button
                type="submit"
                class="rounded bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                Update User
            </button>
            <a
                href="{{ route('admin.users.index') }}"
                class="rounded bg-gray-200 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
            >
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const roleField = document.getElementById('role');
const permissionsSection = document.getElementById('permissionsSection');
const permissionGroupsContainer = document.getElementById('permissionGroupsContainer');
const allPermissions = @json($allPermissions);
const customPermissionGroupsByRole = @json($customPermissionGroupsByRole);
const selectedPermissions = new Set(@json(old('custom_permissions', $user->custom_permissions ?? [])));

function renderPermissionGroups(selectedRole) {
    const groups = customPermissionGroupsByRole[selectedRole] || null;

    if (!groups) {
        permissionsSection.classList.add('hidden');
        permissionGroupsContainer.innerHTML = '';
        return;
    }

    permissionsSection.classList.remove('hidden');

    permissionGroupsContainer.innerHTML = Object.entries(groups).map(([groupName, permissions]) => {
        const items = permissions
            .filter((permission) => allPermissions[permission])
            .map((permission) => `
                <label class="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        name="custom_permissions[]"
                        value="${permission}"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        ${selectedPermissions.has(permission) ? 'checked' : ''}
                    >
                    <span>${allPermissions[permission]}</span>
                </label>
            `)
            .join('');

        return `
            <div class="rounded border border-gray-200 p-4">
                <h4 class="font-medium text-gray-900 mb-3">${groupName}</h4>
                <div class="space-y-2">${items}</div>
            </div>
        `;
    }).join('');
}

roleField.addEventListener('change', function() {
    selectedPermissions.clear();
    renderPermissionGroups(this.value);
});

renderPermissionGroups(roleField.value);
</script>
@endpush
