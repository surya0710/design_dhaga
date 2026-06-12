@extends('frontend.layouts.app')

@section('title', $category->meta_title ?? $pageContent->meta_title)
@section('meta_description', $category->meta_description ?? $pageContent->meta_description)
@section('meta_keywords', $category->meta_keywords ?? $pageContent->meta_keywords)
@section('og_title', $category->meta_title ?? $pageContent->meta_title)
@section('og_description', $category->meta_description ?? $pageContent->meta_description)
@section('og_image', asset($category->meta_image ?? $pageContent->meta_image ?? 'og-home.jpg'))

@push('extras')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/frontend_assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/frontend_assets/owl.theme.default.min.css">
@endpush

@section('schema')
@if(!empty($faqs) && count($faqs) > 0)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $faq)
        {
            "@type": "Question",
            "name": {!! json_encode(strip_tags($faq->question)) !!},
            "acceptedAnswer": {
                "@type": "Answer",
                "text": {!! json_encode(strip_tags($faq->answer)) !!}
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
@endsection

@section('content')
@php
    $pageTitle    = $category->name ?? 'All Products';
    $hasFaqs      = !empty($faqs) && count($faqs) > 0;
    $productCount = $totalProducts ?? count($products);
    $hasProducts  = count($products) > 0;

    if (!empty($showFilters)) {
        $urlCategory      = '';
        $urlSubcategory   = '';
        $activeFilterSlug = '';

        if (!empty($category)) {
            if (!empty($category->parent_id)) {
                $urlSubcategory   = $category->slug;
                $urlCategory      = optional($category->parent)->slug ?? '';
                $activeFilterSlug = $urlCategory;
            } else {
                $urlCategory      = $category->slug;
                $activeFilterSlug = $category->slug;
            }
        }

        $priceMax    = $priceBounds['max'] ?? 50000;
        $currentSort = request('sort', 'newest');

        $sortOptions = [
            'newest'     => 'Newest',
            'price_low'  => 'Price: Low to High',
            'price_high' => 'Price: High to Low',
            'name_asc'   => 'Name: A–Z',
            'name_desc'  => 'Name: Z–A',
        ];
    }
@endphp

<section class="shop-section">
    @if(!empty($showFilters))

    {{--
        We use a plain .shop-layout flex div instead of Bootstrap .row.
        Bootstrap's .row + .container-fluid can inherit overflow:hidden from
        some parent wrappers in themes, which silently breaks position:sticky.
        This plain flex container has no overflow set, guaranteeing sticky works.
    --}}
    <div class="shop-layout">

        {{-- ── Sticky filter sidebar (desktop) ───────────────────────── --}}
        <aside class="shop-sidebar d-none d-md-block">
            <div class="shop-sidebar-inner" id="shopFiltersDesktop">
                @include('frontend.partials.shop-filters-panel', [
                    'filterPrefix'     => '',
                    'showCloseButton'  => false,
                    'priceMax'         => $priceMax,
                    'categories'       => $categories,
                    'activeFilterSlug' => $activeFilterSlug,
                ])
            </div>
        </aside>

        {{-- ── Main content ────────────────────────────────────────────── --}}
        <div class="shop-main">
            @if(!empty($category->page_heading))
                <h1 class="text-center mb-3 class="home-heading"">{{ $category->page_heading }}</h1>
            @elseif(!empty($pageContent->heading))
                <h1 class="text-center mb-3 class="home-heading"">{{ $pageContent->heading }}</h1>
            @else
                <h1 class="text-center mb-3 class="home-heading"">{{ $pageTitle }}</h1>
            @endif

            <div class="col-8 offset-2">
                <div class="shop-category-content text-center px-3 d-none d-md-block">
                    {!! $category->content ?? $pageContent->content ?? '' !!}
                </div>
            </div>

            {{-- Toolbar: mobile filters button (left) | results + sort (right) --}}
            <div class="shop-toolbar">
                <div class="shop-toolbar-left">
                    {{-- Mobile only --}}
                    <button type="button" class="shop-toolbar-btn d-md-none" id="openShopFilters">
                        <i class="fa-solid fa-sliders"></i> Filters
                    </button>
                </div>

                <div class="shop-toolbar-right">
                    <span class="shop-toolbar-results">
                        <span id="shopResultsCount">{{ $productCount }}</span> Results
                        <span class="shop-toolbar-results-divider">|</span>
                    </span>
                    <div class="shop-toolbar-dropdown-wrap">
                        <button type="button" class="shop-toolbar-btn" id="openShopSort">
                            Sort <i class="fa-solid fa-chevron-down shop-sort-chevron"></i>
                        </button>
                        <div class="shop-sort-menu" id="shopSortMenu">
                            @foreach($sortOptions as $value => $label)
                            <button type="button"
                                class="shop-sort-option {{ $currentSort === $value ? 'active' : '' }}"
                                data-sort="{{ $value }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products grid --}}
            <div class="products-conatiner shop-layout-grid"
                id="shopProductsGrid"
                data-load-url="{{ route('shop.load') }}"
                data-page="1"
                data-has-more="{{ !empty($hasMoreProducts) ? '1' : '0' }}"
                data-url-category="{{ $urlCategory }}"
                data-url-subcategory="{{ $urlSubcategory }}"
                data-sort="{{ $currentSort }}"
                data-min-price=""
                data-max-price=""
                data-price-min-bound="0"
                data-price-max-bound="{{ $priceMax }}">

                @if($hasProducts)
                    @include('frontend.partials.shop-products-grid', ['products' => $products])
                @else
                    <p class="shop-no-products">There are no products to display.</p>
                @endif
            </div>

            {{-- Infinite scroll sentinel --}}
            <div id="shopLoadSentinel" class="shop-load-sentinel {{ empty($hasMoreProducts) ? 'd-none' : '' }}">
                <div class="shop-loading-spinner"></div>
            </div>

            {{-- FAQs --}}
            @if($hasFaqs && Auth::check())
                @include('frontend.partials.faq-section', ['faqs' => $faqs, 'containerClass' => ''])
            @endif

        </div>{{-- /.shop-main --}}
    </div>{{-- /.shop-layout --}}

    {{-- Mobile filter drawer + overlay --}}
    <aside class="shop-filters-drawer d-md-none" id="shopFiltersSidebar">
        @include('frontend.partials.shop-filters-panel', [
            'filterPrefix'     => 'Mobile',
            'showCloseButton'  => true,
            'priceMax'         => $priceMax,
            'categories'       => $categories,
            'activeFilterSlug' => $activeFilterSlug,
        ])
    </aside>
    <div class="shop-filter-overlay" id="shopFilterOverlay"></div>

    @else
    {{-- ── Simple layout (no filters) ─────────────────────────────── --}}
    <div class="shop-simple-wrap">
        <h1 class="shop-page-title text-center mb-4">{{ $pageTitle }}</h1>
        <div class="products-conatiner">
            @if($hasProducts)
                @include('frontend.partials.shop-products-grid', ['products' => $products])
            @else
                <p class="shop-no-products text-center">There are no products to display.</p>
            @endif
        </div>
        @if($hasFaqs && Auth::check())
            @include('frontend.partials.faq-section', ['faqs' => $faqs, 'containerClass' => 'container'])
        @endif
    </div>
    @endif

</section>

<script>
const textData = [];
@foreach($highlights as $highlight)
textData.push(
    `<span>{{ $highlight->title }}</span>` +
    `<img src="{{ Storage::url($highlight->emoji) }}" class="emoji" alt="{{ $highlight->alt_text ?? $highlight->title }}">`
);
@endforeach
</script>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ─── Wishlist ─────────────────────────────────────────────────── */
const wishlistConfig = {
    addUrl:    "{{ route('wishlist.add') }}",
    removeUrl: "{{ route('wishlist.remove') }}",
    csrfToken: "{{ csrf_token() }}"
};
const isWishlistPage = {{ (isset($category) && ($category->slug ?? '') === 'wishlist') ? 'true' : 'false' }};

function showWishlistAuthPopup() {
    Swal.fire({
        icon: 'warning',
        title: 'Please Login',
        text: 'You need to be logged in to manage your wishlist.',
        confirmButtonText: 'Login',
        confirmButtonColor: '#8b1e2d',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
    }).then(r => { if (r.isConfirmed) $("#loginModal").modal('toggle'); });
}

function setWishlistButtonState($btn, inWishlist) {
    $btn.toggleClass('active', inWishlist)
        .attr('data-in-wishlist', inWishlist ? '1' : '0');
    $btn.find('i')
        .removeClass('fa-regular')
        .addClass('fa-solid');
}

function toggleWishlist($btn) {
    const productId  = $btn.attr('data-product-id');
    const inWishlist = String($btn.attr('data-in-wishlist')) === '1';
    const url        = inWishlist ? wishlistConfig.removeUrl : wishlistConfig.addUrl;
    $btn.prop('disabled', true);
    $.ajax({
        url, method: 'POST',
        data: { _token: wishlistConfig.csrfToken, product_id: productId },
        success(res) {
            setWishlistButtonState($btn, res.in_wishlist);
            Swal.fire({
                iconHtml: '<i class="fa-regular fa-circle-check fa-2x"></i>',
                title: res.in_wishlist ? 'Added to Wishlist' : 'Removed from Wishlist',
                text: res.message,
                confirmButtonColor: '#8b1e2d',
                timer: 1800,
                showConfirmButton: false,
            });

            // On the wishlist page, drop the card from the grid once it's removed.
            if (isWishlistPage && !res.in_wishlist) {
                const $card = $btn.closest('.product-item');
                $card.fadeOut(250, function () {
                    $(this).remove();
                    const $container = $('.products-conatiner');
                    if ($container.find('.product-item').length === 0) {
                        $container.html('<p class="shop-no-products text-center">There are no products to display.</p>');
                    }
                });
            }
        },
        error(xhr) {
            if (xhr.status === 401) { showWishlistAuthPopup(); return; }
            Swal.fire({
                icon: 'error', title: 'Oops!',
                text: xhr.responseJSON?.message ?? 'Something went wrong.',
                confirmButtonColor: '#8b1e2d',
            }).then(() => location.reload());
        },
        complete() { $btn.prop('disabled', false); },
    });
}

$(document).on('click', '.wishlist-btn', function () { toggleWishlist($(this)); });

/* ─── FAQ ──────────────────────────────────────────────────────── */
$(document).on('click', '.read-more-btn', function () {
    const $answer  = $('#' + $(this).data('target'));
    const expanded = $(this).data('expanded') === 1;
    const $span    = $(this).find('span');
    $answer.toggleClass('expanded', !expanded);
    $(this).data('expanded', expanded ? 0 : 1).toggleClass('align-content-center', !expanded);
    $span.text(expanded ? '•••' : 'Show less').toggleClass('show-less-text', !expanded);
});

function initFaqButtons() {
    $('.faq-answer').each(function () {
        if (this.scrollHeight > this.clientHeight + 2)
            $(this).siblings('.read-more-btn').removeClass('d-none');
    });
}

$('#showMoreFaqBtn').click(function () {
    $('.extra-faq').removeClass('d-none');
    $(this).hide();
    initFaqButtons();
});

initFaqButtons();

/* ─── Shop: filters + infinite scroll ─────────────────────────── */
@if(!empty($showFilters))
(function () {
    const grid      = document.getElementById('shopProductsGrid');
    const sentinel  = document.getElementById('shopLoadSentinel');
    const sidebar   = document.getElementById('shopFiltersSidebar');
    const overlay   = document.getElementById('shopFilterOverlay');
    const sortMenu  = document.getElementById('shopSortMenu');
    const resultsEl = document.getElementById('shopResultsCount');
    const chevron   = document.querySelector('.shop-sort-chevron');
    let   isLoading = false;

    function filterEls(p) {
        return {
            minInput: document.getElementById('shopMinPrice'      + p),
            maxInput: document.getElementById('shopMaxPrice'      + p),
            maxRange: document.getElementById('shopPriceRangeMax' + p),
        };
    }

    function syncPrice(p, source = 'slider') {
        const { minInput, maxInput, maxRange } = filterEls(p);
        if (!minInput || !maxInput || !maxRange) return;
        const bMax = parseInt(grid.dataset.priceMaxBound, 10);
        let min = Math.max(0, parseInt(minInput.value, 10) || 0);
        let max = source === 'max-input'
            ? Math.min(bMax, parseInt(maxInput.value, 10) || bMax)
            : Math.min(bMax, parseInt(maxRange.value, 10) || bMax);
        if (min > max) max = min;
        minInput.value = min;
        maxRange.value = max;
        maxInput.value = max;
    }

    function keepDigitsOnly(el) {
        if (!el) return;
        el.addEventListener('input', function () {
            this.value = this.value.replace(/[^\d]/g, '');
        });
        el.addEventListener('keydown', function (e) {
            if (['e', 'E', '+', '-', '.', ','].includes(e.key)) e.preventDefault();
        });
        el.addEventListener('paste', function (e) {
            const txt = (e.clipboardData || window.clipboardData).getData('text');
            if (/[^\d]/.test(txt)) e.preventDefault();
        });
    }

    function buildParams(page) {
        const p = {
            page:            page,
            url_category:    grid.dataset.urlCategory    || '',
            url_subcategory: grid.dataset.urlSubcategory || '',
            sort:            grid.dataset.sort           || 'newest',
        };
        if (grid.dataset.minPrice) p.min_price = grid.dataset.minPrice;
        if (grid.dataset.maxPrice) p.max_price = grid.dataset.maxPrice;
        return p;
    }

    function loadProducts(page, replace) {
        if (isLoading) return;
        isLoading = true;
        sentinel.classList.remove('d-none');
        fetch(grid.dataset.loadUrl + '?' + new URLSearchParams(buildParams(page)), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            if (replace) {
                grid.innerHTML = data.html || '<p class="shop-no-products">There are no products to display.</p>';
            } else if (data.html) {
                grid.insertAdjacentHTML('beforeend', data.html);
            }
            grid.dataset.page    = data.page;
            grid.dataset.hasMore = data.has_more ? '1' : '0';
            sentinel.classList.toggle('d-none', !data.has_more);
            if (resultsEl && data.total !== undefined) resultsEl.textContent = data.total;
        })
        .catch(() => sentinel.classList.add('d-none'))
        .finally(() => { isLoading = false; });
    }

    function resetAndLoad() { grid.dataset.page = '0'; loadProducts(1, true); }

    function applyPrice(p) {
        syncPrice(p);
        const { minInput, maxInput } = filterEls(p);
        const bMax = parseInt(grid.dataset.priceMaxBound, 10);
        const min  = parseInt(minInput.value, 10) || 0;
        const max  = parseInt(maxInput.value, 10);
        const def  = min <= 0 && max >= bMax;
        grid.dataset.minPrice = def ? '' : min;
        grid.dataset.maxPrice = def ? '' : max;
        resetAndLoad();
        closeMobile();
    }

    ['', 'Mobile'].forEach(p => {
        const { minInput, maxInput, maxRange } = filterEls(p);
        keepDigitsOnly(minInput);
        keepDigitsOnly(maxInput);
        minInput?.addEventListener('input',  () => syncPrice(p, 'min-input'));
        minInput?.addEventListener('change', () => syncPrice(p, 'min-input'));
        maxInput?.addEventListener('change', () => syncPrice(p, 'max-input'));
        maxInput?.addEventListener('blur',   () => syncPrice(p, 'max-input'));
        maxRange?.addEventListener('input',  () => syncPrice(p, 'slider'));
    });

    document.querySelectorAll('.apply-price-filter').forEach(btn =>
        btn.addEventListener('click', function () { applyPrice(this.dataset.prefix || ''); })
    );

    document.querySelectorAll('[data-filter-toggle]').forEach(btn =>
        btn.addEventListener('click', function () {
            const body = document.getElementById('priceFilterBody' + (this.dataset.prefix || ''));
            const icon = this.querySelector('.shop-filter-icon');
            icon.textContent = body.classList.toggle('open') ? '−' : '+';
        })
    );

    function openMobile() {
        const d = filterEls(''), m = filterEls('Mobile');
        if (d.minInput && m.minInput) { m.minInput.value = d.minInput.value; m.maxRange.value = d.maxRange.value; syncPrice('Mobile'); }
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        closeSortMenu();
    }

    function closeMobile() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    function closeSortMenu() {
        sortMenu?.classList.remove('show');
        chevron?.classList.remove('open');
    }

    document.getElementById('openShopFilters')?.addEventListener('click', openMobile);
    document.querySelector('.shop-filter-close')?.addEventListener('click', closeMobile);
    overlay?.addEventListener('click', () => { closeMobile(); closeSortMenu(); });

    document.getElementById('openShopSort')?.addEventListener('click', e => {
        e.stopPropagation();
        const open = sortMenu?.classList.toggle('show');
        chevron?.classList.toggle('open', open);
    });

    document.querySelectorAll('.shop-sort-option').forEach(btn =>
        btn.addEventListener('click', function () {
            grid.dataset.sort = this.dataset.sort;
            document.querySelectorAll('.shop-sort-option').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            closeSortMenu();
            resetAndLoad();
        })
    );

    document.addEventListener('click', e => {
        if (!e.target.closest('.shop-toolbar-dropdown-wrap')) closeSortMenu();
    });

    document.querySelectorAll('.shop-view-btn').forEach(btn =>
        btn.addEventListener('click', function () {
            document.querySelectorAll('.shop-view-btn').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            grid.classList.remove('shop-layout-grid', 'shop-layout-list');
            grid.classList.add(this.dataset.view === 'list' ? 'shop-layout-list' : 'shop-layout-grid');
        })
    );

    if ('IntersectionObserver' in window) {
        new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && grid.dataset.hasMore === '1' && !isLoading)
                    loadProducts(parseInt(grid.dataset.page, 10) + 1, false);
            });
        }, { rootMargin: '200px' }).observe(sentinel);
    }
})();
@endif
</script>
@endpush