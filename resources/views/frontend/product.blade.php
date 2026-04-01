@extends('frontend.layouts.app')

@section('title', $product->meta_title ?? $product->name)
@section('meta_description', $product->meta_description ?? '')
@section('meta_keywords', $product->meta_keywords ?? '')
@section('og_title', $product->meta_title ?? $product->name)
@section('og_description', $product->meta_description ?? '')
@section('og_image', asset('storage/' . $product->image))

@push('styles')
{{-- SweetAlert2 CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="px-2 px-md-5 mt-3">
        <div class="row g-4 align-items-stretch">

            {{-- ============================================================ --}}
            {{-- IMAGE COLUMN                                                  --}}
            {{-- ============================================================ --}}
            <div class="col-md-6">

                {{-- DESKTOP: Thumbnails left + Main image right --}}
                <div class="d-none d-md-block position-relative">
                    <div class="d-flex gap-3">

                        {{-- Desktop Thumbnails (left side) --}}
                        <div class="d-flex flex-column gap-2 overflow-hidden"
                             style="max-height: 700px; scrollbar-width: thin;">

                            <img src="{{ asset('storage/' . $product->image) }}"
                                 class="desktop-thumb border-2 border-danger cursor-pointer"
                                 style="width: 80px;"
                                 onclick="setDesktopImage(this)" />

                            @foreach ($product->galleryImages as $img)
                                <img src="{{ asset('storage/' . $img->image) }}"
                                     class="desktop-thumb cursor-pointer"
                                     style="width: 80px; opacity: 0.6;"
                                     onclick="setDesktopImage(this)" />
                            @endforeach
                        </div>

                        {{-- Main Image (right side) --}}
                        <div class="carousel-container position-relative overflow-hidden flex-grow-1">
                            <img id="desktopMainImage"
                                 src="{{ asset('storage/' . $product->image) }}"
                                 class="cursor-pointer w-100"
                                 style="object-fit: contain; display: block; max-height: 700px;"
                                 alt="{{ $product->name }}"
                                 onclick="openImageModal()" />

                            <button class="btn btn-light rounded-circle position-absolute start-0 top-50 translate-middle-y ms-2 shadow"
                                    style="z-index: 10; width: 45px; height: 45px;"
                                    onclick="prevDesktopImage()">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>

                            <button class="btn btn-light rounded-circle position-absolute end-0 top-50 translate-middle-y me-2 shadow"
                                    style="z-index: 10; width: 45px; height: 45px;"
                                    onclick="nextDesktopImage()">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- MOBILE: Title + Price + Main image + Bottom thumbnails --}}
                <div class="d-md-none">

                    {{-- Mobile: Title, Rating, Price --}}
                    <div class="mb-3">
                        <h2 class="mb-1 mt-0">{{ $product->name }}</h2>

                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="text-warning">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                            <span class="small text-muted">4.0 (128 reviews)</span>
                        </div>

                        <p class="text-black mb-0 small">{{ $product->short_description }}</p>

                        <div class="h6 mb-0 price">
                            @if ($product->sale_price)
                                <span class="fw-bold text-black">₹ {{ number_format($product->sale_price, 0) }}</span>
                                <span class="text-decoration-line-through text-muted small ms-2">
                                    ₹ {{ number_format($product->regular_price, 0) }}
                                </span>
                                <span class="text-maroon small ms-2 fw-semibold">
                                    Save {{ number_format((1 - ($product->sale_price / $product->regular_price)) * 100, 0) }}%
                                </span>
                            @else
                                <span class="fw-bold text-black">₹ {{ number_format($product->regular_price, 0) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">

                        {{-- Mobile Main Image --}}
                        <div class="position-relative" style="overflow: hidden;">
                            <img id="mobileMainImage"
                                 src="{{ asset('storage/' . $product->image) }}"
                                 class="cursor-pointer w-100"
                                 alt="{{ $product->name }}"
                                 onclick="openImageModal()" />

                            <button class="btn btn-light rounded-circle position-absolute start-0 top-50 translate-middle-y shadow"
                                    style="z-index: 10; width: 35px; height: 35px; left: 4px;"
                                    onclick="prevMobileImage()">
                                <i class="fa-solid fa-chevron-left" style="font-size: 12px;"></i>
                            </button>

                            <button class="btn btn-light rounded-circle position-absolute end-0 top-50 translate-middle-y shadow"
                                    style="z-index: 10; width: 35px; height: 35px; right: 4px;"
                                    onclick="nextMobileImage()">
                                <i class="fa-solid fa-chevron-right" style="font-size: 12px;"></i>
                            </button>
                        </div>

                        {{-- Mobile Thumbnails --}}
                        <div class="d-flex gap-2 overflow-auto pb-1" style="scrollbar-width: thin;">
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 class="border border-2 border-danger mobile-thumb"
                                 style="width: 70px;"
                                 onclick="changeImage(this)"
                                 ondblclick="openImageModal(this.src)" />

                            @foreach ($product->galleryImages as $img)
                                <img src="{{ asset('storage/' . $img->image) }}"
                                     class="mobile-thumb"
                                     style="width: 70px; opacity: 0.6;"
                                     onclick="changeImage(this)"
                                     ondblclick="openImageModal(this.src)" />
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
            {{-- END IMAGE COLUMN --}}


            {{-- ============================================================ --}}
            {{-- DETAILS COLUMN                                                --}}
            {{-- ============================================================ --}}
            <div class="col-md-6">
                <div class="sticky-md-top h-100">

                    {{-- Desktop: Title + Wishlist + Stars --}}
                    <div class="d-flex justify-content-between align-items-start d-none d-md-flex">
                        <div>
                            <h1 class="h3 fw-bold mb-1 mt-0" style="font-size: 25px;">
                                {{ $product->name }}
                            </h1>

                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div class="text-warning">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-regular fa-star"></i>
                                </div>
                                <span class="small text-muted">4.0 (128 reviews)</span>
                            </div>
                        </div>

                        <button class="btn bg-maroon rounded-circle d-flex align-items-center justify-content-center text-white wishlist-btn">
                            <i class="fa-regular fa-heart fa-lg"></i>
                        </button>
                    </div>

                    <p class="text-black mb-0 d-none d-md-block">
                        {{ $product->short_description }}
                    </p>

                    {{-- Desktop Price --}}
                    <div class="h4 mb-2 d-none d-md-block price">
                        @if ($product->sale_price)
                            <span class="fw-bold text-black">₹ {{ number_format($product->sale_price, 0) }}</span>
                            <span class="text-decoration-line-through text-muted small ms-2">
                                ₹ {{ number_format($product->regular_price, 0) }}
                            </span>
                            <span class="text-maroon ms-2 fw-semibold">
                                Save {{ number_format((1 - ($product->sale_price / $product->regular_price)) * 100, 0) }}%
                            </span>
                        @else
                            <span class="fw-bold text-black">₹ {{ number_format($product->regular_price, 0) }}</span>
                        @endif
                    </div>

                    {{-- Add to Cart / Request to Purchase --}}
                    @if ($product->type == 1)
                        <button id="addToCartBtn"
                                class="btn bg-maroon text-white w-100 py-3 fw-bold mb-2 btn-add-to-cart"
                                onclick="handleAddToCart({{ $product->id }})">
                            <span class="btn-text">
                                Add To Cart &nbsp;|&nbsp; ₹ <span id="total">{{ $product->sale_price ?? $product->regular_price }}</span>
                            </span>
                            <span class="btn-spinner d-none">
                                <span class="spinner-ring"></span>
                                Adding...
                            </span>
                        </button>
                    @else
                        <a class="btn bg-maroon text-white w-100 py-3 fw-bold mb-2"
                           href="https://api.whatsapp.com/send/?phone=919671941303&text={{ urlencode('Hi! I am interested in this product: ' . url()->current()) }}">
                            Request To Purchase
                        </a>
                    @endif

                    {{-- Feature Icons Grid --}}
                    <div class="row g-2 mt-2 mb-2 p-3 rounded bg-light-pink text-center">
                        @php $iconsText = getIconsByCategory($product->category->name); @endphp
                        @for($i = 1; $i < 7; $i++)
                        <div class="col-4">
                            <svg width="30" height="30">
                                <use xlink:href="{{ asset('frontend_assets/images/product-icons/'.$product->category->name.'/'. $i.'.svg') }}"></use>
                            </svg>
                            <p>{{ $iconsText[$i] }}</p>
                        </div>
                        @endfor
                    </div>

                    {{-- Mobile Feature Strip --}}
                    <section class="features-box d-sm-block d-md-none" style="padding: 15px 0 !important;">
                        <div class="container">
                            <div class="row feature-items">
                                <div class="feature-item col">
                                    <img src="{{ asset('frontend_assets/images/easy-delivery-process.svg') }}" class="mobile-icons">
                                    <h4>Easy Delivery</h4>
                                </div>
                                <div class="feature-item col">
                                    <img src="{{ asset('frontend_assets/images/easy-delivery-process.svg') }}" class="mobile-icons">
                                    <h4>Exquisite Product</h4>
                                </div>
                                <div class="feature-item col">
                                    <img src="{{ asset('frontend_assets/images/easy-delivery-process.svg') }}" class="mobile-icons">
                                    <h4>Intricate Design</h4>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Pincode Check --}}
                    <div class="mb-2 mt-1 fw-bold heading-size">
                        <i class="fa-solid fa-truck me-2"></i>Check Delivery Time
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light-pink p-3 border-0" placeholder="Enter pincode" />
                        <button class="btn btn-white border fw-bold">Check</button>
                    </div>

                </div>
            </div>
            {{-- END DETAILS COLUMN --}}

        </div>


        {{-- ============================================================ --}}
        {{-- PRODUCT DETAILS BOX                                          --}}
        {{-- ============================================================ --}}
        @if ($product->productAttributes->count())
            <section style="background-color: #fbe8e9;">
                <div class="py-3 px-3 px-md-4 rounded my-3">
                    <p class="fw-bold text-dark heading-size text-center mb-3">Product Details</p>

                    <div class="row justify-content-between product-details">
                        @foreach ($product->productAttributes as $index => $attr)
                            <div class="fs-xs-11 text-start
                                {{ $index % 3 == 0 ? 'text-md-start' : ($index % 3 == 1 ? 'text-md-center' : 'text-md-end') }}">
                                {{ $attr->key }}: <strong>{{ $attr->value }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

    </div>


    {{-- ============================================================ --}}
    {{-- FULL-WIDTH TABS                                               --}}
    {{-- ============================================================ --}}
    <div class="w-100 bg-white pt-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 px-2 px-md-5">

                    {{-- Tab Navigation (Desktop Only) --}}
                    <ul class="nav nav-underline border-bottom border-2 mb-0 gap-4 d-none d-md-flex justify-content-between"
                        role="tablist">

                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-dark py-4 fs-5"
                                    id="desc-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#descTab"
                                    type="button"
                                    role="tab">
                                <i class="fa-solid fa-book me-2 text-maroon"></i>Product Description
                            </button>
                        </li>

                        @if ($product->hand_painted_details)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-dark py-4 fs-5"
                                        id="handPainted-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#handPaintedTab"
                                        type="button"
                                        role="tab">
                                    <i class="fa-solid fa-palette me-2 text-maroon"></i>Hand Painted Details
                                </button>
                            </li>
                        @endif

                        @if ($product->care_instructions)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-dark py-4 fs-5"
                                        id="care-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#careTab"
                                        type="button"
                                        role="tab">
                                    <i class="fa-solid fa-heart-pulse me-2 text-maroon"></i>Care Instructions
                                </button>
                            </li>
                        @endif

                        @if ($product->manufacturing_details)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-dark py-4 fs-5"
                                        id="manufacturing-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#manufacturingTab"
                                        type="button"
                                        role="tab">
                                    <i class="fa-solid fa-tools me-2 text-maroon"></i>Manufacturing Details
                                </button>
                            </li>
                        @endif

                    </ul>

                    {{-- Tab Content (Desktop Only) --}}
                    <div class="tab-content pt-2 pb-2 d-none d-md-block">

                        {{-- Product Description --}}
                        <div class="tab-pane fade show active" id="descTab" role="tabpanel">
                            <div class="row g-4 align-items-stretch">
                                <div class="col-lg-12">
                                    <p class="text-dark lh-lg mb-4 fs-6">{!! $product->description !!}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Hand Painted Details --}}
                        @if ($product->hand_painted_details)
                            <div class="tab-pane fade" id="handPaintedTab" role="tabpanel">
                                <div class="row g-4 align-items-stretch">
                                    <div class="col-lg-12">
                                        <p class="text-dark lh-lg mb-4 fs-6">
                                            {!! nl2br(e($product->hand_painted_details)) !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Care Instructions --}}
                        @if ($product->care_instructions)
                            <div class="tab-pane fade" id="careTab" role="tabpanel">
                                <div class="row g-4 align-items-stretch">
                                    <div class="col-lg-12">
                                        <p class="text-dark lh-lg mb-4 fs-6">
                                            {!! nl2br(e($product->care_instructions)) !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Manufacturing Details --}}
                        @if ($product->manufacturing_details)
                            <div class="tab-pane fade" id="manufacturingTab" role="tabpanel">
                                <div class="row g-4 align-items-stretch">
                                    <div class="col-lg-12">
                                        <p class="text-dark lh-lg mb-4 fs-6">
                                            {!! nl2br(e($product->manufacturing_details)) !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- ============================================================ --}}
                    {{-- MOBILE ACCORDION                                             --}}
                    {{-- ============================================================ --}}
                    <div class="d-block d-md-none py-2">
                        <div class="accordion accordion-flush" id="productAccordion">

                            {{-- Product Description --}}
                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header" id="headingDesc">
                                    <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapseDesc"
                                            aria-expanded="false"
                                            aria-controls="collapseDesc">
                                        <i class="fa-solid fa-book me-2 text-maroon"></i>Product Description
                                    </button>
                                </h2>
                                <div id="collapseDesc"
                                     class="accordion-collapse collapse"
                                     aria-labelledby="headingDesc"
                                     data-bs-parent="#productAccordion">
                                    <div class="accordion-body px-0 pb-4">
                                        <p class="text-dark lh-lg mb-0 fs-6">{!! $product->description !!}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Hand Painted Details --}}
                            @if ($product->hand_painted_details)
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="headingHandPainted">
                                        <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseHandPainted"
                                                aria-expanded="false"
                                                aria-controls="collapseHandPainted">
                                            <i class="fa-solid fa-palette me-2 text-maroon"></i>Hand Painted Details
                                        </button>
                                    </h2>
                                    <div id="collapseHandPainted"
                                         class="accordion-collapse collapse"
                                         aria-labelledby="headingHandPainted"
                                         data-bs-parent="#productAccordion">
                                        <div class="accordion-body px-0 pb-4">
                                            <p class="text-dark lh-lg mb-0 fs-6">
                                                {!! nl2br(e($product->hand_painted_details)) !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Care Instructions --}}
                            @if ($product->care_instructions)
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="headingCare">
                                        <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseCare"
                                                aria-expanded="false"
                                                aria-controls="collapseCare">
                                            <i class="fa-solid fa-heart-pulse me-2 text-maroon"></i>Care Instructions
                                        </button>
                                    </h2>
                                    <div id="collapseCare"
                                         class="accordion-collapse collapse"
                                         aria-labelledby="headingCare"
                                         data-bs-parent="#productAccordion">
                                        <div class="accordion-body px-0 pb-4">
                                            <p class="text-dark lh-lg mb-0 fs-6">
                                                {!! nl2br(e($product->care_instructions)) !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Manufacturing Details --}}
                            @if ($product->manufacturing_details)
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="headingManufacturing">
                                        <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseManufacturing"
                                                aria-expanded="false"
                                                aria-controls="collapseManufacturing">
                                            <i class="fa-solid fa-tools me-2 text-maroon"></i>Manufacturing Details
                                        </button>
                                    </h2>
                                    <div id="collapseManufacturing"
                                         class="accordion-collapse collapse"
                                         aria-labelledby="headingManufacturing"
                                         data-bs-parent="#productAccordion">
                                        <div class="accordion-body px-0 pb-4">
                                            <p class="text-dark lh-lg mb-0 fs-6">
                                                {!! nl2br(e($product->manufacturing_details)) !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                    {{-- END MOBILE ACCORDION --}}

                </div>
            </div>
        </div>
    </div>


    {{-- ============================================================ --}}
    {{-- SECTION 2: Artisan Gallery                                   --}}
    {{-- ============================================================ --}}
    @if ($product->artisanImages->count())
        <section class="bg-white py-0">
            <div class="container-fluid px-0 overflow-hidden">
                <div class="row position-relative px-3 m-0 align-items-center justify-content-center text-center">
                    <div class="col-12 col-md-8 position-relative" style="z-index: 2;">
                        <h2 class="px-md-5 mt-0">
                            {{ $product->artisan_heading ?? 'When we say our artisans go an extra mile, we mean it.' }}
                        </h2>
                    </div>
                </div>
            </div>

            <div class="px-2 px-md-5 pt-2 pb-2">
                <div class="row g-4 g-xl-5">
                    @foreach ($product->artisanImages as $artisan)
                        <div class="col-lg-4 col-md-6">
                            @if ($artisan->image)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $artisan->image) }}"
                                         class="w-100"
                                         alt="{{ $artisan->title ?? 'Artisan' }}" />
                                </div>
                            @endif

                            @if ($artisan->title)
                                <h3 class="h5 fw-bold border-bottom border-2 border-dark pb-2 mb-3 text-uppercase"
                                    style="letter-spacing: 1px;">
                                    {{ $artisan->title }}
                                </h3>
                            @endif

                            @if ($artisan->description)
                                <p class="text-secondary small lh-lg">
                                    {!! nl2br(e($artisan->description)) !!}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ============================================================ --}}
    {{-- SECTION 3: Square Banner                                     --}}
    {{-- ============================================================ --}}
    @if ($product->square_banner)
        <section class="py-md-2 bg-white">
            <div class="container-fluid px-2 px-md-5">
                <div class="row align-items-center g-4 g-lg-5">

                    {{-- Mobile Heading --}}
                    <div class="col-12 d-lg-none mb-2">
                        <h2 class="fw-bold text-black my-0" style="line-height: 1.3;">
                            {{ $product->square_banner_title ?? '' }}
                        </h2>
                    </div>

                    {{-- Left Image --}}
                    <div class="col-lg-5">
                        <div class="style-comfort-img rounded-3 overflow-hidden">
                            <img src="{{ asset('storage/' . $product->square_banner) }}"
                                 class="img-fluid w-100"
                                 alt="{{ $product->square_banner_title ?? $product->name }}"
                                 style="object-fit: cover;" />
                        </div>
                    </div>

                    {{-- Right Content --}}
                    <div class="col-lg-7 ps-lg-5">

                        {{-- Desktop Heading --}}
                        @if ($product->square_banner_title)
                            <h2 class="fw-bold d-none d-lg-block mb-4 text-black" style="font-size: 2.6rem;">
                                {{ $product->square_banner_title }}
                            </h2>
                        @endif

                        @if ($product->square_banner_description)
                            <p class="mb-0 text-muted" style="font-size: 1.05rem;">
                                {!! nl2br(e($product->square_banner_description)) !!}
                            </p>
                        @endif

                    </div>

                </div>
            </div>
        </section>
    @endif


    {{-- ============================================================ --}}
    {{-- SECTION 4: Reviews (Auth Only)                               --}}
    {{-- ============================================================ --}}
    @if (Auth::check())
        <section class="py-5">
            <div class="container">

                <div id="reviewSummary" class="shadow p-4 p-md-5 rounded-1 d-flex flex-column align-items-center">
                    <h2 class="text-center fw-bold fs-1 mb-5">Customer Reviews</h2>
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-4 gap-md-5 text-center">
                        <div class="text-center text-md-start">
                            <div class="mb-2">
                                <i class="fa-regular fa-star fs-4" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star fs-4" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star fs-4" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star fs-4" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star fs-4" style="color: #c05a65;"></i>
                            </div>
                            <p class="m-0 fs-5 text-dark text-nowrap">Be the first to write a review</p>
                        </div>
                        <div class="d-none d-md-block" style="width: 1px; height: 70px; background-color: #ddd;"></div>
                        <div class="w-100 w-md-auto">
                            <button onclick="toggleReviewForm()"
                                    class="btn rounded-0 fw-semibold fs-5 px-5 py-3 btn-brand-custom w-100 w-md-auto"
                                    style="border: none;">
                                Write a review
                            </button>
                        </div>
                    </div>
                </div>

                <div id="reviewForm" class="bg-white p-4 p-md-5 rounded shadow-sm mx-auto d-none" style="max-width: 800px;">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h3 class="fw-bold mb-1">Customer Reviews</h3>
                            <div class="mb-1">
                                <i class="fa-regular fa-star" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star" style="color: #c05a65;"></i>
                                <i class="fa-regular fa-star" style="color: #c05a65;"></i>
                            </div>
                            <small class="text-muted">Be the first to write a review</small>
                        </div>
                        <button onclick="toggleReviewForm()" class="btn btn-brand-custom px-4">Cancel review</button>
                    </div>

                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Write a review</h4>
                        <div class="mt-3">
                            <label class="d-block mb-2 text-muted small">Rating</label>
                            <div class="star-rating fs-3" style="color: #c05a65;">
                                <i class="fa-regular fa-star" onclick="setRating(1)"></i>
                                <i class="fa-regular fa-star" onclick="setRating(2)"></i>
                                <i class="fa-regular fa-star" onclick="setRating(3)"></i>
                                <i class="fa-regular fa-star" onclick="setRating(4)"></i>
                                <i class="fa-regular fa-star" onclick="setRating(5)"></i>
                            </div>
                        </div>
                    </div>

                    <form>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Review Title (100)</label>
                            <input type="text" class="form-control p-3 bg-light border-0" placeholder="Give your review a title">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Review content</label>
                            <textarea class="form-control p-3 bg-light border-0" rows="5" placeholder="Start writing here..."></textarea>
                        </div>
                        <div class="mb-4 text-center">
                            <label class="form-label text-muted small d-block">Picture (optional)</label>
                            <div class="mx-auto upload-box" onclick="document.getElementById('fileInput').click()">
                                <i class="fa-solid fa-arrow-up-from-bracket fs-3 text-muted"></i>
                                <input type="file" id="fileInput" hidden>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Display name</label>
                            <input type="text" class="form-control p-3 bg-light border-0" placeholder="Enter your name">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Email address</label>
                            <input type="email" class="form-control p-3 bg-light border-0" placeholder="Your email address">
                        </div>
                        <p class="small text-muted text-center mb-4">
                            How we use your data: We'll only contact you about the review you left, and only if necessary.
                            By submitting your review, you agree to Judge.me's
                            <a href="#" class="text-decoration-underline text-muted">terms</a>,
                            <a href="#" class="text-decoration-underline text-muted">privacy</a> and
                            <a href="#" class="text-decoration-underline text-muted">content</a> policies.
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button"
                                    onclick="toggleReviewForm()"
                                    class="btn px-4 py-2"
                                    style="border: 1px solid #8b1e2d; color: #8b1e2d;">
                                Cancel review
                            </button>
                            <button type="button" class="btn btn-brand-custom px-4 py-2">Submit Review</button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    @endif

