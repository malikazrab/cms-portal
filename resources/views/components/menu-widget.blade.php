@props(['menuId' => null])

@php
    $menu = \App\Models\Menu::with('menuItems.children')->find($menuId);
@endphp

@if($menu && $menu->menuItems)
    <ul class="menu-widget {{ $attributes->get('class', 'flex space-x-4') }}">
        @foreach($menu->menuItems->where('parent_id', null)->sortBy('sort_order') as $item)
            <li class="menu-item {{ $item->children->count() ? 'has-dropdown' : '' }}">
                <a href="{{ $item->url ?? ($item->page ? url('/pages/' . $item->page->slug) : '#') }}" 
                   class="menu-link">
                    {{ $item->label }}
                </a>
                
                @if($item->children->count())
                    <ul class="dropdown-menu">
                        @foreach($item->children->sortBy('sort_order') as $child)
                            <li>
                                <a href="{{ $child->url ?? ($child->page ? url('/pages/' . $child->page->slug) : '#') }}">
                                    {{ $child->label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
@endif