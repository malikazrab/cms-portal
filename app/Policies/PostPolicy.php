<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    public function update(User $user, Post $post)
    {
        return $user->role === User::ROLE_ADMIN || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post)
    {
        return $user->role === User::ROLE_ADMIN || $user->id === $post->user_id;
    }
}
