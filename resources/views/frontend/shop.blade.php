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
<section class="my-2">
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

            $productUrl = getProductUrl($product);
            @endphp
            <a class="product-item" href="{{ $productUrl }}">

                <div class="position-relative d-inline-block w-100">

                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-100 rounded">

                    <button type="button" class="btn p-0 border-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow wishlist-btn {{ $isInWishlist ? 'active bg-dark-grey' : 'bg-white' }}"
                        style="width: 30px; height: 30px; z-index: 2;" data-product-id="{{ $product->id }}" data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}"
                        aria-label="Toggle wishlist" onclick="event.preventDefault(); event.stopPropagation();">
                        <i class="{{ $isInWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                    </button>

                </div>

                <p class="mt-0 text-left">{{ $product->name }}</p>
                @if ($product->sale_price)
                <span class="text-black text-bold">₹{{ number_format($product->sale_price, 0) }}</span>
                <span class="text-decoration-line-through text-muted small text-light">
                    ₹ {{ number_format($product->regular_price, 0) }}
                </span>
                <span class="text-maroon small">
                    Save {{ number_format((1 - ($product->sale_price / $product->regular_price)) * 100, 0) }}%
                </span>
                @else
                <span class="text-black">₹ {{ number_format($product->regular_price, 0) }}</span>
                @endif
            </a>
            @endforeach
            @else
            <p>There are no products to display.</p>
            @endif
        </div>

        {{-- FAQ SECTION --}}
        @if(isset($faqs) && count($faqs) > 0 && Auth::check())
        <section class="faq-section py-4">
            <div class="container">
                <div class="row justify-content-center">    
                    <div class="col-lg-8 col-md-9">
                        <div class="text-center mb-2">
                            <h4 class="mb-0">Frequently Asked Questions</h4>
                        </div>
                        <div class="faq-wrapper">
                            @foreach($faqs as $key => $faq)
                            <div class="faq-item {{ $key >= 1 ? 'extra-faq d-none' : '' }}">
                                <h4 class="faq-question mb-1 text-blue">Que: {{ $faq->question }}</h4>
                                <div class="faq-answer-wrapper">
                                    <p class="faq-answer mb-0 text-justify" id="faqAnswer{{ $key }}">
                                        <strong>Ans:</strong> {!! $faq->answer !!}
                                    </p>
                                    <button type="button" class="read-more-btn btn p-0 fw-bold d-none" data-target="faqAnswer{{ $key }}" data-expanded="0">
                                        ....
                                    </button>
                                </div>
                            </div>
                            @endforeach

                            @if(count($faqs) > 1)
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-outline-secondary see-more text-dark text-decoration-none fw-bold" id="showMoreFaqBtn">
                                    See more...
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
    </div>
</section>
<script>
    const textData = [];
    @foreach($highlights as $highlight)
    textData.push(`<span>{{ $highlight->title }}</span>
             <img src="{{ Storage::url($highlight->emoji) }}"
                  class="emoji"
                  alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
    @endforeach
</script>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const wishlistConfig = {
        addUrl: "{{ route('wishlist.add') }}",
        removeUrl: "{{ route('wishlist.remove') }}",
        loginUrl: "",
        csrfToken: "{{ csrf_token() }}"
    };

    function showWishlistAuthPopup() {
        Swal.fire({
            icon: 'warning',
            title: 'Please Login',
            text: 'You need to be logged in to manage your wishlist.',
            confirmButtonText: 'Login',
            confirmButtonColor: '#8b1e2d',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $("#loginModal").modal('toggle');
            }
        });
    }

    function setWishlistButtonState($button, inWishlist) {
        $button.toggleClass('active', inWishlist);
        $button.attr('data-in-wishlist', inWishlist ? '1' : '0');

        // Toggle background classes
        $button.toggleClass('bg-dark-grey', inWishlist);
        $button.toggleClass('bg-white', !inWishlist);

        const icon = $button.find('i');
        icon.removeClass('fa-regular fa-solid');
        icon.addClass(inWishlist ? 'fa-solid' : 'fa-regular');
    }

    function toggleWishlist($button) {
        const productId = $button.attr('data-product-id');
        const inWishlist = String($button.attr('data-in-wishlist')) === '1';
        const url = inWishlist ? wishlistConfig.removeUrl : wishlistConfig.addUrl;

        $button.prop('disabled', true);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: wishlistConfig.csrfToken,
                product_id: productId
            },
            success: function(response) {
                setWishlistButtonState($button, response.in_wishlist);

                Swal.fire({
                    iconHtml: '<i class="fa-regular fa-circle-check fa-2x"></i>',
                    title: response.in_wishlist ? 'Added to Wishlist' : 'Removed from Wishlist',
                    text: response.message,
                    confirmButtonColor: '#8b1e2d',
                    timer: 1800,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    showWishlistAuthPopup();
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: xhr.responseJSON?.message ?? 'Something went wrong. Please try again.',
                    confirmButtonColor: '#8b1e2d',
                }).then((result) => {
                    if (result.isConfirmed || result.isDismissed) {
                        location.reload();
                    }
                });
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    }

    $(".wishlist-btn").click(function() {
        toggleWishlist($(this));
    });

    // Show/hide individual answer
    $(document).on("click", ".read-more-btn", function () {
        const targetId = $(this).data("target");
        const $answer = $("#" + targetId);
        const isExpanded = $(this).data("expanded") === 1;

        if (isExpanded) {
            $answer.removeClass("expanded");
            $(this).text("....").data("expanded", 0);
        } else {
            $answer.addClass("expanded");
            $(this).text("Show less").data("expanded", 1);
        }
    });

    // Check which answers actually overflow and show their button
    function initFaqButtons() {
        $(".faq-answer").each(function () {
            const el = this;
            // scrollHeight > clientHeight means text is clipped
            if (el.scrollHeight > el.clientHeight + 2) {
                $(this).siblings(".read-more-btn").removeClass("d-none");
            }
        });
    }

    // Show more FAQ items
    $("#showMoreFaqBtn").click(function () {
        $(".extra-faq").removeClass("d-none");
        $(this).hide();
        // Re-check overflow for newly revealed items
        initFaqButtons();
    });

    // Run on load
    initFaqButtons();
</script>
@endpush