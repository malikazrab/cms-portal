<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;              // ← ye add karo
use App\Policies\PostPolicy;      // ← ye add karo

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [        // ← ye property pehle se hogi, bas isme add karo
        Post::class => PostPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}