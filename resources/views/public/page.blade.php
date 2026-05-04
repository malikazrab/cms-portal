@php
    $builder = json_decode($page->content, true);
    $components = is_array($builder['components'] ?? null) ? $builder['components'] : null;
    $background = $builder['pageBackground'] ?? [];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title ?: $page->title }}</title>
    @if ($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="mx-auto max-w-5xl px-4 py-10">
        <article class="rounded bg-white p-6 shadow-sm"
            style="background-color: {{ $background['color'] ?? '#ffffff' }}; @if(!empty($background['image'])) background-image: url('{{ $background['image'] }}'); background-size: {{ $background['size'] ?? 'cover' }}; background-position: center; @endif">
            <h1 class="mb-8 text-4xl font-bold">{{ $builder['title'] ?? $page->title }}</h1>

            @if ($components)
                <div class="space-y-6">
                    @foreach ($components as $component)
                        @switch($component['type'] ?? '')
                            @case('heading')
                                <h2 class="text-2xl font-semibold" style="text-align: {{ $component['align'] ?? 'left' }}; color: {{ $component['color'] ?? '#111827' }}">{{ $component['content'] ?? 'Heading' }}</h2>
                                @break
                            @case('paragraph')
                                <div class="prose max-w-none">{!! \Mews\Purifier\Facades\Purifier::clean($component['content'] ?? '') !!}</div>
                                @break
                            @case('image')
                                <img src="{{ $component['src'] ?? '' }}" alt="" class="max-w-full rounded shadow-sm">
                                @break
                            @case('button')
                                <a href="{{ $component['link'] ?? '#' }}" class="inline-block rounded px-6 py-2 font-medium text-white" style="background-color: {{ $component['bgColor'] ?? '#2563eb' }}">{{ $component['text'] ?? 'Click Me' }}</a>
                                @break
                            @case('iconbox')
                                <div class="rounded p-6 text-center" style="background-color: {{ $component['bgColor'] ?? '#f8fafc' }}">
                                    <i class="{{ $component['icon'] ?? 'fa-regular fa-star' }}" style="font-size: 48px; color: {{ $component['iconColor'] ?? '#2563eb' }}"></i>
                                    <h3 class="mt-3 text-xl font-bold">{{ $component['title'] ?? 'Icon Title' }}</h3>
                                    <p class="mt-2 text-gray-600">{{ $component['description'] ?? '' }}</p>
                                </div>
                                @break
                            @case('testimonial')
                                <blockquote class="rounded bg-gray-100 p-8 italic">
                                    <p>{{ $component['text'] ?? '' }}</p>
                                    <footer class="mt-4 font-bold">{{ $component['author'] ?? '' }}</footer>
                                </blockquote>
                                @break
                            @case('accordion')
                                <div class="space-y-2">
                                    @foreach (($component['items'] ?? []) as $item)
                                        <details class="rounded border p-3">
                                            <summary class="font-semibold">{{ $item['title'] ?? 'Accordion Item' }}</summary>
                                            <div class="prose mt-2 max-w-none">{!! \Mews\Purifier\Facades\Purifier::clean($item['content'] ?? '') !!}</div>
                                        </details>
                                    @endforeach
                                </div>
                                @break
                            @case('progress')
                                <div>
                                    <div class="mb-1 flex justify-between text-sm"><span>{{ $component['label'] ?? 'Progress' }}</span><span>{{ $component['percent'] ?? 75 }}%</span></div>
                                    <div class="h-3 overflow-hidden rounded-full bg-gray-200"><div class="h-3 rounded-full" style="width: {{ $component['percent'] ?? 75 }}%; background-color: {{ $component['color'] ?? '#2563eb' }}"></div></div>
                                </div>
                                @break
                            @case('columns')
                                <div class="grid gap-6 md:grid-cols-2">
                                    <div class="prose max-w-none rounded border bg-gray-50 p-4">{!! \Mews\Purifier\Facades\Purifier::clean($component['col1Content'] ?? '') !!}</div>
                                    <div class="prose max-w-none rounded border bg-gray-50 p-4">{!! \Mews\Purifier\Facades\Purifier::clean($component['col2Content'] ?? '') !!}</div>
                                </div>
                                @break
                            @case('social')
                                <div class="flex flex-wrap gap-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white">Facebook</a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="rounded bg-sky-500 px-4 py-2 text-sm font-medium text-white">Twitter</a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="rounded bg-blue-800 px-4 py-2 text-sm font-medium text-white">LinkedIn</a>
                                </div>
                                @break
                            @case('contactform')
                                <form class="grid gap-3 rounded border bg-gray-50 p-4">
                                    <input type="text" placeholder="Name" class="rounded border-gray-300">
                                    <input type="email" placeholder="Email" class="rounded border-gray-300">
                                    <textarea rows="4" placeholder="Message" class="rounded border-gray-300"></textarea>
                                    <button type="button" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white">Send Message</button>
                                </form>
                                @break
                        @endswitch
                    @endforeach
                </div>
            @else
                <div class="prose max-w-none">{!! \Mews\Purifier\Facades\Purifier::clean($page->content) !!}</div>
            @endif
        </article>
    </main>
</body>
</html>
