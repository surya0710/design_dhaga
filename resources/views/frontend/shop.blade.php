@extends('frontend.layouts.app')
@section('title', $category->meta_title)

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

    @push('extras')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/frontend_assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/frontend_assets/owl.theme.default.min.css">

    <style>
        .wishlist-btn.active i {
            font-weight: 900;
        }
    </style>
    @endpush

    @section('content')
        <section class="m-2">
            <div class="container-fluid mt-4">
                <div class="row">
                    <h3 class="text-center">{{ $category->name }}</h3>
                </div>
                @php
                    use App\Models\Wishlist;
                @endphp

                <div class="products-conatiner mt-2">
                    @if(count($products) > 0)
                        @foreach($products as $product)
                            @php
                                $isInWishlist = auth()->check()
                                    ? Wishlist::where('user_id', auth()->id())
                                        ->where('product_id', $product->id)
                                        ->exists()
                                    : false;
                            @endphp

                            <a class="product-item" href="{{ route('shop.product' , [$category->slug, $product->category->slug, $product->slug]) }}">
                                
                                <div class="position-relative d-inline-block w-100">

                                    <img 
                                        src="{{ asset('storage/' . $product->image) }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-100 rounded"
                                    >

                                    <button 
                                        type="button"
                                        class="btn p-0 bg-white border-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow wishlist-btn {{ $isInWishlist ? 'active' : '' }}"
                                        style="width: 30px; height: 30px; z-index: 2;"
                                        data-product-id="{{ $product->id }}"
                                        data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}"
                                        aria-label="Toggle wishlist"
                                        onclick="event.preventDefault(); event.stopPropagation();"
                                    >
                                        <i class="{{ $isInWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                    </button>

                                </div>

                                <p class="mt-2">{{ $product->name }}</p>
                            </a>
                        @endforeach
                    @else
                        <p>There are no products to display.</p>
                    @endif
                </div>
            </div>
        </section>
    @endsection
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        const wishlistConfig = {
            addUrl: "{{ route('wishlist.add') }}",
            removeUrl: "{{ route('wishlist.remove') }}",
            loginUrl: "{{ route('login') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('frontend_assets/js/product.js') }}"></script>
    @endpush