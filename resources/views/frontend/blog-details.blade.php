@extends('frontend.layouts.app')
@section('title', $blog->meta_title)

@section('meta_description', $blog->meta_description)

@section('meta_keywords', $blog->meta_keywords)

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('uploads/blogs/'.$blog->image))

@push('extras')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
@endpush

@section('content')

<div class="container">
    <div class="row gx-lg-5 px-xs-2">
        <div class="col-lg-12">
            <div class="blog-content text-justify">
                <div class="mb-4 blog-content text-center">
                    <h1 class="fw-bold">{{ $blog->title }}</h1>
                    @if(!empty($blog->author))
                    <p class="fw-bold mb-1">by {{ $blog->author }}</p>
                    @endif
                    <p class="mb-1">{{ date('j-F-Y', strtotime($blog->created_at)) }}</p>
                </div>

                <div class="rounded overflow-hidden mb-4 text-center">
                    <img src="{{ asset('uploads/blogs/'.$blog->image) }}" class="img-fluid w-100" alt="{{ $blog->title }}" />
                </div>
                {!! $blog->content !!}
            </div>
        </div>

        <div class="col-lg-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h2 class="h4 fw-bold mb-0">Recent Blogs</h2>
            </div>

            <div id="recentBlogsCarousel">
                <div class="owl-carousel">
                    @foreach($recentBlogs as $blog)
                    <div>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="text-decoration-none text-dark">
                            <div class="card border-0 h-100">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ asset('uploads/blogs/'.$blog->image) }}" class="card-img-top object-fit-cover" alt="{{ $blog->title }}" />
                                </div>
                                <div class="card-body blogs-card">
                                    <h5 class="card-title mt-2">{{ $blog->title }}</h5>
                                    <p class="text-muted small">
                                        {!! Str::limit(strip_tags($blog->content), 200) !!}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <hr />

        <div class="col-lg-12 mb-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h2 class="h4 fw-bold mb-0">Featured Products</h2>
            </div>

            <div id="featuredProductsCarousel">
                <div class="owl-carousel">
                    @foreach($featuredProducts as $product)
                    @php
                        $productUrl = getProductUrl($product);
                        $isInWishlist = auth()->check()
                            ? \App\Models\Wishlist::where('user_id', auth()->id())
                                ->where('product_id', $product->id)
                                ->exists()
                            : false;
                    @endphp
                    <div>
                        <a href="{{ $productUrl }}" class="text-decoration-none text-dark">
                            <div class="card border-0 h-100">
                                <div class="position-relative">
                                    <div class="ratio ratio-4x3">
                                        <img src="{{ Storage::url($product->image) }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}" />
                                    </div>
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
                                <div class="card-body blogs-card">
                                    <p class="mt-0 text-left mb-0 featured-product-name">{{ $product->name }}</p>
                                    @include('frontend.partials.product-price', ['product' => $product])
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Overlay -->
<div id="shareOverlay" class="share-overlay"></div>

<!-- Floating Button -->
<div id="shareToggleBtn" class="share-toggle-btn">
    <i class="fa-solid fa-share-nodes"></i>
</div>

<!-- Share Panel -->
<div id="sharePanel" class="share-panel">
    <div class="share-header">
        <h5>Share this article</h5>
        <button id="closeShare"><i class="fa-solid fa-xmark"></i></button>
    </div>

    @php
        $shareUrl = urlencode(request()->fullUrl());
        $shareTitle = urlencode($blog->title);
    @endphp

    <div class="share-grid">
        <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" class="share-card whatsapp">
            <i class="fa-brands fa-whatsapp"></i>
            <span>WhatsApp</span>
        </a>

        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="share-card facebook">
            <i class="fa-brands fa-facebook"></i>
            <span>Facebook</span>
        </a>

        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" class="share-card twitter">
            <i class="fa-brands fa-twitter"></i>
            <span>Twitter</span>
        </a>

        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" class="share-card linkedin">
            <i class="fa-brands fa-linkedin"></i>
            <span>LinkedIn</span>
        </a>

        <button class="share-card copy" onclick="copyLink()">
            <i class="fa-solid fa-link"></i>
            <span>Copy Link</span>
        </button>
    </div>