</div>


{{-- ============================================================ --}}
{{-- IMAGE MODAL                                                   --}}
{{-- ============================================================ --}}
<div id="imageModal"
     style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.95); z-index: 9999; align-items: center;
            justify-content: center; flex-direction: column; padding: 20px;">

    <button onclick="closeImageModal()"
            style="position: absolute; top: 20px; right: 30px; background: none; border: none;
                   color: white; font-size: 32px; cursor: pointer; z-index: 10000;">
        &times;
    </button>

    <div style="width: 100%; max-width: 600px; height: 60vh; position: relative;
                display: flex; align-items: center; justify-content: center;
                z-index: 9999; overflow: hidden;">

        <img id="modalMainImage" src=""
             style="width: 100%; height: 100%; object-fit: contain;
                    cursor: grab; transition: transform 0.2s ease;" />

        {{-- Zoom Controls --}}
        <div style="position: absolute; bottom: 15px; right: 0; z-index: 10000; display: flex; gap: 6px;">
            <button onclick="zoomIn()"    class="btn btn-light rounded-circle shadow" style="width: 40px; height: 40px;">+</button>
            <button onclick="zoomOut()"   class="btn btn-light rounded-circle shadow" style="width: 40px; height: 40px;">−</button>
            <button onclick="resetZoom()" class="btn btn-light rounded-circle shadow" style="width: 40px; height: 40px;">
                <i class="fa fa-sync"></i>
            </button>
        </div>
    </div>

    {{-- Modal Thumbnails --}}
    <div style="width: 100%; max-width: 600px; display: flex; gap: 8px; margin-top: 20px;
                overflow-x: auto; padding-bottom: 8px; justify-content: center; z-index: 9999;">

        <img src="{{ asset('storage/' . $product->image) }}"
             class="modal-thumb"
             style="width: 70px; height: 90px; object-fit: cover; flex-shrink: 0;
                    border: 2px solid white; cursor: pointer; opacity: 1;"
             onclick="setModalImage(this)" />

        @foreach ($product->galleryImages as $img)
            <img src="{{ asset('storage/' . $img->image) }}"
                 class="modal-thumb"
                 style="width: 70px; height: 90px; object-fit: cover; flex-shrink: 0;
                        cursor: pointer; opacity: 0.5;"
                 onclick="setModalImage(this)" />
        @endforeach
    </div>
