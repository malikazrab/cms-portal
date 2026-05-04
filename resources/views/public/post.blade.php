<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->meta_title ?: $post->title }}</title>
    @if ($post->meta_description)
        <meta name="description" content="{{ $post->meta_description }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <article class="mx-auto max-w-3xl px-4 py-10">
        <a href="{{ route('public.home') }}" class="text-sm text-blue-600 hover:text-blue-700">Back to home</a>
        <h1 class="mt-4 text-4xl font-bold">{{ $post->title }}</h1>
        <div class="mt-2 text-sm text-gray-500">{{ $post->category->name ?? 'Uncategorized' }}</div>
        @if ($post->featured_image)
            <img src="{{ asset('storage/'.$post->featured_image) }}" alt="" class="mt-6 w-full rounded object-cover">
        @endif
        <div class="prose mt-8 max-w-none">
            {!! $post->content !!}
        </div>
    </article>
</body>
</html>
