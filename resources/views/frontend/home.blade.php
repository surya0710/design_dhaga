@extends('frontend.layouts.app')
@section('title', $pageContent->meta_title ?? '')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', $pageContent->meta_title ?? '')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

@php
    $desktopInfo    = $homeSections->get('desktop_info');
    $mobileFeatures = $homeSections->get('mobile_features');
    $ideaBrush      = $homeSections->get('idea_brush');
    $graphicsDesign = $homeSections->get('graphics_design');
    $whoWeAre       = $homeSections->get('who_we_are');
    $inspiredArt    = $homeSections->get('inspired_art');

    $bodyParts = function ($body) {
        $parts = preg_split("/\r\n|\n|\r/", (string) $body);
        $intro = trim(array_shift($parts) ?? '');
        $items = collect($parts)->map(fn ($line) => trim($line))->filter()->values();

        return [$intro, $items];
    };
@endphp

@section('schema')
<script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "Corporation",
    "name": "Design Dhaga",
    "alternateName": "Design Dhaga",
    "url": "https://www.designdhaga.com/",
    "logo": "https://www.designdhaga.com/frontend_assets/images/logo/logo.svg",
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "9671941303",
        "contactType": "customer service",
        "contactOption": "TollFree",
        "areaServed": ["US","GB","CA","IN","AU","VN","NG","NP"],
        "availableLanguage": ["en","Hindi"]
    },
    "sameAs": [
        "https://www.facebook.com/design.dhaga",
        "https://www.instagram.com/design.dhaga",
        "https://www.youtube.com/@designdhaga",
        "https://in.pinterest.com/design_dhaga"
    ]
    }
</script>
@endsection

@section('content')
<div class="container py-2 category-icons">
    <div class="d-flex justify-content-center gap-3">
        @foreach($categories as $category)
            @if($category->show_on_home == 1)
            <div class="text-center">
                <a href="{{ route('shop.index', [$category->slug]) }}" class="text-decoration-none">
                    <img src="{{ asset('uploads/categories/'.$category->image) }}" alt="{{ $category->name }}') }}" class="img-fluid" loading="lazy">
                    <h4>{{ $category->name }}</h4>
                </a>
            </div>
            @endif
        @endforeach
    </div>
</div>
@if(!empty($pageContent?->heading))
<div class="container">
    <div class="row text-center">
        <div class="col-12">
            <h1 class="home-heading">{{ $pageContent->heading }}</h1>
        </div>
    </div>
</div>
@endif
<!-- ================= Banner Slider ================= -->
<div id="homeSlider" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($sliders as $index => $slider)
        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
            <a href="{{ $slider->button_link }}" target="{{  $slider->target }}">
                <img src="{{ Storage::url($slider->image) }}" class="d-block w-100" alt=" {{ $slider->image_alt }}">
                <div class="carousel-caption caption-{{ $slider->text_location }} text-{{ $slider->text_color }} d-none">
                    <h2>{!! $slider->heading !!}</h2>
                    <p>{!! $slider->description !!}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#homeSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
@if($desktopInfo && $desktopInfo->items->isNotEmpty())
<section class="container d-none d-md-block" id="our-info">
    <div class="row">
        @foreach($desktopInfo->items as $item)
            <div class="col-md-4 {{ $loop->first ? 'info-box px-5' : ($loop->last ? 'text-right info-box px-5' : 'text-center info-box') }}">
                <h1 class="my-0">{{ $item->title }}</h1>
                <p>{{ $item->subtitle }}</p>
            </div>
        @endforeach
    </div>