</div>

@endsection


@push('scripts')
{{-- jQuery --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ── Image URLs Array ────────────────────────────────────────────────────────
    const imageUrls = [
        "{{ asset('storage/' . $product->image) }}",
        @foreach ($product->galleryImages as $img)
            "{{ asset('storage/' . $img->image) }}",
        @endforeach
    ];

    let desktopImageIndex = 0;
    let modalImageIndex   = 0;
    let mobileIndex       = 0;


    // ── Desktop Image Slider ────────────────────────────────────────────────────
    function setDesktopImage(element) {
        const index = Array.from(document.querySelectorAll('.desktop-thumb')).indexOf(element);
        if (index >= 0) {
            desktopImageIndex = index;
            updateDesktopImage();
        }
    }

    function nextDesktopImage() {
        desktopImageIndex = (desktopImageIndex + 1) % imageUrls.length;
        updateDesktopImage();
    }

    function prevDesktopImage() {
        desktopImageIndex = (desktopImageIndex - 1 + imageUrls.length) % imageUrls.length;
        updateDesktopImage();
    }

    function updateDesktopImage() {
        document.getElementById('desktopMainImage').src = imageUrls[desktopImageIndex];
        document.querySelectorAll('.desktop-thumb').forEach((thumb, index) => {
            thumb.style.opacity = index === desktopImageIndex ? '1' : '0.6';
            thumb.classList.toggle('border-2',    index === desktopImageIndex);
            thumb.classList.toggle('border-danger', index === desktopImageIndex);
        });
    }


    // ── Mobile Image Navigation ─────────────────────────────────────────────────
    function nextMobileImage() {
        mobileIndex = (mobileIndex + 1) % imageUrls.length;
        setMobileImageByIndex(mobileIndex);
    }

    function prevMobileImage() {
        mobileIndex = (mobileIndex - 1 + imageUrls.length) % imageUrls.length;
        setMobileImageByIndex(mobileIndex);
    }


    // ── Image Modal ─────────────────────────────────────────────────────────────
    function openImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display        = 'flex';
        modal.style.flexDirection  = 'column';
        modal.style.alignItems     = 'center';
        modal.style.justifyContent = 'center';
        document.body.style.overflow = 'hidden';
        modalImageIndex = window.innerWidth >= 768 ? desktopImageIndex : mobileIndex;
        updateModalImage();
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function setModalImage(element) {
        const index = Array.from(document.querySelectorAll('.modal-thumb')).indexOf(element);
        if (index >= 0) {
            modalImageIndex = index;
            updateModalImage();
        }
    }

    function nextModalImage() {
        modalImageIndex = (modalImageIndex + 1) % imageUrls.length;
        updateModalImage();
    }

    function prevModalImage() {
        modalImageIndex = (modalImageIndex - 1 + imageUrls.length) % imageUrls.length;
        updateModalImage();
    }

    function updateModalImage() {
        document.getElementById('modalMainImage').src = imageUrls[modalImageIndex];

        scale      = 1;
        translateX = 0;
        translateY = 0;

        if (modalImg) {
            modalImg.style.transform = 'translate(0px, 0px) scale(1)';
            modalImg.style.cursor    = 'default';
        }

        document.querySelectorAll('.modal-thumb').forEach((thumb, index) => {
            thumb.style.opacity     = index === modalImageIndex ? '1' : '0.5';
            thumb.style.borderColor = index === modalImageIndex ? 'white' : 'transparent';
        });
    }


    // ── Mobile Swipe Slider ─────────────────────────────────────────────────────
    let mobileThumbs = [];

    function setMobileImageByIndex(nextIndex) {
        if (!mobileThumbs.length) return;

        const total     = mobileThumbs.length;
        const safeIndex = ((nextIndex % total) + total) % total;
        const nextThumb = mobileThumbs[safeIndex];
        const mainImage = document.getElementById('mobileMainImage');

        if (mainImage) {
            mainImage.classList.remove('mobile-slide-anim');
            mainImage.offsetWidth; // force reflow
            mainImage.src = nextThumb.src;
            mainImage.classList.add('mobile-slide-anim');
        }

        mobileThumbs.forEach(img => {
            img.classList.add('opacity-75');
            img.classList.remove('border', 'border-2', 'border-danger');
        });
        nextThumb.classList.remove('opacity-75');
        nextThumb.classList.add('border', 'border-2', 'border-danger');
        mobileIndex = safeIndex;
    }

    function changeImage(element) {
        if (element && element.classList.contains('mobile-thumb')) {
            const index = mobileThumbs.indexOf(element);
            setMobileImageByIndex(index >= 0 ? index : 0);
            return;
        }
        document.getElementById('mobileMainImage').src = element.src;
        element.parentElement.querySelectorAll('img').forEach(img => {
            img.classList.add('opacity-75');
            img.classList.remove('border', 'border-2', 'border-danger');
        });
        element.classList.remove('opacity-75');
        element.classList.add('border', 'border-2', 'border-danger');
    }

    function setupMobileSwipeSlider() {
        mobileThumbs = Array.from(document.querySelectorAll('.mobile-thumb'));
        if (!mobileThumbs.length) return;

        const mainImage = document.getElementById('mobileMainImage');
        if (!mainImage) return;

        const initialIndex = mobileThumbs.findIndex(img => img.src === mainImage.src);
        setMobileImageByIndex(initialIndex >= 0 ? initialIndex : 0);

        let startX = 0, startY = 0, tracking = false;

        mainImage.addEventListener('touchstart', e => {
            if (e.touches.length !== 1) return;
            startX   = e.touches[0].clientX;
            startY   = e.touches[0].clientY;
            tracking = true;
        }, { passive: true });

        mainImage.addEventListener('touchend', e => {
            if (!tracking) return;
            tracking = false;
            const deltaX = e.changedTouches[0].clientX - startX;
            const deltaY = e.changedTouches[0].clientY - startY;
            if (Math.abs(deltaX) < 35 || Math.abs(deltaX) < Math.abs(deltaY)) return;
            deltaX < 0
                ? setMobileImageByIndex(mobileIndex + 1)
                : setMobileImageByIndex(mobileIndex - 1);
        }, { passive: true });
    }

    document.addEventListener('DOMContentLoaded', setupMobileSwipeSlider);


    // ── Keyboard Navigation ─────────────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeImageModal();
        if (document.getElementById('imageModal').style.display === 'flex') {
            if (e.key === 'ArrowLeft')  prevModalImage();
            if (e.key === 'ArrowRight') nextModalImage();
        }
    });


    // ── Review Form Toggle ──────────────────────────────────────────────────────
    function toggleReviewForm() {
        const summary = document.getElementById('reviewSummary');
        const form    = document.getElementById('reviewForm');
        if (form.classList.contains('d-none')) {
            summary.classList.add('d-none');
            form.classList.remove('d-none');
        } else {
            form.classList.add('d-none');
            summary.classList.remove('d-none');
        }
    }


    // ── Star Rating ─────────────────────────────────────────────────────────────
    function setRating(val) {
        document.querySelectorAll('.star-rating i').forEach((star, index) => {
            star.classList.toggle('fa-solid',  index < val);
            star.classList.toggle('fa-regular', index >= val);
        });
    }


    // ── Add to Cart ─────────────────────────────────────────────────────────────
    function handleAddToCart(productId) {
        const btn = document.getElementById('addToCartBtn');
        btn.classList.add('loading');

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                product_id: productId,
                quantity: 1,
            },
            success: function (response) {
                btn.classList.remove('loading');

                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    html: `<p class="mb-0"><strong>{{ $product->name }}</strong> has been added to your cart.</p>`,
                    confirmButtonText: 'View Cart',
                    showCancelButton: true,
                    cancelButtonText: 'Continue Shopping',
                    confirmButtonColor: '#8b1e2d',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        popup:   'rounded-3 shadow',
                        title:   'fs-5 fw-bold',
                    },
                    timer: 5000,
                    timerProgressBar: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('cart.index') }}";
                    }
                });
            },
            error: function (xhr) {
                btn.classList.remove('loading');

                // If unauthenticated, redirect to login
                if (xhr.status === 401) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please Login',
                        text: 'You need to be logged in to add items to your cart.',
                        confirmButtonText: 'Login',
                        confirmButtonColor: '#8b1e2d',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: xhr.responseJSON?.message ?? 'Something went wrong. Please try again.',
                    confirmButtonColor: '#8b1e2d',
                });
            },
        });
    }


    // ── Image Zoom & Pan ────────────────────────────────────────────────────────
    let scale      = 1;
    let translateX = 0;
    let translateY = 0;
    let isDragging = false;
    let startX     = 0;
    let startY     = 0;
    let modalImg   = null;

    document.addEventListener('DOMContentLoaded', function () {
        modalImg = document.getElementById('modalMainImage');

        function applyTransform() {
            if (!modalImg) return;
            modalImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
            modalImg.style.cursor    = scale > 1 ? (isDragging ? 'grabbing' : 'grab') : 'default';
        }

        window.zoomIn = function () {
            if (!modalImg) return;
            scale = Math.min(scale + 0.25, 4);
            applyTransform();
        };

        window.zoomOut = function () {
            if (!modalImg) return;
            scale = Math.max(scale - 0.25, 1);
            if (scale === 1) { translateX = 0; translateY = 0; }
            applyTransform();
        };

        window.resetZoom = function () {
            if (!modalImg) return;
            scale      = 1;
            translateX = 0;
            translateY = 0;
            applyTransform();
        };

        modalImg.addEventListener('mousedown', function (e) {
            if (scale <= 1) return;
            isDragging = true;
            startX     = e.clientX;
            startY     = e.clientY;
            e.preventDefault();
            applyTransform();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging || scale <= 1) return;
            translateX += e.clientX - startX;
            translateY += e.clientY - startY;
            startX      = e.clientX;
            startY      = e.clientY;
            applyTransform();
        });

        document.addEventListener('mouseup', function () {
            if (!modalImg) return;
            isDragging = false;
            applyTransform();
        });

        modalImg.addEventListener('dragstart', function (e) {
            e.preventDefault();
        });

        applyTransform();
    });
</script>
@endpush