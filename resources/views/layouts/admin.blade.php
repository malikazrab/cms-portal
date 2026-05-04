<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name', 'CMS Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <header class="border-b bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold text-gray-900">CMS Portal</a>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span>{{ auth()->user()->email ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-6 lg:grid-cols-[220px_1fr]">
            <aside class="rounded bg-white p-4 shadow-sm">
                <nav class="space-y-1 text-sm">
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.posts.index') }}">Posts</a>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.posts.create') }}">New Post</a>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.pages.index') }}">Pages</a>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.pages.create') }}">New Page</a>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.media.index') }}">Media</a>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.categories.index') }}">Categories</a>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100" href="{{ route('admin.settings.index') }}">Settings</a>
                </nav>
            </aside>

            <main>
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
