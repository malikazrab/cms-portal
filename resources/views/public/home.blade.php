@extends('layouts.public')

@section('title', $siteName ?? config('app.name', 'CMS Portal'))

@if (!empty($siteDescription))
    @section('meta_description', $siteDescription)
@endif

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:py-16">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm sm:p-14">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">Default Home</p>
            <h1 class="mt-5 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">{{ $siteName ?? config('app.name', 'CMS Portal') }}</h1>
            <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-slate-600">
                {{ $siteDescription ?: 'Choose a page as the home page from admin settings, then edit it in the page builder. Posts stay available on the blog page.' }}
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <a href="{{ route('public.blog') }}" class="rounded-full bg-slate-900 px-6 py-3 font-medium text-white transition hover:bg-slate-800">Open Blog</a>
            </div>
        </div>

        @if (!empty($pages) && $pages->isNotEmpty())
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($pages as $page)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <h2 class="text-xl font-semibold text-slate-900">{{ $page->title }}</h2>
                        <div class="mt-4 text-sm text-slate-600">
                            <a href="{{ route('public.page', $page->slug) }}" class="font-medium text-slate-900 hover:text-slate-700">Open page</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
