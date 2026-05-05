@extends('layouts.public')

@section('title', ($siteName ?? config('app.name', 'CMS Portal')).' Blog')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10">
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Blog</h1>
            <p class="mt-2 text-gray-600">Posts are listed here separately from the home page.</p>
        </div>

        <div class="space-y-6">
            @forelse ($posts as $post)
                <article class="overflow-hidden rounded bg-white shadow-sm">
                    @if ($post->featured_image_url)
                        <a href="{{ route('public.post', $post->slug) }}" class="block">
                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="h-56 w-full object-cover">
                        </a>
                    @endif
                    <div class="p-6">
                    <h2 class="text-xl font-semibold">
                        <a href="{{ route('public.post', $post->slug) }}" class="hover:text-blue-600">{{ $post->title }}</a>
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">{{ $post->category->name ?? 'Uncategorized' }}</p>
                    @if ($post->excerpt)
                        <p class="mt-3 text-gray-700">{{ $post->excerpt }}</p>
                    @endif
                    </div>
                </article>
            @empty
                <p class="rounded bg-white p-6 text-gray-600 shadow-sm">No published posts yet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $posts->links() }}</div>
    </section>
@endsection
