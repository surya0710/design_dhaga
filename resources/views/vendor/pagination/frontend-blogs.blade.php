@if ($paginator->hasPages())
    <nav class="blog-pagination" aria-label="Blog pagination">
        <ul class="blog-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="blog-pagination-btn is-disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </span>
                </li>
            @else
                <li>
                    <a class="blog-pagination-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="blog-pagination-btn is-ellipsis" aria-disabled="true">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="blog-pagination-btn is-active" aria-current="page">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a class="blog-pagination-btn" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="blog-pagination-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </a>
                </li>
            @else
                <li>
                    <span class="blog-pagination-btn is-disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
