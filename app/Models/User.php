<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'custom_permissions'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_POST_EDITOR = 'post_editor';
    public const ROLE_PAGE_EDITOR = 'page_editor';

    protected const ROLE_PERMISSIONS = [
        self::ROLE_ADMIN => ['*'],
        self::ROLE_EDITOR => [
            'admin.access',
            'dashboard.view',
            'posts.view',
            'posts.create',
            'posts.update',
            'posts.delete',
            'pages.view',
            'pages.create',
            'pages.update',
            'pages.delete',
            'media.view',
            'media.upload',
            'media.delete',
            'categories.view',
            'categories.create',
            'activity.view',
        ],
        self::ROLE_POST_EDITOR => [
            'admin.access',
            'dashboard.view',
            'posts.view',
            'posts.create',
            'posts.update',
            'posts.delete',
            'media.view',
            'media.upload',
            'media.delete',
            'categories.view',
        ],
        self::ROLE_PAGE_EDITOR => [
            'admin.access',
            'dashboard.view',
            'pages.view',
            'pages.create',
            'pages.update',
            'pages.delete',
            'media.view',
            'media.upload',
            'media.delete',
        ],
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'custom_permissions' => 'array',
        ];
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasPermission(string $permission): bool
    {
        // If user has custom permissions, check those first
        if ($this->custom_permissions && in_array($permission, $this->custom_permissions)) {
            return true;
        }

        // Fall back to role-based permissions
        $permissions = self::ROLE_PERMISSIONS[$this->role] ?? [];

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    /**
     * Get effective permissions (role + custom).
     */
    public function getEffectivePermissions(): array
    {
        // If admin, return all
        if ($this->role === self::ROLE_ADMIN) {
            return ['*'];
        }

        // Combine role permissions with custom permissions
        $rolePermissions = self::ROLE_PERMISSIONS[$this->role] ?? [];
        $customPermissions = $this->custom_permissions ?? [];

        return array_unique(array_merge($rolePermissions, $customPermissions));
    }

    /**
     * Get all permissions for a specific role.
     */
    public static function getRolePermissions(string $role): array
    {
        return self::ROLE_PERMISSIONS[$role] ?? [];
    }

    /**
     * Get all available permissions in the system.
     */
    public static function getAllPermissions(): array
    {
        return [
            'admin.access' => 'Access Admin Panel',
            'dashboard.view' => 'View Dashboard',
            'posts.view' => 'View Posts',
            'posts.create' => 'Create Posts',
            'posts.update' => 'Update Posts',
            'posts.delete' => 'Delete Posts',
            'pages.view' => 'View Pages',
            'pages.create' => 'Create Pages',
            'pages.update' => 'Update Pages',
            'pages.delete' => 'Delete Pages',
            'media.view' => 'View Media',
            'media.upload' => 'Upload Media',
            'media.delete' => 'Delete Media',
            'categories.view' => 'View Categories',
            'categories.create' => 'Create Categories',
            'tags.view' => 'View Tags',
            'tags.create' => 'Create Tags',
            'settings.manage' => 'Manage Settings',
            'users.manage' => 'Manage Users',
            'activity.view' => 'View Activity Logs',
        ];
    }

    public static function availableRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_POST_EDITOR => 'Post Editor',
            self::ROLE_PAGE_EDITOR => 'Page Editor',
        ];
    }
}
