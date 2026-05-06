@extends('layouts.public')

@section('title', $siteName ?? config('app.name', 'CMS Portal'))

@if (!empty($siteDescription))
    @section('meta_description', $siteDescription)
@endif

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16">
        <div class="rounded bg-white p-10 text-center shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-600">Default Home</p>
            <h1 class="mt-4 text-4xl font-bold text-gray-900">{{ $siteName ?? config('app.name', 'CMS Portal') }}</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600">
                {{ $siteDescription ?: 'Choose a page as the home page from admin settings, then edit it in the page builder. Posts stay available on the blog page.' }}
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('public.blog') }}" class="rounded bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">Open Blog</a>
            </div>
        </div>

        @if (!empty($pages) && $pages->isNotEmpty())
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pages as $page)
                    <article class="rounded border bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $page->title }}</h2>
                        <div class="mt-4 text-sm text-gray-600">
                            <a href="{{ route('public.page', $page->slug) }}" class="text-blue-600 hover:text-blue-700">Open page</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
