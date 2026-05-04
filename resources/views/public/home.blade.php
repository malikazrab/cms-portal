<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CMS Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="mx-auto max-w-4xl px-4 py-10">
        <h1 class="text-3xl font-bold">{{ config('app.name', 'CMS Portal') }}</h1>
        <div class="mt-8 space-y-6">
            @forelse ($posts as $post)
                <article class="rounded bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold">
                        <a href="{{ route('public.post', $post->slug) }}" class="hover:text-blue-600">{{ $post->title }}</a>
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">{{ $post->category->name ?? 'Uncategorized' }}</p>
                    @if ($post->excerpt)
                        <p class="mt-3 text-gray-700">{{ $post->excerpt }}</p>
                    @endif
                </article>
            @empty
                <p class="rounded bg-white p-6 text-gray-600 shadow-sm">No published posts yet.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    </main>
</body>
</html>
