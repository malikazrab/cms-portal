@php
    $builder = json_decode($pageModel->content ?? '', true);
    $components = is_array($builder['components'] ?? null) ? $builder['components'] : null;
    $background = $builder['pageBackground'] ?? [];
    $globalStyles = $builder['globalStyles'] ?? [];
    $showTitle = $showTitle ?? true;
    $wrapInCard = $wrapInCard ?? true;

    $wrapperClass = $wrapInCard ? 'rounded bg-white p-6 shadow-sm' : '';
    $wrapperStyle = 'background-color: '.($background['color'] ?? $globalStyles['bgColor'] ?? 'transparent').';';

    if (!empty($background['image'] ?? $globalStyles['bgImage'] ?? null)) {
        $wrapperStyle .= "background-image: url('".($background['image'] ?? $globalStyles['bgImage'])."'); background-size: ".($background['size'] ?? 'cover').'; background-position: center;';
    }
@endphp

<article class="{{ $wrapperClass }}" style="{{ $wrapperStyle }}">
    @if ($showTitle)
        <h1 class="mb-8 text-4xl font-bold">{{ $builder['title'] ?? $pageModel->title }}</h1>
    @endif

    @if ($components)
        <div class="space-y-6">
            @foreach ($components as $component)
                @php
                    $type = $component['type'] ?? '';
                    $settings = $component['settings'] ?? $component;
                    $paddingStyle = 'padding: '.($settings['pt'] ?? 0).'px '.($settings['pr'] ?? 0).'px '.($settings['pb'] ?? 0).'px '.($settings['pl'] ?? 0).'px;';
                    $boxStyle = $paddingStyle.'background: '.($settings['bgColor'] ?? 'transparent').'; border-radius: '.($settings['borderRadius'] ?? 0).'px; '.($settings['customCss'] ?? '');
                    $ratio = $settings['ratio'] ?? '16/9';
                    $videoPadding = $ratio === '4/3' ? '75%' : ($ratio === '1/1' ? '100%' : '56.25%');
                    $circleSize = (int) ($settings['size'] ?? 120);
                    $strokeWidth = (int) ($settings['strokeWidth'] ?? 10);
                    $circleRadius = ($circleSize / 2) - $strokeWidth;
                    $circumference = 2 * pi() * $circleRadius;
                    $progress = (int) ($settings['percentage'] ?? $settings['percent'] ?? 75);
                    $dash = $circumference * $progress / 100;
                @endphp

                @switch($type)
                    @case('heading')
                        @php $tag = $settings['tag'] ?? 'h2'; @endphp
                        @if ($tag === 'h1')
                            <h1 class="font-semibold" style="text-align: {{ $settings['alignment'] ?? 'left' }}; color: {{ $settings['color'] ?? '#111827' }}; font-size: {{ $settings['fontSize'] ?? 32 }}px; font-weight: {{ $settings['fontWeight'] ?? 700 }}; margin: 0; {{ $boxStyle }}">
                                {{ $settings['text'] ?? $component['content'] ?? 'Heading' }}
                            </h1>
                        @elseif ($tag === 'h3')
                            <h3 class="font-semibold" style="text-align: {{ $settings['alignment'] ?? 'left' }}; color: {{ $settings['color'] ?? '#111827' }}; font-size: {{ $settings['fontSize'] ?? 32 }}px; font-weight: {{ $settings['fontWeight'] ?? 700 }}; margin: 0; {{ $boxStyle }}">
                                {{ $settings['text'] ?? $component['content'] ?? 'Heading' }}
                            </h3>
                        @elseif ($tag === 'h4')
                            <h4 class="font-semibold" style="text-align: {{ $settings['alignment'] ?? 'left' }}; color: {{ $settings['color'] ?? '#111827' }}; font-size: {{ $settings['fontSize'] ?? 32 }}px; font-weight: {{ $settings['fontWeight'] ?? 700 }}; margin: 0; {{ $boxStyle }}">
                                {{ $settings['text'] ?? $component['content'] ?? 'Heading' }}
                            </h4>
                        @elseif ($tag === 'h5')
                            <h5 class="font-semibold" style="text-align: {{ $settings['alignment'] ?? 'left' }}; color: {{ $settings['color'] ?? '#111827' }}; font-size: {{ $settings['fontSize'] ?? 32 }}px; font-weight: {{ $settings['fontWeight'] ?? 700 }}; margin: 0; {{ $boxStyle }}">
                                {{ $settings['text'] ?? $component['content'] ?? 'Heading' }}
                            </h5>
                        @elseif ($tag === 'h6')
                            <h6 class="font-semibold" style="text-align: {{ $settings['alignment'] ?? 'left' }}; color: {{ $settings['color'] ?? '#111827' }}; font-size: {{ $settings['fontSize'] ?? 32 }}px; font-weight: {{ $settings['fontWeight'] ?? 700 }}; margin: 0; {{ $boxStyle }}">
                                {{ $settings['text'] ?? $component['content'] ?? 'Heading' }}
                            </h6>
                        @else
                            <h2 class="font-semibold" style="text-align: {{ $settings['alignment'] ?? 'left' }}; color: {{ $settings['color'] ?? '#111827' }}; font-size: {{ $settings['fontSize'] ?? 32 }}px; font-weight: {{ $settings['fontWeight'] ?? 700 }}; margin: 0; {{ $boxStyle }}">
                                {{ $settings['text'] ?? $component['content'] ?? 'Heading' }}
                            </h2>
                        @endif
                        @break
                    @case('paragraph')
                        <div style="text-align: {{ $settings['alignment'] ?? 'left' }}; {{ $boxStyle }}" class="prose max-w-none">
                            {!! \Mews\Purifier\Facades\Purifier::clean($settings['content'] ?? $component['content'] ?? '') !!}
                        </div>
                        @break
                    @case('button')
                        <div style="text-align: {{ $settings['alignment'] ?? 'center' }}; {{ $boxStyle }}">
                            <a href="{{ $settings['link'] ?? '#' }}" class="inline-block rounded px-6 py-3 font-medium text-white" style="background-color: {{ $settings['bgColor'] ?? '#2563eb' }}; color: {{ $settings['textColor'] ?? '#ffffff' }};">
                                {{ $settings['text'] ?? $component['text'] ?? 'Click Me' }}
                            </a>
                        </div>
                        @break
                    @case('image')
                        <div style="text-align: {{ $settings['alignment'] ?? 'center' }}; {{ $boxStyle }}">
                            <img src="{{ $settings['url'] ?? $component['src'] ?? '' }}" alt="{{ $settings['alt'] ?? '' }}" class="inline-block max-w-full rounded shadow-sm">
                        </div>
                        @break
                    @case('video')
                        <div style="{{ $boxStyle }}">
                            <div style="position: relative; padding-bottom: {{ $videoPadding }}; height: 0;">
                                <iframe src="{{ $settings['url'] ?? '' }}" style="position:absolute; inset:0; width:100%; height:100%; border:0;" allowfullscreen></iframe>
                            </div>
                        </div>
                        @break
                    @case('icon')
                        <div style="text-align: {{ $settings['alignment'] ?? 'center' }}; {{ $boxStyle }}">
                            <i class="{{ $settings['iconClass'] ?? $component['icon'] ?? 'fas fa-star' }}" style="font-size: {{ $settings['size'] ?? 40 }}px; color: {{ $settings['color'] ?? '#0ea5e9' }}"></i>
                        </div>
                        @break
                    @case('icon-list')
                        <div style="{{ $boxStyle }}">
                            <ul class="space-y-2">
                                @foreach (($settings['items'] ?? []) as $item)
                                    <li class="flex items-center gap-2">
                                        <i class="{{ $item['icon'] ?? 'fas fa-check' }}" style="color: {{ $settings['iconColor'] ?? '#0ea5e9' }}"></i>
                                        <span>{{ $item['text'] ?? '' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @break
                    @case('testimonial')
                        <blockquote class="rounded bg-gray-100 p-8 italic" style="{{ $boxStyle }}">
                            <p>{{ $settings['text'] ?? '' }}</p>
                            <footer class="mt-4 font-bold">{{ $settings['author'] ?? '' }}</footer>
                            @if (!empty($settings['role']))
                                <div class="mt-1 text-sm not-italic text-gray-500">{{ $settings['role'] }}</div>
                            @endif
                        </blockquote>
                        @break
                    @case('team-member')
                        <div class="text-center" style="{{ $boxStyle }}">
                            <img src="{{ $settings['photo'] ?? '' }}" alt="{{ $settings['name'] ?? '' }}" class="mx-auto mb-4 h-24 w-24 rounded-full object-cover">
                            <h3 class="text-xl font-bold">{{ $settings['name'] ?? '' }}</h3>
                            <p class="text-sm text-gray-500">{{ $settings['role'] ?? '' }}</p>
                            <p class="mt-3 text-gray-700">{{ $settings['bio'] ?? '' }}</p>
                        </div>
                        @break
                    @case('pricing')
                        <div class="rounded-2xl border p-8 text-center shadow-sm" style="{{ $boxStyle }}">
                            <h3 class="text-2xl font-bold">{{ $settings['title'] ?? '' }}</h3>
                            <div class="mt-4 text-5xl font-black">{{ $settings['currency'] ?? '$' }}{{ $settings['price'] ?? '' }}<span class="text-base font-medium">{{ $settings['period'] ?? '' }}</span></div>
                            <ul class="mt-6 space-y-2 text-gray-700">
                                @foreach (($settings['features'] ?? []) as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <div class="mt-6">
                                <span class="inline-block rounded bg-blue-600 px-5 py-3 font-medium text-white">{{ $settings['buttonText'] ?? 'Choose Plan' }}</span>
                            </div>
                        </div>
                        @break
                    @case('accordion')
                        <div class="space-y-2" style="{{ $boxStyle }}">
                            @foreach (($settings['items'] ?? []) as $item)
                                <details class="rounded border p-3" @if(!empty($item['open'])) open @endif>
                                    <summary class="font-semibold">{{ $item['title'] ?? 'Accordion Item' }}</summary>
                                    <div class="prose mt-2 max-w-none">{!! \Mews\Purifier\Facades\Purifier::clean($item['content'] ?? '') !!}</div>
                                </details>
                            @endforeach
                        </div>
                        @break
                    @case('tabs')
                        <div style="{{ $boxStyle }}">
                            <div class="mb-4 flex flex-wrap gap-2 border-b pb-2">
                                @foreach (($settings['items'] ?? []) as $index => $item)
                                    <span class="rounded-full px-3 py-1 text-sm {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $item['label'] ?? 'Tab' }}</span>
                                @endforeach
                            </div>
                            <div class="prose max-w-none">{!! \Mews\Purifier\Facades\Purifier::clean(($settings['items'][0]['content'] ?? '')) !!}</div>
                        </div>
                        @break
                    @case('counter')
                        <div class="text-center" style="{{ $boxStyle }}">
                            <div style="font-size: {{ $settings['fontSize'] ?? 48 }}px; color: {{ $settings['color'] ?? '#0ea5e9' }};" class="font-black">
                                {{ $settings['prefix'] ?? '' }}{{ $settings['end'] ?? 100 }}{{ $settings['suffix'] ?? '+' }}
                            </div>
                            <p class="mt-1 text-gray-500">{{ $settings['label'] ?? '' }}</p>
                        </div>
                        @break
                    @case('progress-bar')
                    @case('progress')
                        <div style="{{ $boxStyle }}">
                            <div class="mb-1 flex justify-between text-sm">
                                <span>{{ $settings['label'] ?? 'Progress' }}</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-gray-200">
                                <div class="h-3 rounded-full" style="width: {{ $progress }}%; background-color: {{ $settings['color'] ?? '#2563eb' }}"></div>
                            </div>
                        </div>
                        @break
                    @case('circle-progress')
                        <div class="text-center" style="{{ $boxStyle }}">
                            <svg width="{{ $circleSize }}" height="{{ $circleSize }}" style="transform: rotate(-90deg)">
                                <circle cx="{{ $circleSize / 2 }}" cy="{{ $circleSize / 2 }}" r="{{ $circleRadius }}" fill="none" stroke="#e2e8f0" stroke-width="{{ $strokeWidth }}"></circle>
                                <circle cx="{{ $circleSize / 2 }}" cy="{{ $circleSize / 2 }}" r="{{ $circleRadius }}" fill="none" stroke="{{ $settings['color'] ?? '#0ea5e9' }}" stroke-width="{{ $strokeWidth }}" stroke-dasharray="{{ $dash }} {{ $circumference }}" stroke-linecap="round"></circle>
                            </svg>
                            <p class="mt-2">{{ $progress }}%</p>
                        </div>
                        @break
                    @case('countdown')
                        <div class="flex justify-center gap-4 text-center" style="{{ $boxStyle }}">
                            <div><div class="text-3xl font-black" style="color: {{ $settings['color'] ?? '#0ea5e9' }}">30</div><div class="text-xs">Days</div></div>
                            <div><div class="text-3xl font-black" style="color: {{ $settings['color'] ?? '#0ea5e9' }}">12</div><div class="text-xs">Hours</div></div>
                            <div><div class="text-3xl font-black" style="color: {{ $settings['color'] ?? '#0ea5e9' }}">45</div><div class="text-xs">Mins</div></div>
                            <div><div class="text-3xl font-black" style="color: {{ $settings['color'] ?? '#0ea5e9' }}">30</div><div class="text-xs">Secs</div></div>
                        </div>
                        @break
                    @case('image-carousel')
                        <div style="{{ $boxStyle }}">
                            <img src="{{ $settings['images'][0] ?? '' }}" alt="" class="w-full rounded">
                        </div>
                        @break
                    @case('before-after')
                        <div class="grid gap-4 md:grid-cols-2" style="{{ $boxStyle }}">
                            <img src="{{ $settings['beforeUrl'] ?? '' }}" alt="Before" class="w-full rounded">
                            <img src="{{ $settings['afterUrl'] ?? '' }}" alt="After" class="w-full rounded">
                        </div>
                        @break
                    @case('google-maps')
                        <div style="{{ $boxStyle }}">
                            <iframe width="100%" height="{{ $settings['height'] ?? 400 }}" frameborder="0" src="https://maps.google.com/maps?q={{ urlencode($settings['address'] ?? 'New York') }}&output=embed"></iframe>
                        </div>
                        @break
                    @case('contact-form')
                        <form class="grid gap-3 rounded border bg-gray-50 p-4" style="{{ $boxStyle }}">
                            <h3 class="text-lg font-semibold">{{ $settings['title'] ?? 'Contact Us' }}</h3>
                            <input type="text" placeholder="Name" class="rounded border-gray-300">
                            <input type="email" placeholder="Email" class="rounded border-gray-300">
                            <textarea rows="4" placeholder="Message" class="rounded border-gray-300"></textarea>
                            <button type="button" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white">{{ $settings['submitText'] ?? 'Send Message' }}</button>
                        </form>
                        @break
                    @case('subscribe-form')
                        <div class="flex gap-2" style="{{ $boxStyle }}">
                            <input type="email" placeholder="{{ $settings['placeholder'] ?? 'Enter your email' }}" class="flex-1 rounded border-gray-300">
                            <button type="button" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white">{{ $settings['buttonText'] ?? 'Subscribe' }}</button>
                        </div>
                        @break
                    @case('search-form')
                        <form action="{{ route('public.blog') }}" method="GET" class="flex gap-2" style="{{ $boxStyle }}">
                            <input type="search" name="search" placeholder="{{ $settings['placeholder'] ?? 'Search...' }}" class="flex-1 rounded border-gray-300">
                            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white">{{ $settings['buttonText'] ?? 'Search' }}</button>
                        </form>
                        @break
                    @case('raw-html')
                        <div style="{{ $boxStyle }}">
                            {!! \Mews\Purifier\Facades\Purifier::clean($settings['code'] ?? '') !!}
                        </div>
                        @break
                    @case('spacer')
                        <div style="height: {{ $settings['height'] ?? 40 }}px; {{ $boxStyle }}"></div>
                        @break
                    @case('divider')
                        <hr style="border-top: {{ $settings['thickness'] ?? 1 }}px {{ $settings['style'] ?? 'solid' }} {{ $settings['color'] ?? '#e2e8f0' }}; width: {{ $settings['width'] ?? 100 }}%; margin: 0 auto;">
                        @break
                    @case('columns')
                        <div class="grid gap-6 md:grid-cols-2" style="{{ $boxStyle }}">
                            <div class="prose max-w-none rounded border bg-gray-50 p-4">{!! \Mews\Purifier\Facades\Purifier::clean($settings['col1Content'] ?? '<p>Column 1</p>') !!}</div>
                            <div class="prose max-w-none rounded border bg-gray-50 p-4">{!! \Mews\Purifier\Facades\Purifier::clean($settings['col2Content'] ?? '<p>Column 2</p>') !!}</div>
                        </div>
                        @break
                    @default
                        <div style="{{ $boxStyle }}" class="prose max-w-none">
                            {!! \Mews\Purifier\Facades\Purifier::clean($settings['content'] ?? $component['content'] ?? '') !!}
                        </div>
                @endswitch
            @endforeach
        </div>
    @else
        <div class="prose max-w-none">{!! \Mews\Purifier\Facades\Purifier::clean($pageModel->content ?? '') !!}</div>
    @endif
</article>
