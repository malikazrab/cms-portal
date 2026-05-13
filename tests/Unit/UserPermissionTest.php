<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserPermissionTest extends TestCase
{
    public function test_post_editor_custom_permissions_override_default_role_permissions(): void
    {
        $user = new User([
            'role' => User::ROLE_POST_EDITOR,
            'custom_permissions' => ['posts.view', 'posts.update'],
        ]);

        $this->assertTrue($user->hasPermission('admin.access'));
        $this->assertTrue($user->hasPermission('dashboard.view'));
        $this->assertTrue($user->hasPermission('posts.view'));
        $this->assertTrue($user->hasPermission('posts.update'));
        $this->assertFalse($user->hasPermission('posts.create'));
        $this->assertFalse($user->hasPermission('posts.delete'));
        $this->assertFalse($user->hasPermission('settings.manage'));
    }

    public function test_post_editor_without_custom_permissions_uses_default_role_permissions(): void
    {
        $user = new User([
            'role' => User::ROLE_POST_EDITOR,
            'custom_permissions' => [],
        ]);

        $this->assertTrue($user->hasPermission('posts.create'));
        $this->assertTrue($user->hasPermission('posts.delete'));
        $this->assertFalse($user->hasPermission('settings.manage'));
    }

    public function test_post_editor_custom_permissions_do_not_allow_admin_scope_permissions(): void
    {
        $allowedPermissions = User::getAllowedCustomPermissionsForRole(User::ROLE_POST_EDITOR);

        $this->assertContains('posts.view', $allowedPermissions);
        $this->assertContains('media.view', $allowedPermissions);
        $this->assertContains('categories.view', $allowedPermissions);
        $this->assertNotContains('settings.manage', $allowedPermissions);
        $this->assertNotContains('users.manage', $allowedPermissions);
    }
}
