<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $posts = Post::with('user', 'category')
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('public.home', compact('posts'));
    }

    public function showPost($slug)
    {
        $post = Post::with('user', 'category', 'tags')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('public.post', compact('post'));
    }

    public function showPage($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('public.page', compact('page'));
    }
}