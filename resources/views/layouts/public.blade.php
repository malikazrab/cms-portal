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
<body class="bg-gray-50 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-5 px-4 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('public.home') }}" class="text-2xl font-semibold text-gray-900">{{ $siteName ?? config('app.name', 'CMS Portal') }}</a>
                @if (!empty($siteDescription))
                    <p class="mt-1 text-sm text-gray-600">{{ $siteDescription }}</p>
                @endif
            </div>

            <nav class="flex flex-wrap items-center gap-3 text-sm font-medium text-gray-700">
                <a href="{{ route('public.home') }}" class="rounded-full px-3 py-2 hover:bg-gray-100">Home</a>
                <a href="{{ route('public.blog') }}" class="rounded-full px-3 py-2 hover:bg-gray-100">Blog</a>
                @foreach ($navigationPages as $navPage)
                    <a href="{{ route('public.page', $navPage->slug) }}" class="rounded-full px-3 py-2 hover:bg-gray-100">{{ $navPage->title }}</a>
                @endforeach
            </nav>
        </div>

        @if (!empty($headerPage))
            <div class="border-t border-gray-100 bg-white">
                <div class="mx-auto max-w-6xl px-4 py-6">
                    @include('public.partials.builder-content', ['pageModel' => $headerPage, 'wrapInCard' => false, 'showTitle' => false])
                </div>
            </div>
        @endif
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-12 border-t border-gray-200 bg-white">
        @if (!empty($footerPage))
            <div class="mx-auto max-w-6xl px-4 py-8">
                @include('public.partials.builder-content', ['pageModel' => $footerPage, 'wrapInCard' => false, 'showTitle' => false])
            </div>
        @else
            <div class="mx-auto max-w-6xl px-4 py-8 text-sm text-gray-500">
                {{ $siteName ?? config('app.name', 'CMS Portal') }}
            </div>
        @endif
    </footer>
</body>
</html>
