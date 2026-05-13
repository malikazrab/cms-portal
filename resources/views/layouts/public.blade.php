<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteName ?? config('app.name', 'CMS Portal'))</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-stone-50 text-slate-900">
    <div class="relative min-h-screen overflow-x-hidden">

    <header class="relative border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('public.home') }}" class="text-2xl font-semibold tracking-tight text-slate-900">{{ $siteName ?? config('app.name', 'CMS Portal') }}</a>
                @if (!empty($siteDescription))
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $siteDescription }}</p>
                @endif
            </div>

            <div class="rounded-full border border-slate-200 bg-white p-2">
                <nav class="flex flex-wrap items-center gap-1 text-sm font-medium text-slate-700 lg:justify-end">
                    <a href="{{ route('public.home') }}" class="rounded-full px-4 py-2 transition hover:bg-slate-100">Home</a>
                    <a href="{{ route('public.blog') }}" class="rounded-full px-4 py-2 transition hover:bg-slate-100">Blog</a>
                    @foreach ($navigationPages as $navPage)
                        <a href="{{ route('public.page', $navPage->slug) }}" class="rounded-full px-4 py-2 transition hover:bg-slate-100">{{ $navPage->title }}</a>
                    @endforeach
                </nav>
            </div>
        </div>

        @if (!empty($headerPage))
            <div class="border-t border-slate-100 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-6">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
                    @include('public.partials.builder-content', ['pageModel' => $headerPage, 'wrapInCard' => false, 'showTitle' => false])
                    </div>
                </div>
            </div>
        @endif
    </header>

    <main class="relative flex-1 py-8 sm:py-10">
        @yield('content')
    </main>

    <footer class="relative mt-8 border-t border-slate-200 bg-white text-slate-900">
        @if (!empty($footerPage))
            <div class="mx-auto max-w-7xl px-4 py-8 sm:py-10">
                <div class="py-2">
                    @include('public.partials.builder-content', ['pageModel' => $footerPage, 'wrapInCard' => false, 'showTitle' => false])
                </div>
            </div>
        @else
            <div class="mx-auto max-w-7xl px-4 py-10">
                <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-8 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-lg font-semibold text-slate-900">{{ $siteName ?? config('app.name', 'CMS Portal') }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $siteDescription ?: 'Built with the CMS Portal publishing tools.' }}</p>
                    </div>
                    <p class="text-sm text-slate-500">All rights reserved.</p>
                </div>
            </div>
        @endif
    </footer>
</div>
</body>
</html>
