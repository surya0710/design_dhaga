@extends('frontend.layouts.app')
@section('title', $pageContent->meta_title ?? '')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', $pageContent->meta_title ?? '')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

@section('twitter_card', 'summary')
@section('twitter_title', 'Design Dhaga - Hand-Painted Fashion & Custom Branding Services')
@section('twitter_site', '@designdhaga')
@section('twitter_description', 'Handmade, hand-painted ethnic wear & designer clothing for women, men, kids & celebrities. Explore exclusive twinning dresses, sarees & custom branding services online.')
@section('twitter_image', asset($pageContent->meta_image ?? 'og-home.jpg'))
@section('twitter_image_alt', 'Design Dhaga hero image - Hand-painted handmade ethnic wear and designer clothing collections')

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
            @php
                $categoryImage = responsiveImage('uploads/categories/'.$category->image, [96, 160, 240]);
            @endphp
            <div class="text-center">
                <a href="{{ route('shop.index', [$category->slug]) }}" class="text-decoration-none">
                    <img src="{{ $categoryImage['src'] }}" srcset="{{ $categoryImage['srcset'] }}" sizes="120px" alt="{{ $category->alt_tag }} " class="img-fluid" loading="lazy" decoding="async">
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
        @php
            $sliderImage = responsiveImage($slider->image, [768, 1280, 1600], 'storage');
            $sliderLoadingAttribute = $loop->first ? 'fetchpriority="high"' : 'loading="lazy"';
        @endphp
        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
            <a href="{{ $slider->button_link }}" target="{{  $slider->target }}">
                <img src="{{ $sliderImage['src'] }}" srcset="{{ $sliderImage['srcset'] }}" sizes="100vw" class="d-block w-100" alt="{{ $slider->image_alt }}" decoding="async" {!! $sliderLoadingAttribute !!}>
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
                <h3 class="my-0">{{ $item->title }}</h3>
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
                @php
                    $featureImage = responsiveImage($item->image, [64, 96, 128]);
                @endphp
                <div class="feature-item col p-0">
                    <img src="{{ $featureImage['src'] }}" srcset="{{ $featureImage['srcset'] }}" sizes="64px" class="mobile-icons" loading="lazy" decoding="async" alt="{{ $item->alt_tag ?: $item->title }}" />
                    <h3 class="fs-12">{{ $item->title }}</h3>
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
            <!-- New Arrival -->
            <div class="tab-pane fade show active" id="new-arrival-tab-pane" role="tabpanel">
                <div class="products-conatiner">
                    @foreach ($newArrivals as $product)
                    @php 
                        $url = getProductUrl($product); 
                        $isInWishlist = in_array($product->id, $wishlistProductIds ?? [], true);
                        $productImage = responsiveImage($product->image, [240, 360, 480, 640], 'storage');
                    @endphp
                    <a class="product-item" href="{{ $url }}">
                        <div class="position-relative d-inline-block w-100">
                        <img src="{{ $productImage['src'] }}" srcset="{{ $productImage['srcset'] }}" sizes="(max-width: 768px) 50vw, 220px" class="loaded" alt="{{ $product->name }}" loading="lazy" decoding="async">
                        <button type="button" class="btn p-0 border-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow wishlist-btn {{ $isInWishlist ? 'active' : '' }}"
                            style="width: 30px; height: 30px; z-index: 2;" data-product-id="{{ $product->id }}" data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}"
                            aria-label="Toggle wishlist">
                            <i class="fa-solid fa-heart"></i>
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
                        $isInWishlist = in_array($product->id, $wishlistProductIds ?? [], true);
                        $productImage = responsiveImage($product->image, [240, 360, 480, 640], 'storage');
                    @endphp
                    <a class="product-item" href="{{ $url }}">
                        <div class="position-relative d-inline-block w-100">
                        <img src="{{ $productImage['src'] }}" srcset="{{ $productImage['srcset'] }}" sizes="(max-width: 768px) 50vw, 220px" class="loaded" alt="{{ $product->name }}" loading="lazy" decoding="async">
                        <button type="button" class="btn p-0 border-0 position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow wishlist-btn {{ $isInWishlist ? 'active' : '' }}"
                            style="width: 30px; height: 30px; z-index: 2;" data-product-id="{{ $product->id }}" data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}"
                            aria-label="Toggle wishlist">
                            <i class="fa-solid fa-heart"></i>
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
            @php
                $ideaBrushImage = responsiveImage($ideaBrush->image, [480, 768, 1024]);
            @endphp
            <div class="col text-small-center">
                <img src="{{ $ideaBrushImage['src'] }}" srcset="{{ $ideaBrushImage['srcset'] }}" sizes="(max-width: 768px) 90vw, 45vw" alt="{{ $ideaBrush->alt_tag ?: $ideaBrush->title }}" class="w-80" loading="lazy" decoding="async" />
            </div>
            <div class="col">
                <div class="py-md-3 px-3">
                    <h3 class="fs-28">{{ $ideaBrush->title }}</h3>
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
            @php
                $highlightImage = responsiveImage($highlight->emoji, [32, 48, 64], 'storage');
            @endphp
            <div class="item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1 l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> {{ $highlight->title }}
                <img src="{{ $highlightImage['src'] }}" srcset="{{ $highlightImage['srcset'] }}" sizes="32px" class="emoji" alt="{{ $highlight->alt_text }}" loading="lazy" decoding="async">
            </div>
            @endforeach
        </div>
    </div>