</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<style>
    #recentBlogsCarousel .owl-nav button,
    #featuredProductsCarousel .owl-nav button {
        display: flex !important;
        align-items: center;
        justify-content: center;
        top: 42%;
        z-index: 5;
    }

    #recentBlogsCarousel .owl-nav,
    #featuredProductsCarousel .owl-nav,
    #recentBlogsCarousel .owl-nav.disabled,
    #featuredProductsCarousel .owl-nav.disabled {
        display: block !important;
    }

    #recentBlogsCarousel .owl-nav .owl-prev,
    #featuredProductsCarousel .owl-nav .owl-prev {
        left: 8px;
    }

    #recentBlogsCarousel .owl-nav .owl-next,
    #featuredProductsCarousel .owl-nav .owl-next {
        right: 8px;
    }
</style>
<script>
    $(document).ready(function() {
        const $recentBlogsCarousel = $("#recentBlogsCarousel .owl-carousel");
        const recentBlogCount = $recentBlogsCarousel.children().length;

        // Keep 3 cards visible on desktop, but ensure navigation can still slide
        // when the recent blog count is too low (e.g., exactly 3).
        if (recentBlogCount > 0 && recentBlogCount <= 3) {
            $recentBlogsCarousel.children().clone(true).appendTo($recentBlogsCarousel);
        }

        $recentBlogsCarousel.owlCarousel({
            loop: true,
            margin: 12,
            nav: true,
            navText: [
                '<i class="fa-solid fa-chevron-left"></i>',
                '<i class="fa-solid fa-chevron-right"></i>'
            ],
            dots: false,
            autoplay: false,
            smartSpeed: 800,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 3
                }
            }
        });

        $("#featuredProductsCarousel .owl-carousel").owlCarousel({
            loop: true,
            margin: 12,
            nav: true,
            navText: [
                '<i class="fa-solid fa-chevron-left"></i>',
                '<i class="fa-solid fa-chevron-right"></i>'
            ],
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            smartSpeed: 2000,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 3
                }
            }
        });
    });
</script>
<script>
    const shareBtn = document.getElementById('shareToggleBtn');
    const sharePanel = document.getElementById('sharePanel');
    const shareoverlay = document.getElementById('shareOverlay');
    const closeBtn = document.getElementById('closeShare');

    function openShare() {
        sharePanel.classList.add('active');
        shareoverlay.classList.add('active');
    }

    function closeSharePanel() {
        sharePanel.classList.remove('active');
        shareoverlay.classList.remove('active');
    }

    shareBtn.addEventListener('click', openShare);
    closeBtn.addEventListener('click', closeSharePanel);
    shareoverlay.addEventListener('click', closeSharePanel);

    function copyLink() {
        navigator.clipboard.writeText(window.location.href);
        alert("🔗 Link copied!");
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ─── Wishlist ─────────────────────────────────────────────────── */
const wishlistConfig = {
    addUrl:    "{{ route('wishlist.add') }}",
    removeUrl: "{{ route('wishlist.remove') }}",
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
    }).then(r => { if (r.isConfirmed) $("#loginModal").modal('toggle'); });
}

function setWishlistButtonState($btn, inWishlist) {
    $btn.toggleClass('active', inWishlist)
        .attr('data-in-wishlist', inWishlist ? '1' : '0');
    $btn.find('i').removeClass('fa-regular').addClass('fa-solid');
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
        },
        error(xhr) {
            if (xhr.status === 401) { showWishlistAuthPopup(); return; }
            Swal.fire({
                icon: 'error', title: 'Oops!',
                text: xhr.responseJSON?.message ?? 'Something went wrong.',
                confirmButtonColor: '#8b1e2d',
            });
        },
        complete() { $btn.prop('disabled', false); },
    });
}

$(document).on('click', '.wishlist-btn', function () { toggleWishlist($(this)); });
</script>
@endpush
