@extends('layouts.public')

@section('title', $post->meta_title ?: $post->title)

@if ($post->meta_description)
    @section('meta_description', $post->meta_description)
@endif

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-10">
        <a href="{{ route('public.blog') }}" class="text-sm text-blue-600 hover:text-blue-700">Back to blog</a>
        <h1 class="mt-4 text-4xl font-bold">{{ $post->title }}</h1>
        <div class="mt-2 text-sm text-gray-500">{{ $post->category->name ?? 'Uncategorized' }}</div>
        @if ($post->featured_image_url)
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="mt-6 w-full rounded object-cover">
        @endif
        <div class="prose mt-8 max-w-none">
            {!! $post->content !!}
        </div>
    </article>
@endsection