</section>
@endif
@if($mobileFeatures && $mobileFeatures->items->isNotEmpty())
<section class="features-box d-sm-block d-md-none">
    <div class="container">
        <div class="row feature-items">
            @foreach($mobileFeatures->items as $item)
                <div class="feature-item col p-0">
                    <img src="{{ asset($item->image) }}" class="mobile-icons" loading="lazy" />
                    <h4>{{ $item->title }}</h4>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="bg-body-primary py-4">
    <div class="container">

        <!-- Centered Tabs -->
        <div class="d-flex justify-content-center mb-3">
            <ul class="nav nav-tabs custom-tabs border-0 gap-2" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="new-arrival-tab" data-bs-toggle="tab" data-bs-target="#new-arrival-tab-pane" type="button" role="tab"
                    aria-controls="new-arrival-tab-pane" aria-selected="true">New Arrival</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="best-seller-tab" data-bs-toggle="tab" data-bs-target="#best-seller-tab-pane" type="button" role="tab"
                    aria-controls="best-seller-tab-pane" aria-selected="false">Best Seller</button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            @php
                use App\Models\Wishlist;
            @endphp
            <!-- New Arrival -->
            <div class="tab-pane fade show active" id="new-arrival-tab-pane" role="tabpanel">
                <div class="products-conatiner">
                    @foreach ($newArrivals as $product)
                    @php 
                        $url = getProductUrl($product); 
                        $isInWishlist = auth()->check()
                            ? Wishlist::where('user_id', auth()->id())
                                ->where('product_id', $product->id)
                                ->exists()
                            : false;
                    @endphp
                    <a class="product-item" href="{{ $url }}">
                        <div class="position-relative d-inline-block w-100">
                        <img src="{{ Storage::url($product->image) }}" class="loaded" alt="{{ $product->name }}" loading="lazy">
                        <button type="button" class="btn p-0 border-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow wishlist-btn {{ $isInWishlist ? 'active bg-dark-grey' : 'bg-white' }}"
                            style="width: 30px; height: 30px; z-index: 2;" data-product-id="{{ $product->id }}" data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}"
                            aria-label="Toggle wishlist" onclick="event.preventDefault(); event.stopPropagation();"> 
                            <i class="{{ $isInWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                        </button>
                        </div>
                        <p class="mb-1">{{ $product->name }}</p>
                        @include('frontend.partials.product-price', ['product' => $product])
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Best Seller -->
            <div class="tab-pane fade" id="best-seller-tab-pane" role="tabpanel">
                <div class="products-conatiner">
                    @foreach ($bestSellers as $product)
                    @php 
                        $url = getProductUrl($product); 
                        $isInWishlist = auth()->check()
                            ? Wishlist::where('user_id', auth()->id())
                                ->where('product_id', $product->id)
                                ->exists()
                            : false;
                    @endphp
                    <a class="product-item" href="{{ $url }}">
                        <div class="position-relative d-inline-block w-100">
                        <img src="{{ Storage::url($product->image) }}" class="loaded" alt="{{ $product->name }}" loading="lazy">
                        <button type="button" class="btn p-0 border-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow wishlist-btn {{ $isInWishlist ? 'active bg-dark-grey' : 'bg-white' }}"
                            style="width: 30px; height: 30px; z-index: 2;" data-product-id="{{ $product->id }}" data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}"
                            aria-label="Toggle wishlist" onclick="event.preventDefault(); event.stopPropagation();"> 
                            <i class="{{ $isInWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                        </button>
                        </div>
                        <p class="mb-1">{{ $product->name }}</p>
                        @include('frontend.partials.product-price', ['product' => $product])
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-4 bg-body-secondary" id="your-idea-our-brush">
    <div class="container">
        <div class="d-flex align-items-center">
            <div class="col text-small-center">
                <img src="{{ asset($ideaBrush->image) }}" alt="Custmize Now" class="w-80" loading="lazy" />
            </div>
            <div class="col">
                <div class="py-md-3 px-3">
                    <h1>{{ $ideaBrush->title }}</h1>
                    <p class="text-justify">{!! $ideaBrush->body !!}</p>
                    <a class="btn btn-outline-secondary view-all-btn mt-2 bg-dark" href="{{ $ideaBrush->button_url }} ">{{ $ideaBrush->button_text }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="sliding-text bg-dark py-3 px-2 w-100">
    <div class="scroll-container">
        <div class="scroll-content">
            <!-- ORIGINAL ITEMS -->
             @foreach($highlights as $highlight)
            <div class="item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1 l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> {{ $highlight->title }}
                <img src="{{ Storage::url($highlight->emoji) }}" class="emoji" alt="{{ $highlight->alt_text }}" loading="lazy">
            </div>
            @endforeach
        </div>
    </div>
</section>
<section class="bg-body-primary py-4" id="graphics-section">
    <div class="container">
        <div class="d-flex align-items-center reverse-sm">
            <div class="col">
                <div class="py-md-3 px-3">
                    <h1>{{ $graphicsDesign->title }}</h1>
                    <p class="text-justify">{!! $graphicsDesign->body !!}</p>
                    <a class="btn btn-outline-secondary view-all-btn mt-2 bg-dark" href="{{ $graphicsDesign->button_url }}">{{ $graphicsDesign->button_text }}</a>
                </div>
            </div>
            <div class="col text-md-right text-small-center">
                <img src="{{ asset($graphicsDesign->image) }}" alt="Custmize Now" class="customize-image" loading="lazy" />
            </div>
        </div>
    </div>
</section>
<section class="container-fluid py-3" id="who-we-are">
    <div class="row px-4">
        <h2 class="mb-0">{{ $whoWeAre->title }}</h2>
        <p class="mt-2">{{ $whoWeAre->subtitle }}</p>
    </div>

    <div class="row mt-2 px-3">
        <div id="whoWeAreSlider">
            <div class="owl-carousel owl-theme">
                @foreach($whoWeAre->items as $item)
                <div class="item">
                    <img src="{{ asset($item->image) }}" class="w-100 border rounded" alt="{{ $item->title }}" loading="lazy">
                    <a href="{{ $item->link_url }}">
                        <div class="item-box ">
                            <span>{{ $item->title }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<section class="bg-body-primary py-3" id="inspired-by-art">
    <div class="container">
        <div class="row">
            <h2 class="text-center">{{ $inspiredArt->title }}</h2>
            <p class="text-center">{{ $inspiredArt->subtitle }}</p>
        </div>
        <div class="row">
            @foreach($inspiredArt->items as $item)
            <div class="col text-center">
                <img alt="Timeless" src="{{ asset($item->image) }}" loading="lazy" />
                <h4>{{ $item->title }}</h4>
            </div>
            @endforeach
        </div>
        <div class="row text-center mt-4">
            {!! $inspiredArt->body !!}
        </div>
    </div>
</section>
<section class="container-fluid py-3" id="who-we-are">
    <div class="row px-3">
        <h3 class="text-center mb-4">What People Say About Us</h3>
    </div>

    <div class="row px-3">
        <div class="owl-carousel owl-theme testimonials-carousel">

            @foreach($reviews as $review)
            <div class="item">
                <div class="testimonial-card">

                    <div class="testimonial-img">
                        <img src="{{ asset($review->image) }}" alt="{{ $review->name }}">
                    </div>

                    <div class="testimonial-content">

                        <div class="rating-badge">
                            <span>

                                {{-- Filled Stars --}}
                                @for($i = 1; $i <= $review->stars; $i++)
                                    <i class="fas fa-star" style="color: gold;"></i>
                                @endfor

                                {{-- Empty Stars --}}
                                @for($i = $review->stars + 1; $i <= 5; $i++)
                                    <i class="far fa-star" style="color: #ccc;"></i>
                                @endfor

                            </span>
                        </div>

                        <h4>{{ $review->name }}</h4>

                        <p>
                            {{ $review->testimonial }}
                        </p>

                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<script>
    const textData = [];
    @foreach($highlights as $highlight)
        textData.push(`<span>{{ $highlight->title }}</span>
             <img src="{{ Storage::url($highlight->emoji) }}" class="emoji" loading="lazy" alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
    @endforeach
</script>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $("#whoWeAreSlider .owl-carousel").owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            dots: true,
            autoplay: false,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            smartSpeed: 800,

            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                1200: {
                    items: 4
                }
            }
        });
    });

    $(document).ready(function() {
        $(".testimonials-carousel").owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3500,
            autoplayHoverPause: true,
            smartSpeed: 800,

            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1200: {
                    items: 4
                }
            }
        });
    });
</script>
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
        const productId  = $button.attr('data-product-id');
        const inWishlist = String($button.attr('data-in-wishlist')) === '1';
        const url        = inWishlist ? wishlistConfig.removeUrl : wishlistConfig.addUrl;

        $button.prop('disabled', true);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: wishlistConfig.csrfToken,
                product_id: productId
            },
            success: function (response) {
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
            error: function (xhr) {
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
            complete: function () {
                $button.prop('disabled', false);
            }
        });
    }

    $(".wishlist-btn").click(function() {
        toggleWishlist($(this));
    });
</script>
@endpush
