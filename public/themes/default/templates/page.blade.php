{{-- Default Theme - Single Page --}}
<div class="mx-auto px-4 py-12">
    <div class="@if($themeSettings['layout']['sidebar'] ?? 'right' === 'right') grid grid-cols-1 lg:grid-cols-3 gap-8 @endif">
        
        {{-- Main Content --}}
        <div class="@if($themeSettings['layout']['sidebar'] ?? 'right' === 'right') lg:col-span-2 @endif">
            <article>
                <h1 class="text-3xl font-bold mb-6" style="font-family: {{ $themeSettings['fonts']['heading'] ?? 'Inter, sans-serif' }}; color: {{ $themeSettings['colors']['text'] ?? '#1e293b' }}">
                    {{ $page->title ?? 'Page Title' }}
                </h1>
                
                @if(isset($page->featured_image))
                    <img src="{{ asset('storage/' . $page->featured_image) }}" alt="{{ $page->title }}" class="w-full rounded-lg mb-6">
                @endif
                
                <div class="prose max-w-none" style="font-family: {{ $themeSettings['fonts']['body'] ?? 'Inter, sans-serif' }}; font-size: {{ $themeSettings['fonts']['body_size'] ?? '16px' }}">
                    {!! $page->content ?? '<p>No content yet.</p>' !!}
                </div>
            </article>
        </div>
        
        {{-- Sidebar (if enabled) --}}
        @if($themeSettings['layout']['sidebar'] ?? 'right' !== 'none')
            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="font-bold mb-4">Navigation</h3>
                    <ul class="space-y-2">
                        @foreach ($navigationPages ?? [] as $navPage)
                            <li>
                                <a href="{{ route('public.page', $navPage->slug) }}" 
                                   class="block py-1 px-2 rounded hover:bg-gray-200 text-sm {{ isset($page) && $page->id === $navPage->id ? 'bg-gray-200 font-bold' : '' }}">
                                    {{ $navPage->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
    </div>
</div>