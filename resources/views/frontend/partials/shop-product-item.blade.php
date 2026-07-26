@php
    $isInWishlist = auth()->check()
        ? \App\Models\Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->exists()
        : false;

    $productUrl = getProductUrl($product);
    $productImage = responsiveImage($product->image, [240, 360, 480, 640], 'storage');
@endphp
<a class="product-item" href="{{ $productUrl }}">
    <div class="position-relative d-inline-block w-100">
        <img
            src="{{ $productImage['src'] }}"
            @if(!empty($productImage['srcset'])) srcset="{{ $productImage['srcset'] }}" sizes="(max-width: 768px) 50vw, 220px" @endif
            alt="{{ $product->name }}"
            class="w-100 rounded"
            loading="lazy"
            decoding="async"
        >

        <button type="button"
            class="btn p-0 border-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow wishlist-btn {{ $isInWishlist ? 'active' : '' }}"
            style="width: 30px; height: 30px; z-index: 2;"
            data-product-id="{{ $product->id }}"
            data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}"
            aria-label="Toggle wishlist"
            onclick="event.preventDefault();">
            <i class="fa-solid fa-heart"></i>
        </button>
    </div>

    <p class="mt-0 text-left">{{ $product->name }}</p>
    @include('frontend.partials.product-price', ['product' => $product])
</a>
