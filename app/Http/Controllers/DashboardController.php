<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();
        $totalPages = Page::count();

        return view('admin.dashboard', compact(
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'totalPages'
        ));
    }
}