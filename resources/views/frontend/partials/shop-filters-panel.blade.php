@php
    $filterPrefix = $filterPrefix ?? '';
    $showCloseButton = $showCloseButton ?? false;
@endphp
<div class="shop-filters-panel">
    @if($showCloseButton)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="shop-filters-title mb-0">Filters</h2>
            <button type="button" class="btn-close shop-filter-close" aria-label="Close filters"></button>
        </div>
    @else
        <h2 class="shop-filters-title">Filters</h2>
    @endif

    <div class="shop-filter-group">
        <button type="button" class="shop-filter-heading" data-filter-toggle="price" data-prefix="{{ $filterPrefix }}">
            <span>Price</span>
            <span class="shop-filter-icon">+</span>
        </button>
        <div class="shop-filter-body" id="priceFilterBody{{ $filterPrefix }}">
            <div class="shop-price-card">
                <div class="shop-price-inputs mb-2">
                    <div class="shop-price-field">
                        <label for="shopMinPrice{{ $filterPrefix }}">Min (₹)</label>
                        <input type="number" id="shopMinPrice{{ $filterPrefix }}" class="shop-min-price" data-prefix="{{ $filterPrefix }}" min="0" max="{{ $priceMax }}" value="0" step="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                    </div>
                    <span class="shop-price-sep">to</span>
                    <div class="shop-price-field">
                        <label for="shopMaxPrice{{ $filterPrefix }}">Max (₹)</label>
                        <input type="number" id="shopMaxPrice{{ $filterPrefix }}" class="shop-max-price" data-prefix="{{ $filterPrefix }}" min="0" max="{{ $priceMax }}" value="{{ $priceMax }}" step="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                    </div>
                </div>
                <input type="range" id="shopPriceRangeMax{{ $filterPrefix }}" class="shop-price-range shop-max-range" data-prefix="{{ $filterPrefix }}" min="0" max="{{ $priceMax }}" value="{{ $priceMax }}" step="1" aria-label="Maximum price">
            </div>
            <button type="button" class="btn btn-sm btn-dark mt-3 w-100 apply-price-filter" data-prefix="{{ $filterPrefix }}">Apply</button>
        </div>
    </div>

    @foreach($categories as $filterCategory)
        <a href="{{ route('shop.index', $filterCategory->slug) }}"
            class="shop-filter-category {{ $activeFilterSlug === $filterCategory->slug ? 'active' : '' }}">
            {{ $filterCategory->name }}
        </a>
    @endforeach
</div>
