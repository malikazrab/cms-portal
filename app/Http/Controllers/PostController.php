<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\StorePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category']);

        // ✅ Fix Error 4: get() -> input()
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(StorePostRequest $request)
    {
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')
                                         ->store('posts/images', 'public');
        }

        $post = Post::create([
            // ✅ Fix Error 1: auth()->user()->id
           'user_id' => auth('web')->id(),
            // ✅ Fix Error 2: ->input() use karo
            'title'            => $request->input('title'),
            'slug'             => $request->input('slug'),
            'content'          => $request->input('content'),
            'excerpt'          => $request->input('excerpt'),
            'featured_image'   => $featuredImagePath,
            'status'           => $request->input('status'),
            'category_id'      => $request->input('category_id'),
            'meta_title'       => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'published_at'     => $request->input('status') === 'published' ? now() : null,
        ]);

        if ($request->has('tags')) {
            $post->tags()->sync($request->input('tags'));
        }

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $selectedTags = $post->tags->pluck('id')->toArray();
        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'selectedTags'));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $featuredImagePath = $post->featured_image;

        if ($request->hasFile('featured_image')) {
            if ($featuredImagePath && Storage::disk('public')->exists($featuredImagePath)) {
                Storage::disk('public')->delete($featuredImagePath);
            }
            $featuredImagePath = $request->file('featured_image')
                                          ->store('posts/images', 'public');
        }

        // ✅ Fix Error 3: ->input() use karo
        $post->update([
            'title'            => $request->input('title'),
            'slug'             => $request->input('slug'),
            'content'          => $request->input('content'),
            'excerpt'          => $request->input('excerpt'),
            'featured_image'   => $featuredImagePath,
            'status'           => $request->input('status'),
            'category_id'      => $request->input('category_id'),
            'meta_title'       => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'published_at'     => $request->input('status') === 'published' && !$post->published_at
                                    ? now()
                                    : $post->published_at,
        ]);

        $post->tags()->sync($request->input('tags') ?? []);

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Post deleted successfully.');
    }
}