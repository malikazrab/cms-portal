<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    public function delete(User $user, Post $post)
    {
        // Only admin can delete any post, editors can delete only their own
        return $user->role === 'admin' || $user->id === $post->user_id;
    }

    public function update(User $user, Post $post)
    {
        return $user->role === 'admin' || $user->id === $post->user_id;
    }
}