{{-- Default Theme - Home Page --}}
<div>
    {{-- Hero Section --}}
    <section class="py-20 text-center" style="background: linear-gradient(135deg, {{ $themeSettings['colors']['primary'] ?? '#0ea5e9' }}, {{ $themeSettings['colors']['secondary'] ?? '#8b5cf6' }});">
        <div class="mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4" style="font-family: {{ $themeSettings['fonts']['heading'] ?? 'Inter, sans-serif' }}">
                Welcome to {{ $siteName ?? 'CMS Portal' }}
            </h1>
            <p class="text-xl text-white opacity-90 mb-8 max-w-2xl mx-auto">
                {{ $siteDescription ?? 'A modern content management system' }}
            </p>
            <a href="{{ route('public.blog') }}" class="inline-block bg-white text-gray-900 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Explore Blog
            </a>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-16 bg-white">
        <div class="mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12" style="color: {{ $themeSettings['colors']['text'] ?? '#1e293b' }}">
                Features
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-lg border border-gray-200 hover:shadow-lg transition">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: {{ $themeSettings['colors']['primary'] ?? '#0ea5e9' }}20">
                        <i class="fas fa-bolt text-xl" style="color: {{ $themeSettings['colors']['primary'] ?? '#0ea5e9' }}"></i>
                    </div>
                    <h3 class="font-bold mb-2">Fast & Reliable</h3>
                    <p class="text-gray-600 text-sm">Built with Laravel for maximum performance.</p>
                </div>
                <div class="text-center p-6 rounded-lg border border-gray-200 hover:shadow-lg transition">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: {{ $themeSettings['colors']['secondary'] ?? '#8b5cf6' }}20">
                        <i class="fas fa-shield-alt text-xl" style="color: {{ $themeSettings['colors']['secondary'] ?? '#8b5cf6' }}"></i>
                    </div>
                    <h3 class="font-bold mb-2">Secure</h3>
                    <p class="text-gray-600 text-sm">Enterprise-grade security for your content.</p>
                </div>
                <div class="text-center p-6 rounded-lg border border-gray-200 hover:shadow-lg transition">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: {{ $themeSettings['colors']['accent'] ?? '#f59e0b' }}20">
                        <i class="fas fa-paint-brush text-xl" style="color: {{ $themeSettings['colors']['accent'] ?? '#f59e0b' }}"></i>
                    </div>
                    <h3 class="font-bold mb-2">Customizable</h3>
                    <p class="text-gray-600 text-sm">Easily customize colors, fonts, and layout.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Latest Posts --}}
    <section class="py-16 bg-gray-50">
        <div class="mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12" style="color: {{ $themeSettings['colors']['text'] ?? '#1e293b' }}">
                Latest Blog Posts
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($latestPosts ?? [] as $post)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="font-bold mb-2">
                                <a href="{{ route('public.post', $post->slug) }}" class="hover:text-blue-600">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-600 text-sm">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                            <span class="text-xs text-gray-400 mt-2 block">{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>