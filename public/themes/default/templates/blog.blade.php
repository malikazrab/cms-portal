{{-- Default Theme - Blog Listing --}}
<div class="mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8" style="font-family: {{ $themeSettings['fonts']['heading'] ?? 'Inter, sans-serif' }}; color: {{ $themeSettings['colors']['text'] ?? '#1e293b' }}">
        Blog
    </h1>
    
    @if(isset($posts) && count($posts) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($posts as $post)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        @if($post->category)
                            <span class="text-xs font-bold uppercase px-2 py-1 rounded" style="background: {{ $themeSettings['colors']['primary'] ?? '#0ea5e9' }}20; color: {{ $themeSettings['colors']['primary'] ?? '#0ea5e9' }}">
                                {{ $post->category->name }}
                            </span>
                        @endif
                        <h2 class="font-bold mt-2 mb-2">
                            <a href="{{ route('public.post', $post->slug) }}" class="hover:text-blue-600">{{ $post->title }}</a>
                        </h2>
                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit(strip_tags($post->content), 120) }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                            <span>{{ $post->user->name ?? 'Admin' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $posts->links() ?? '' }}
        </div>
    @else
        <div class="text-center py-16 text-gray-500">
            <i class="fas fa-newspaper text-6xl mb-4 block"></i>
            <p class="text-lg">No blog posts yet.</p>
        </div>
    @endif
</div>