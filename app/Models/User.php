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

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';

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
        $permissions = self::ROLE_PERMISSIONS[$this->role] ?? [];

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    public static function availableRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_EDITOR => 'Editor',
        ];
    }
}
