@props(['slug', 'class' => ''])

@php
    $menu = \App\Models\Menu::getBySlug($slug);
@endphp

@if($menu)
<nav {{ $attributes->merge(['class' => $class]) }}>
    <ul class="menu-list">
        @foreach($menu->items as $item)
            <li class="menu-item {{ $item->children->isNotEmpty() ? 'has-children' : '' }} {{ $item->is_current ? 'active' : '' }}">
                <a href="{{ $item->resolved_url }}"
                   target="{{ $item->target }}"
                   class="{{ $item->is_current ? 'active' : '' }}">
                    @if($item->icon)
                        <i class="{{ $item->icon }}"></i>
                    @endif
                    {{ $item->label }}
                    @if($item->children->isNotEmpty())
                        <span class="caret">▾</span>
                    @endif
                </a>

                @if($item->children->isNotEmpty())
                    <ul class="submenu">
                        @foreach($item->children as $child)
                            <li class="{{ $child->is_current ? 'active' : '' }}">
                                <a href="{{ $child->resolved_url }}"
                                   target="{{ $child->target }}">
                                    @if($child->icon)
                                        <i class="{{ $child->icon }}"></i>
                                    @endif
                                    {{ $child->label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</nav>