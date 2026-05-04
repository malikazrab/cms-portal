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
    </section>
@endsection
