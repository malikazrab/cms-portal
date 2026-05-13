<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all non-admin users.
     */
    public function index(): View
    {
        $users = User::where('role', '!=', User::ROLE_ADMIN)
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => User::availableRoles(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => User::availableRoles(),
            'allPermissions' => User::getAllPermissions(),
            'customPermissionGroupsByRole' => [
                User::ROLE_POST_EDITOR => User::getCustomPermissionGroupsForRole(User::ROLE_POST_EDITOR),
                User::ROLE_PAGE_EDITOR => User::getCustomPermissionGroupsForRole(User::ROLE_PAGE_EDITOR),
            ],
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserFormRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'custom_permissions' => User::normalizeCustomPermissionsForRole(
                $validated['role'],
                $validated['custom_permissions'] ?? []
            ),
        ]);

        ActivityLogger::log(
            action: 'user.created',
            subject: $user,
            description: 'New user created',
            properties: [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
            user: $request->user()
        );

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => User::availableRoles(),
            'allPermissions' => User::getAllPermissions(),
            'customPermissionGroupsByRole' => [
                User::ROLE_POST_EDITOR => User::getCustomPermissionGroupsForRole(User::ROLE_POST_EDITOR),
                User::ROLE_PAGE_EDITOR => User::getCustomPermissionGroupsForRole(User::ROLE_PAGE_EDITOR),
            ],
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['error' => 'You cannot modify your own account from this page. Please use the profile settings instead.']);
        }

        $validated = $request->validated();
        $changes = [];

        // Track what changed
        if ($user->name !== $validated['name']) {
            $changes['name'] = ['old' => $user->name, 'new' => $validated['name']];
        }
        if ($user->email !== $validated['email']) {
            $changes['email'] = ['old' => $user->email, 'new' => $validated['email']];
        }
        if ($user->role !== $validated['role']) {
            $changes['role'] = ['old' => $user->role, 'new' => $validated['role']];
        }
        if (($user->custom_permissions ?? []) !== ($validated['custom_permissions'] ?? [])) {
            $changes['custom_permissions'] = [
                'old' => $user->custom_permissions,
                'new' => $validated['custom_permissions']
            ];
        }

        // Update user
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->custom_permissions = User::normalizeCustomPermissionsForRole(
            $validated['role'],
            $validated['custom_permissions'] ?? []
        );

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
            $changes['password_changed'] = true;
        }

        $user->save();

        ActivityLogger::log(
            action: 'user.updated',
            subject: $user,
            description: 'User information updated',
            properties: [
                'target_user_id' => $user->id,
                'changes' => $changes,
            ],
            user: $request->user()
        );

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $user->delete();

        ActivityLogger::log(
            action: 'user.deleted',
            description: 'User account deleted',
            properties: [
                'deleted_user' => $userData,
            ],
            user: $request->user()
        );

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:'.implode(',', array_keys(User::availableRoles())),
        ]);

        if ($request->user()->is($user)) {
            return back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        $oldRole = $user->role;
        $user->role = $validated['role'];
        $user->save();

        ActivityLogger::log(
            action: 'user.role_updated',
            subject: $user,
            description: 'User role updated',
            properties: [
                'target_user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $user->role,
            ],
            user: $request->user()
        );

        return back()->with('success', 'User role updated successfully.');
    }
}
