<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use App\Models\Menu;  // <--- ADD THIS LINE
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();
        $totalPages = Page::count();
        $publishedPages = Page::where('status', 'published')->count();
        $draftPages = Page::where('status', 'draft')->count();
        $latestPages = Page::latest()->take(5)->get();
        $latestPosts = Post::with('category')->latest()->take(5)->get();
        
        // For Menu Widget
        $menus = Menu::withCount('menuItems')->get();

        return view('admin.dashboard', compact(
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'totalPages',
            'publishedPages',
            'draftPages',
            'latestPages',
            'latestPosts',
            'menus'
        ));
    }
}