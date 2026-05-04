<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => User::availableRoles(),
        ]);
    }

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
