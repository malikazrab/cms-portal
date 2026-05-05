<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class RoleManagementController extends Controller
{
    /**
     * Display the roles and permissions management page.
     */
    public function index(): View
    {
        $roles = User::availableRoles();
        $allPermissions = User::getAllPermissions();
        
        $rolePermissions = [];
        foreach ($roles as $roleKey => $roleName) {
            $rolePermissions[$roleKey] = [
                'name' => $roleName,
                'permissions' => User::getRolePermissions($roleKey),
            ];
        }

        return view('admin.roles.index', [
            'roles' => $roles,
            'rolePermissions' => $rolePermissions,
            'allPermissions' => $allPermissions,
        ]);
    }

    /**
     * Display details for a specific role.
     */
    public function show(string $roleKey): View
    {
        $roles = User::availableRoles();

        abort_unless(array_key_exists($roleKey, $roles), 404);

        $permissions = User::getRolePermissions($roleKey);
        $allPermissions = User::getAllPermissions();

        // Get users with this role who have custom permissions
        $usersWithCustomPermissions = User::where('role', $roleKey)
            ->whereNotNull('custom_permissions')
            ->get();

        return view('admin.roles.show', [
            'roleKey' => $roleKey,
            'roleName' => $roles[$roleKey],
            'permissions' => $permissions,
            'allPermissions' => $allPermissions,
            'usersWithCustomPermissions' => $usersWithCustomPermissions,
        ]);
    }
}