</section>
<section class="bg-body-primary py-4" id="graphics-section">
    <div class="container">
        <div class="d-flex align-items-center reverse-sm">
            @php
                $graphicsImage = responsiveImage($graphicsDesign->image, [480, 768, 1024]);
            @endphp
            <div class="col">
                <div class="py-md-3 px-3">
                    <h3 class="fs-28">{{ $graphicsDesign->title }}</h3>
                    <p class="text-justify">{!! $graphicsDesign->body !!}</p>
                    <a class="btn btn-outline-secondary view-all-btn mt-2 bg-dark" href="{{ $graphicsDesign->button_url }}">{{ $graphicsDesign->button_text }}</a>
                </div>
            </div>
            <div class="col text-md-right text-small-center">
                <img src="{{ $graphicsImage['src'] }}" srcset="{{ $graphicsImage['srcset'] }}" sizes="(max-width: 768px) 90vw, 45vw" alt="{{ $graphicsDesign->alt_tag ?: $graphicsDesign->title }}" class="customize-image" loading="lazy" decoding="async" />
            </div>
        </div>
    </div>
</section>
<!-- @include('frontend.partials.instagram-feed') -->
<section class="container-fluid py-3" id="who-we-are">
    <div class="row px-4">
        <h2 class="mb-0">{{ $whoWeAre->title }}</h2>
        <p class="mt-2">{{ $whoWeAre->subtitle }}</p>
    </div>

    <div class="row mt-2 px-3">
        <div id="whoWeAreSlider">
            <div class="owl-carousel owl-theme">
                @foreach($whoWeAre->items as $item)
                @php
                    $whoWeAreImage = responsiveImage($item->image, [320, 480, 640]);
                @endphp
                <div class="item">
                    <img src="{{ $whoWeAreImage['src'] }}" srcset="{{ $whoWeAreImage['srcset'] }}" sizes="(max-width: 575px) 100vw, (max-width: 1199px) 33vw, 25vw" class="w-100 border rounded" alt="{{ $item->alt_tag ?: $item->title }}" loading="lazy" decoding="async">
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
            @php
                $inspiredImage = responsiveImage($item->image, [160, 240, 320]);
            @endphp
            <div class="col text-center">
                <img alt="{{ $item->alt_tag ?: $item->title }}" src="{{ $inspiredImage['src'] }}" srcset="{{ $inspiredImage['srcset'] }}" sizes="(max-width: 768px) 33vw, 180px" loading="lazy" decoding="async" />
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
            @php
                $reviewImage = responsiveImage($review->image, [160, 240, 320]);
            @endphp
            <div class="item">
                <div class="testimonial-card">

                    <div class="testimonial-img">
                        <img src="{{ $reviewImage['src'] }}" srcset="{{ $reviewImage['srcset'] }}" sizes="120px" alt="{{ $review->alt_tag }}" loading="lazy" decoding="async">
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
        @php
            $highlightImage = responsiveImage($highlight->emoji, [32, 48, 64], 'storage');
        @endphp
        textData.push(`<span>{{ $highlight->title }}</span>
             <img src="{{ $highlightImage['src'] }}" srcset="{{ $highlightImage['srcset'] }}" sizes="32px" class="emoji" loading="lazy" decoding="async" alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
    @endforeach
</script>
@endsection

@push('extras')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<style>
    .instagram-feed-section {
        background: #fff;
    }

    .instagram-feed-title {
        color: #111827;
    }

    .instagram-feed-icon {
        margin-left: 8px;
        background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .instagram-feed-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .instagram-profile-bar {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px 20px;
        background: #fff;
    }

    .instagram-profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

    .instagram-profile-avatar--placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #9ca3af;
        font-size: 1.5rem;
    }

    .instagram-profile-name {
        font-weight: 700;
        color: #111827;
        margin-right: 10px;
    }

    .instagram-profile-handle {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .instagram-profile-bio {
        color: #374151;
        font-size: 0.92rem;
        margin-top: 6px;
    }

    .instagram-profile-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        color: #374151;
        font-size: 0.9rem;
    }

    .instagram-follow-btn {
        background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        border: none;
        color: #fff;
        border-radius: 999px;
        padding: 10px 22px;
        font-weight: 600;
        white-space: nowrap;
    }

    .instagram-follow-btn:hover {
        color: #fff;
        opacity: 0.92;
    }

    .instagram-feed-item {
        padding: 0 4px;
    }

    .instagram-feed-media {
        position: relative;
        display: block;
        overflow: hidden;
        border-radius: 8px;
    }

    .instagram-feed-image {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        display: block;
        border-radius: 8px;
        background: #f3f4f6;
    }

    #instagramFeedSlider .owl-carousel .owl-item.loading {
        background: transparent;
        min-height: 0;
    }

    #instagramFeedSlider .ajax-loader {
        display: none !important;
    }

    .instagram-feed-play {
        position: absolute;
        left: 10px;
        bottom: 10px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.92);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #111827;
        font-size: 0.7rem;
    }

    .instagram-feed-caption {
        margin: 10px 0 0;
        font-size: 0.82rem;
        line-height: 1.45;
        color: #374151;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    #instagramFeedSlider .owl-dots {
        margin-top: 18px;
    }

    #instagramFeedSlider .owl-dot span {
        width: 8px;
        height: 8px;
        margin: 4px;
        background: #d1d5db;
    }

    #instagramFeedSlider .owl-dot.active span {
        background: #6b7280;
    }

    @media (max-width: 767px) {
        .instagram-profile-bar {
            display: grid;
            grid-template-columns: auto 1fr;
            grid-template-areas:
                "avatar heading"
                "bio bio"
                "button button";
            gap: 10px 12px;
            align-items: center;
            padding: 16px;
        }

        .instagram-profile-avatar-wrap {
            grid-area: avatar;
        }

        .instagram-profile-info {
            display: contents;
        }

        .instagram-profile-heading {
            grid-area: heading;
            min-width: 0;
        }

        .instagram-profile-name {
            display: block;
            margin-right: 0;
            font-size: 0.95rem;
            line-height: 1.3;
        }

        .instagram-profile-handle {
            display: block;
            font-size: 0.85rem;
            margin-top: 2px;
        }

        .instagram-profile-bio {
            grid-area: bio;
            margin-top: 0;
            font-size: 0.88rem;
        }

        .instagram-profile-avatar {
            width: 56px;
            height: 56px;
        }

        .instagram-follow-btn {
            grid-area: button;
            width: 100%;
            text-align: center;
        }

        .instagram-feed-caption {
            font-size: 0.75rem;
            -webkit-line-clamp: 2;
        }
    }
</style>
@endpush

@push('wishlist-options')
<script>window.wishlistOptions = { reloadOnError: true };</script>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script>
    $(document).ready(function() {
        if ($("#instagramFeedSlider .owl-carousel").length) {
            const instagramPostCount = $("#instagramFeedSlider .instagram-feed-item").length;

            $("#instagramFeedSlider .owl-carousel").owlCarousel({
                loop: instagramPostCount > 5,
                margin: 16,
                nav: true,
                dots: true,
                slideBy: 'page',
                lazyLoad: false,
                autoplay: false,
                smartSpeed: 300,

                responsive: {
                    0: {
                        items: 3
                    },
                    992: {
                        items: 4
                    },
                    1200: {
                        items: 5
                    }
                }
            }).on('initialized.owl.carousel refreshed.owl.carousel', function () {
                $(this).find('.owl-item').removeClass('loading');
            });
        }
    });

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
@endpush
