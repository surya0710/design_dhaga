@extends('frontend.layouts.app')
@section('title', $category->meta_title ?? 'Design Dhaga All Products')

@section('meta_description', $category->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $category->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', $category->meta_title ?? 'Design Dhaga All Products')
@section('og_description', $category->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

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

@section('schema')
@if(isset($faqs) && count($faqs) > 0)
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
<section class="my-2">
    <div class="container-fluid mt-4">
        <div class="row">
            <h1 class="text-center">{{ $category->name ?? 'All Products' }}</h1>
        </div>
        <div class="container-fluid d-none d-md-block">
            <div class="row text-center px-3">    
                {!! $category->content ?? '' !!}
            </div>
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
                @include('frontend.partials.product-price', ['product' => $product])
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
                    <div class="col-lg-10 col-md-8">
                        <div class="text-center mb-2">
                            <h2 class="mb-0">Frequently Asked Questions</h2>
                        </div>
                        <div class="faq-wrapper">
                            @foreach($faqs as $key => $faq)
                            <div class="faq-item {{ $key >= 1 ? 'extra-faq d-none' : '' }}">
                                <h4 class="faq-question mb-1 mt-1 text-blue">Que: {{ $faq->question }}</h4>
                                <div class="faq-answer-wrapper">
                                    <p class="faq-answer mb-0 text-justify" id="faqAnswer{{ $key }}">
                                        <strong>Ans:</strong> {!! $faq->answer !!}
                                    </p>
                                    <p type="button" class="read-more-btn btn fw-bold d-none" data-target="faqAnswer{{ $key }}" data-expanded="0">
                                        <span>...</span>
                                    </p>
                                </div>
                            </div>
                            @endforeach

                            <div class="text-center extra-faq d-none">
                                <h4 class="mb-2">Still have questions? We'd love to hear from you.</h4>
                                <a type="button" class="btn btn-outline-secondary view-all-btn bg-dark" href="{{ route('contact-us') }}">Contact Us</a>
                            </div>

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
        const $span = $(this).find("span");

        if (isExpanded) {
            $answer.removeClass("expanded");
            $(this).data("expanded", 0);
            $span.text("...");
            $span.css({ "font-size": "1.5rem", "letter-spacing": "2px", "padding": "5px 10px 15px" });
        } else {
            $answer.addClass("expanded");
            $(this).data("expanded", 1);
            $span.text("Show less");
            $span.css({ "font-size": "1rem", "letter-spacing": "normal", "padding": "5px 10px" });
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
