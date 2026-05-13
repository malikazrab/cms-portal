<header class="bg-white border-b border-gray-200">
    <div class="mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route('public.home') }}" class="text-2xl font-bold" style="color: {{ $themeSettings['colors']['primary'] ?? '#0ea5e9' }}">
            {{ $siteName ?? 'CMS Portal' }}
        </a>
        <nav class="flex gap-6">
            <a href="{{ route('public.home') }}" class="hover:text-blue-600">Home</a>
            <a href="{{ route('public.blog') }}" class="hover:text-blue-600">Blog</a>
            @foreach ($navigationPages as $page)
                <a href="{{ route('public.page', $page->slug) }}" class="hover:text-blue-600">{{ $page->title }}</a>
            @endforeach
        </nav>
    </div>
</header>