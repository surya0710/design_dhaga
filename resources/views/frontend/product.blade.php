@extends('frontend.layouts.app')

@section('title', $product->meta_title ?? $product->name)
@section('meta_description', $product->meta_description ?? '')
@section('meta_keywords', $product->meta_keywords ?? '')
@section('og_title', $product->meta_title ?? $product->name)
@section('og_description', $product->meta_description ?? '')
@section('og_image', asset('storage/' . $product->image))

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .wishlist-btn.active i {
        font-weight: 900;
    }

    .delivery-result-box {
        border: 1px solid #ead5d8;
        background: #fff8f8;
        border-radius: 8px;
    }

    .delivery-result-box .label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .delivery-result-box .value {
        font-size: 14px;
        color: #212529;
        font-weight: 600;
    }

    .delivery-result-box .status-success {
        color: #198754;
        font-weight: 700;
    }

    .delivery-result-box .status-fail {
        color: #dc3545;
        font-weight: 700;
    }

    .delivery-loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .wishlist-btn .fa-regular{
        color:#000!important;
    }
</style>
@endpush

@section('content')
@php
    $gallery    = $product->galleryImages;
    $mainImage  = $product->image;
    $productID  = $product->id;
@endphp
<div class="container-fluid">
    <div class="px-2 px-md-5 mt-3">
        <div class="row g-4 align-items-stretch flex-column flex-lg-row">

            <div class="col-12 col-lg-6">
                <div class="d-none d-lg-block position-relative">
                    <div class="d-flex gap-3">
                        <div class="d-flex flex-column gap-2 overflow-hidden" style="max-height: 700px; scrollbar-width: thin;">

                            <img src="{{ asset('storage/' . $product->image) }}" class="desktop-thumb border-2 border-danger cursor-pointer" style="width: 80px;" 
                            onclick="setDesktopImage(this)" />

                            @foreach ($gallery as $img)
                                <img src="{{ asset('storage/' . $img->image) }}" class="desktop-thumb cursor-pointer" style="width: 80px; opacity: 0.6;" onclick="setDesktopImage(this)" />
                            @endforeach
                        </div>

                        <div class="carousel-container position-relative overflow-hidden flex-grow-1">
                            <img id="desktopMainImage" src="{{ asset('storage/' . $product->image) }}" class="cursor-pointer w-100" style="object-fit: contain; display: block; max-height: 700px;"
                            alt="{{ $product->name }}" onclick="openImageModal(this.src)" />

                            <button class="btn btn-light rounded-circle position-absolute start-0 top-50 translate-middle-y ms-2 shadow"
                            style="z-index: 10; width: 45px; height: 45px;" onclick="prevDesktopImage()">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>

                            <button class="btn btn-light rounded-circle position-absolute end-0 top-50 translate-middle-y me-2 shadow" style="z-index: 10; width: 45px; height: 45px;"
                            onclick="nextDesktopImage()">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-lg-none">
                    <div class="mb-2">
                        <div class="d-flex align-items-start">
                            <h2 class="mb-1 mt-0">{{ $product->name }}</h2>
                            <button type="button" class="btn {{ $isInWishlist ? 'bg-dark-grey' : '' }} rounded-circle d-flex align-items-center justify-content-center wishlist-btn {{ $isInWishlist ? 'active' : '' }} mt-2" style="border:1px solid #000;"
                                data-product-id="{{ $product->id }}" data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}" aria-label="Toggle wishlist">
                                <i class="{{ $isInWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart fa-lg"></i>
                            </button>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="text-warning review-trigger" style="cursor:pointer;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($averageRating >= $i)
                                        <i class="fa-solid fa-star"></i>
                                    @else
                                        <i class="fa-regular fa-star review-star" data-value="{{ $i }}"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="small text-muted review-count" style="cursor:pointer;">
                                {{ $averageRating }} ({{ $product->reviews->count() }} reviews)
                            </span>
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
                        <div class="position-relative" style="overflow: hidden;">
                            <img id="mobileMainImage" src="{{ asset('storage/' . $product->image) }}" class="cursor-pointer w-100" alt="{{ $product->name }}" onclick="openImageModal(this.src)" />

                            <button class="btn btn-light rounded-circle position-absolute start-0 top-50 translate-middle-y shadow" style="z-index: 10; width: 35px; height: 35px; left: 4px;" 
                            onclick="prevMobileImage()">
                                <i class="fa-solid fa-chevron-left" style="font-size: 12px;"></i>
                            </button>

                            <button class="btn btn-light rounded-circle position-absolute end-0 top-50 translate-middle-y shadow" style="z-index: 10; width: 35px; height: 35px; right: 4px;"
                            onclick="nextMobileImage()">
                                <i class="fa-solid fa-chevron-right" style="font-size: 12px;"></i>
                            </button>
                        </div>

                        <div class="d-flex gap-2 overflow-auto pb-1" style="scrollbar-width: thin;">
                            <img src="{{ asset('storage/' . $product->image) }}" class="border border-2 border-danger mobile-thumb" style="width: 70px;" onclick="changeImage(this)"
                            ondblclick="openImageModal(this.src)" />

                            @foreach ($gallery as $img)
                                <img src="{{ asset('storage/' . $img->image) }}" class="mobile-thumb" style="width: 70px; opacity: 0.6;"
                                onclick="changeImage(this)" ondblclick="openImageModal(this.src)" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="sticky-md-top h-100">
                    <div class="d-flex justify-content-between align-items-start d-none d-lg-flex">
                        <div>
                            <h1 class="h3 fw-bold mb-0 mt-0" style="font-size: 25px; line-height: 1;">
                                {{ $product->name }}
                            </h1>

                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div class="text-warning review-trigger" style="cursor:pointer;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($averageRating >= $i)
                                            <i class="fa-solid fa-star"></i>
                                        @else
                                            <i class="fa-regular fa-star review-star" data-value="{{ $i }}"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="small text-muted review-count" style="cursor:pointer;">
                                    {{ $averageRating }} ({{ $product->reviews->count() }} reviews)
                                </span>
                            </div>
                        </div>

                        <button type="button" class="btn {{ $isInWishlist ? 'bg-dark-grey' : '' }} rounded-circle d-flex align-items-center justify-content-center wishlist-btn {{ $isInWishlist ? 'active' : '' }} mt-2" style="border:1px solid #000;"
                                data-product-id="{{ $product->id }}" data-in-wishlist="{{ $isInWishlist ? '1' : '0' }}" aria-label="Toggle wishlist">
                                <i class="{{ $isInWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart fa-lg"></i>
                        </button>
                    </div>

                    <p class="text-black mb-0 d-none d-lg-block">
                        {{ $product->short_description }}
                    </p>

                    <div class="h4 mb-2 d-none d-lg-block price">
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

                    @if ($product->type == 1 && $country == "India")
                        <button id="addToCartBtn" class="btn bg-maroon text-white w-100 py-3 fw-bold btn-add-to-cart" onclick="handleAddToCart({{ $product->id }})">
                            <span class="btn-text">
                                Add To Cart &nbsp;|&nbsp; ₹ <span id="total">{{ $product->sale_price ?? $product->regular_price }}</span>
                            </span>
                            <span class="btn-spinner d-none">
                                <span class="spinner-ring"></span>
                                Adding...
                            </span>
                        </button>
                    @else
                        <a class="btn bg-maroon text-white w-100 py-3 fw-bold"
                           href="https://api.whatsapp.com/send/?phone=919671941303&text={{ urlencode('Hi! I am interested in this product: ' . url()->current()) }}">
                            Request To Purchase
                        </a>
                    @endif

                    <div class="row g-2 mt-2 p-1 rounded bg-body-secondary text-center">
                        @foreach($product->icons as $icon)
                        <div class="col-4">
                            <svg width="40" height="40">
                                <use xlink:href="{{ asset('storage/' . $icon->image) }}"></use>
                            </svg>
                            <p class="text-black">{{ $icon->text }}</p>
                        </div>
                        @endforeach
                    </div>

                    <section class="features-box d-block d-lg-none" style="padding: 15px 0 !important;">
                        <div class="container">
                            <div class="row feature-items">
                                <div class="feature-item col">
                                    <img src="{{ asset('frontend_assets/images/easy-delivery-process.svg') }}" class="mobile-icons">
                                    <h4>Easy Delivery</h4>
                                </div>
                                <div class="feature-item col">
                                    <img src="{{ asset('frontend_assets/images/exquisite-product.svg') }}" class="mobile-icons">
                                    <h4>Exquisite Product</h4>
                                </div>
                                <div class="feature-item col">
                                    <img src="{{ asset('frontend_assets/images/intricate-design.svg') }}" class="mobile-icons">
                                    <h4>Intricate Design</h4>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="mb-2 mt-1 fw-bold heading-size">
                        <i class="fa-solid fa-truck me-2"></i>Check Delivery Time
                    </div>

                    <div id="deliveryCheckWrapper" data-product-id="{{ $product->id }}" data-product-weight="{{ $product->weight ?? 0.5 }}">

                        <div id="deliveryInputSection">
                            <div class="input-group">
                                <input type="text" id="deliveryPincode" class="form-control bg-light-pink p-3 border-0" placeholder="Enter pincode" maxlength="6" inputmode="numeric" />
                                <button class="btn btn-white border fw-bold" type="button" id="checkDeliveryBtn"> 
                                    Check
                                </button>
                            </div>
                            <small id="deliveryError" class="text-danger d-none mt-2"></small>
                        </div>

                        <div id="deliverySuccessSection" class="d-none mt-2">
                            <div class="delivery-result-box p-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div class="d-flex justify-content-between flex-column gap-2">
                                        <div class="d-flex">
                                            <div class="label">
                                                <i class="fa-solid fa-location-dot me-1"></i>
                                            </div>
                                            <div class="label">DELIVER TO:</div> &nbsp;
                                            <div class="value" id="deliveryPincodeValue"></div>
                                        </div>

                                        <div class="d-flex">
                                            <div class="label confirm-delivery-label text-success">We deliver to your zipcode.</div> &nbsp;
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="changeDeliveryPincodeBtn">
                                        Change
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="deliveryUnavailableSection" class="d-none mt-2">
                            <div class="delivery-result-box p-3">
                                <div class="status-fail mb-2">
                                    <i class="fa-solid fa-circle-xmark me-1"></i>
                                    Delivery not available
                                </div>
                                <div class="small text-muted mb-3">
                                    Sorry, this pincode is not serviceable right now.
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="retryDeliveryBtn">
                                    Try another pincode
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

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

    <div class="w-100 bg-white pt-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 px-2 px-md-5">
                    <ul class="nav nav-underline border-bottom border-2 mb-0 gap-4 d-none d-lg-flex justify-content-between"
                        role="tablist">

                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-dark py-4 fs-5" id="desc-tab" data-bs-toggle="tab" data-bs-target="#descTab" type="button" role="tab">
                                <i class="fa-solid fa-book me-2 text-maroon"></i>Product Description
                            </button>
                        </li>

                        @if ($product->hand_painted_details)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-dark py-4 fs-5" id="handPainted-tab" data-bs-toggle="tab" data-bs-target="#handPaintedTab" type="button" role="tab">
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

                    <div class="tab-content pt-2 pb-2 d-none d-lg-block">
                        <div class="tab-pane fade show active" id="descTab" role="tabpanel">
                            <div class="row g-4 align-items-stretch flex-column flex-lg-row">
                                <div class="col-lg-12">
                                    <p class="text-dark lh-lg mb-4 fs-6">{!! $product->description !!}</p>
                                </div>
                            </div>
                        </div>

                        @if ($product->hand_painted_details)
                            <div class="tab-pane fade" id="handPaintedTab" role="tabpanel">
                                <div class="row g-4 align-items-stretch flex-column flex-lg-row">
                                    <div class="col-lg-12">
                                        <p class="text-dark lh-lg mb-4 fs-6">
                                            {!! $product->hand_painted_details !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($product->care_instructions)
                            <div class="tab-pane fade" id="careTab" role="tabpanel">
                                <div class="row g-4 align-items-stretch flex-column flex-lg-row">
                                    <div class="col-lg-12">
                                        <p class="text-dark lh-lg mb-4 fs-6">
                                            {!! $product->care_instructions !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($product->manufacturing_details)
                            <div class="tab-pane fade" id="manufacturingTab" role="tabpanel">
                                <div class="row g-4 align-items-stretch flex-column flex-lg-row">
                                    <div class="col-lg-12">
                                        <p class="text-dark lh-lg mb-4 fs-6">
                                            {!! $product->manufacturing_details !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-block d-lg-none py-2">
                        <div class="accordion accordion-flush" id="productAccordion">
                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header" id="headingDesc">
                                    <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDesc"
                                    aria-expanded="false" aria-controls="collapseDesc">
                                        <i class="fa-solid fa-book me-2 text-maroon"></i>Product Description
                                    </button>
                                </h2>
                                <div id="collapseDesc" class="accordion-collapse collapse" aria-labelledby="headingDesc" data-bs-parent="#productAccordion">
                                    <div class="accordion-body px-0 pb-4">
                                        <p class="text-dark lh-lg mb-0 fs-6">{!! $product->description !!}</p>
                                    </div>
                                </div>
                            </div>

                            @if ($product->hand_painted_details)
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="headingHandPainted">
                                        <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapseHandPainted" aria-expanded="false" aria-controls="collapseHandPainted">
                                            <i class="fa-solid fa-palette me-2 text-maroon"></i>Hand Painted Details
                                        </button>
                                    </h2>
                                    <div id="collapseHandPainted" class="accordion-collapse collapse" aria-labelledby="headingHandPainted" data-bs-parent="#productAccordion">
                                        <div class="accordion-body px-0 pb-4">
                                            <p class="text-dark lh-lg mb-0 fs-6">
                                                {!! $product->hand_painted_details !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($product->care_instructions)
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="headingCare">
                                        <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCare"
                                        aria-expanded="false" aria-controls="collapseCare">
                                            <i class="fa-solid fa-heart-pulse me-2 text-maroon"></i>Care Instructions
                                        </button>
                                    </h2>
                                    <div id="collapseCare" class="accordion-collapse collapse" aria-labelledby="headingCare" data-bs-parent="#productAccordion">
                                        <div class="accordion-body px-0 pb-4">
                                            <p class="text-dark lh-lg mb-0 fs-6">
                                                {!! $product->care_instructions !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($product->manufacturing_details)
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="headingManufacturing">
                                        <button class="accordion-button collapsed fw-bold fs-6 px-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseManufacturing"
                                        aria-expanded="false" aria-controls="collapseManufacturing">
                                            <i class="fa-solid fa-tools me-2 text-maroon"></i>Manufacturing Details
                                        </button>
                                    </h2>
                                    <div id="collapseManufacturing"
                                         class="accordion-collapse collapse"
                                         aria-labelledby="headingManufacturing"
                                         data-bs-parent="#productAccordion">
                                        <div class="accordion-body px-0 pb-4">
                                            <p class="text-dark lh-lg mb-0 fs-6">
                                                {!! $product->manufacturing_details !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

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
                        <div class="col-lg-4 col-md-4">
                            @if ($artisan->image)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $artisan->image) }}" loading="lazy" class="w-100" alt="{{ $artisan->title ?? 'Artisan' }}" />
                                </div>
                            @endif

                            @if ($artisan->title)
                                <h3 class="h5 fw-bold border-bottom border-2 border-dark pb-2 mb-3 text-uppercase" style="letter-spacing: 1px;">
                                    {{ $artisan->title }}
                                </h3>
                            @endif

                            @if ($artisan->description)
                                <p class="artisan-description text-secondary lh-lg">
                                    {!! nl2br(e($artisan->description)) !!}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($product->square_banner)
        <section class="pt-2 bg-white pb-4">
            <div class="container-fluid px-2 px-md-5">
                <div class="row align-items-center g-4 g-lg-5">
                    <div class="col-12 d-lg-none mb-2">
                        <h2 class="fw-bold text-black my-0" style="line-height: 1.3;">
                            {{ $product->square_banner_title ?? '' }}
                        </h2>
                    </div>

                    <div class="col-lg-5">
                        <div class="style-comfort-img rounded-3 overflow-hidden">
                            <img src="{{ asset('storage/' . $product->square_banner) }}" loading="lazy" class="img-fluid w-100" alt="{{ $product->square_banner_title ?? $product->name }}"
                                 style="object-fit: cover;" />
                        </div>
                    </div>

                    <div class="col-lg-7 ps-lg-5">
                        @if ($product->square_banner_title)
                            <h2 class="h3 fw-bold mb-1 mt-0">
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

    <section class="pt-2 bg-white pb-4">
        <div class="container-fluid px-0 overflow-hidden">
            <div class="row position-relative px-3 m-0 align-items-center justify-content-center text-center">
                <div class="col-12 col-md-8 position-relative" style="z-index: 2;">
                    <h2 class="px-md-5 mt-0">Related Products</h2>
                </div>
            </div>
        </div>
        <div id="recentBlogsCarousel">
            <div class="owl-carousel">
                @foreach($relatedProducts as $product)
                @php $productUrl = getProductUrl($product); @endphp
                <div>
                    <a href="{{ $productUrl }}" class="text-decoration-none text-dark">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="ratio ratio-4x3">
                                <img src="{{ Storage::url($product->image) }}" loading="lazy" class="card-img-top object-fit-cover" alt="{{ $product->name }}" />
                            </div>
                            <div class="card-body">
                                <h5 class="mb-2 mt-0">{{ $product->name }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>

<div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; align-items: center;
            justify-content: center; flex-direction: column; padding: 20px;">

    <button onclick="closeImageModal()" style="position: absolute; top: 20px; right: 30px; background: none; border: none; color: white; font-size: 32px; cursor: pointer; z-index: 10000;">
        &times;
    </button>

    <div style="width: 100%; max-width: 600px; height: 60vh; position: relative; display: flex; align-items: center; justify-content: center; z-index: 9999; overflow: hidden;">

        <img id="modalMainImage" src="" style="width: 100%; height: 100%; object-fit: contain; cursor: grab; transition: transform 0.2s ease;" />

        <div style="position: absolute; bottom: 15px; right: 0; z-index: 10000; display: flex; gap: 6px;">
            <button onclick="zoomIn()" class="btn btn-light rounded-circle shadow" style="width: 40px; height: 40px;">+</button>
            <button onclick="zoomOut()" class="btn btn-light rounded-circle shadow" style="width: 40px; height: 40px;">−</button>
            <button onclick="resetZoom()" class="btn btn-light rounded-circle shadow" style="width: 40px; height: 40px;">
                <i class="fa fa-sync"></i>
            </button>
        </div>
    </div>

    <div style="width: 100%; max-width: 600px; display: flex; gap: 8px; margin-top: 20px; overflow-x: auto; padding-bottom: 8px; justify-content: center; z-index: 9999;">

        <img src="{{ asset('storage/' . $mainImage) }}" class="modal-thumb" style="width: 70px; height: 90px; object-fit: cover; flex-shrink: 0; border: 2px solid white; cursor: pointer; opacity: 1;"
             onclick="setModalImage(this)" />

        @foreach ($gallery as $img)
            <img src="{{ asset('storage/' . $img->image) }}" class="modal-thumb" style="width: 70px; height: 90px; object-fit: cover; flex-shrink: 0; cursor: pointer; opacity: 0.5;"
            onclick="setModalImage(this)" />
        @endforeach
    </div>
</div>

<div id="reviewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:99999; align-items:center; justify-content:center;">
    
    <div style="background:#fff; width:100%; max-width:500px; border-radius:10px; padding:20px; position:relative;">
        
        <button onclick="closeReviewModal()" style="position:absolute; top:10px; right:15px; border:none; background:none; font-size:22px;">&times;</button>

        <h4 class="mb-3">Add Review</h4>

        <form id="reviewForm">
            @csrf

            <input type="hidden" name="product_id" value="{{ $productID }}">
            <input type="hidden" name="rating" id="selectedRating">

            <!-- Stars -->
            <div class="mb-3">
                <div id="starSelect" class="text-warning" style="font-size:24px;">
                    <i class="fa-regular fa-star star-input" data-value="1"></i>
                    <i class="fa-regular fa-star star-input" data-value="2"></i>
                    <i class="fa-regular fa-star star-input" data-value="3"></i>
                    <i class="fa-regular fa-star star-input" data-value="4"></i>
                    <i class="fa-regular fa-star star-input" data-value="5"></i>
                </div>
            </div>

            <!-- Review -->
            <div class="mb-3">
                <textarea name="review" class="form-control" placeholder="Write your review..." required rows="5"></textarea>
            </div>

            <!-- Image -->
            <div class="mb-3">
                <input type="file" name="image" class="form-control">
            </div>

            <button type="submit" class="btn bg-maroon text-white w-100">Submit Review</button>
        </form>
    </div>
</div>

<div id="allReviewsModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:99999; align-items:center; justify-content:center; padding:1rem;">

    <div style="background:#fff; width:100%; max-width:560px; max-height:78vh; overflow-y:auto; border-radius:16px; position:relative; border:1px solid #e5e7eb; box-shadow:0 20px 60px rgba(0,0,0,0.15);">

        {{-- Sticky header --}}
        <div style="padding:1.25rem 1.5rem 1rem; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; background:#fff; z-index:10; border-radius:16px 16px 0 0;">
            <div>
                <p style="font-size:16px; font-weight:600; margin:0; color:#111;">Customer reviews</p>
                @if(!empty($allReviews))
                    @php
                        $avg = round($allReviews->avg('rating'), 1);
                        $count = $allReviews->count();
                    @endphp
                    <div style="display:flex; align-items:center; gap:6px; margin-top:4px;">
                        <span style="color:#F59E0B; font-size:13px;">
                            @for($i=1; $i<=5; $i++)
                                <i class="fa {{ $i <= round($avg) ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                            @endfor
                        </span>
                        <span style="font-size:13px; font-weight:600; color:#111;">{{ $avg }}</span>
                        <span style="font-size:12px; color:#6b7280;">· {{ $count }} {{ Str::plural('review', $count) }}</span>
                    </div>
                @endif
            </div>
            <button onclick="closeAllReviewsModal()" style="width:32px; height:32px; border-radius:50%; border:1px solid #e5e7eb; background:#f9fafb; color:#6b7280; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; line-height:1; transition:background 0.15s;">&#x2715;</button>
        </div>

        {{-- Reviews list --}}
        <div style="padding:0 1.5rem 1.5rem;">
            @if(!empty($allReviews))
                @foreach($allReviews as $review)
                    @php
                        $name = $review->user->name ?? 'User';
                        $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $name), 0, 2))));
                        $colors = [
                            ['bg'=>'#EEF2FF','text'=>'#4338CA'],
                            ['bg'=>'#E1F5EE','text'=>'#0F6E56'],
                            ['bg'=>'#FAECE7','text'=>'#993C1D'],
                            ['bg'=>'#FEF3C7','text'=>'#92400E'],
                            ['bg'=>'#FCE7F3','text'=>'#9D174D'],
                        ];
                        $color = $colors[$loop->index % count($colors)];
                    @endphp
                    <div style="padding:1rem 0; {{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
                        <div style="display:flex; align-items:flex-start; gap:12px;">

                            {{-- Avatar --}}
                            <div style="width:38px; height:38px; border-radius:50%; background:{{ $color['bg'] }}; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; color:{{ $color['text'] }}; flex-shrink:0;">
                                {{ $initials }}
                            </div>

                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;">
                                    <span style="font-size:14px; font-weight:600; color:#111;">{{ $name }}</span>
                                    <span style="font-size:12px; color:#9ca3af;">{{ $review->created_at->format('d M Y') }}</span>
                                </div>

                                {{-- Stars --}}
                                <div style="margin:4px 0 8px; color:#F59E0B; font-size:13px; letter-spacing:1px;">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fa {{ $i <= $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star" style="{{ $i > $review->rating ? 'color:#d1d5db;' : '' }}"></i>
                                    @endfor
                                </div>

                                <p style="font-size:14px; color:#374151; margin:0 0 10px; line-height:1.6;">{{ $review->review }}</p>

                                @if($review->image)
                                    <img src="{{ asset('storage/'.$review->image) }}" style="width:150px; height:150px; border-radius:8px; object-fit:cover; border:1px solid #e5e7eb;">
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align:center; padding:3rem 1rem; color:#9ca3af;">
                    <p style="font-size:15px; margin:0;">No reviews yet</p>
                </div>
            @endif
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
        $shareTitle = urlencode($product->title);
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
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('.artisan-description').each(function () {
        let lines = $(this).html().split('<br>');
        let newHtml = '<ul class="custom-check">';

        lines.forEach(line => {
            if (line.trim()) {
                newHtml += `<li>${line.replace(/✔️|✔/g, '').trim()}</li>`;
            }
        });

        newHtml += '</ul>';
        $(this).html(newHtml);
    });

    const imageUrls = [
        "{{ asset('storage/' . $product->image) }}",
        @foreach ($gallery as $img)
            "{{ asset('storage/' . $img->image) }}",
        @endforeach
    ];

    const wishlistConfig = {
        addUrl: "{{ route('wishlist.add') }}",
        removeUrl: "{{ route('wishlist.remove') }}",
        loginUrl: "",
        csrfToken: "{{ csrf_token() }}"
    };

    function handleAddToCart(productId) {
        const btn = document.getElementById('addToCartBtn');
        btn.classList.add('loading');

        $.ajax({
            url: @json(route('cart.add')),
            method: 'POST',
            data: {
                _token: @json(csrf_token()),
                product_id: productId,
                quantity: 1,
            },
            success: function () {
                btn.classList.remove('loading');

                Swal.fire({
                    iconHtml: '<i class="fa-regular fa-circle-check fa-2x"></i>',
                    title: 'Added to Cart!',
                    html: `<p class="mb-0"><strong>@json($product->name)</strong> has been added to your cart.</p>`,
                    confirmButtonText: 'View Cart',
                    showCancelButton: true,
                    cancelButtonText: 'Continue Shopping',
                    confirmButtonColor: '#8b1e2d',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        popup: 'rounded-3 shadow',
                        title: 'fs-5 fw-bold',
                    },
                    timer: 5000,
                    timerProgressBar: true,
                }).then((result) => {

                    // 👉 If user clicks "View Cart"
                    if (result.isConfirmed) {
                        window.location.href = @json(route('cart.index'));
                    }

                    // 👉 If popup closes due to timer
                    else if (result.dismiss === Swal.DismissReason.timer) {
                        location.reload();
                    }

                });
            },
            error: function (xhr) {
                btn.classList.remove('loading');

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
                            $("#loginModal").modal('toggle');
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

    const deliveryConfig = {
        endpoint: @json(route('pincode.serviceable')),
        csrfToken: @json(csrf_token()),
        storageKey: 'delivery_check_product_{{ $product->id }}'
    };

    function getDeliveryElements() {
        return {
            wrapper: document.getElementById('deliveryCheckWrapper'),
            inputSection: document.getElementById('deliveryInputSection'),
            successSection: document.getElementById('deliverySuccessSection'),
            unavailableSection: document.getElementById('deliveryUnavailableSection'),
            pincodeInput: document.getElementById('deliveryPincode'),
            checkBtn: document.getElementById('checkDeliveryBtn'),
            errorBox: document.getElementById('deliveryError'),
            pincodeValue: document.getElementById('deliveryPincodeValue'),
            confirmLabel: document.getElementById('confirm-delivery-label'),
            daysValue: document.getElementById('deliveryDaysValue'),
            courierValue: document.getElementById('deliveryCourierValue'),
            courierRow: document.getElementById('deliveryCourierRow'),
            changeBtn: document.getElementById('changeDeliveryPincodeBtn'),
            retryBtn: document.getElementById('retryDeliveryBtn')
        };
    }

    function showDeliveryError(message) {
        const els = getDeliveryElements();
        els.errorBox.textContent = message;
        els.errorBox.classList.remove('d-none');
    }

    function hideDeliveryError() {
        const els = getDeliveryElements();
        els.errorBox.textContent = '';
        els.errorBox.classList.add('d-none');
    }

    function resetDeliverySections() {
        const els = getDeliveryElements();
        els.inputSection.classList.remove('d-none');
        els.successSection.classList.add('d-none');
        els.unavailableSection.classList.add('d-none');
        hideDeliveryError();
    }

    function getNumericDeliveryDays(value) {
        if (value === null || value === undefined || value === '') {
            return null;
        }

        if (!isNaN(value)) {
            return parseInt(value, 10);
        }

        const match = String(value).match(/\d+/);
        return match ? parseInt(match[0], 10) : null;
    }

    function formatEstimatedDate(days) {
        if (days === null || isNaN(days)) {
            return 'To be confirmed';
        }

        const date = new Date();
        date.setDate(date.getDate() + Number(days));

        return new Intl.DateTimeFormat('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }).format(date);
    }

    function renderDeliverySuccess(data) {
        const els = getDeliveryElements();
        const bestOption = data?.best_option || null;
        const pincode = data?.delivery_postcode || els.pincodeInput.value || '';
        const rawDays = bestOption?.estimated_delivery_days ?? null;
        const numericDays = getNumericDeliveryDays(rawDays);
        const formattedDate = formatEstimatedDate(numericDays);
        const courierName = bestOption?.courier_name || '';

        els.pincodeValue.textContent = pincode;

        els.inputSection.classList.add('d-none');
        els.unavailableSection.classList.add('d-none');
        els.successSection.classList.remove('d-none');
        els.confirmLabel.classList.add("text-success");

        localStorage.setItem(deliveryConfig.storageKey, JSON.stringify({
            status: 'success',
            response: data,
            pincode: pincode
        }));
    }

    function renderDeliveryUnavailable(pincode) {
        const els = getDeliveryElements();

        els.inputSection.classList.add('d-none');
        els.successSection.classList.add('d-none');
        els.unavailableSection.classList.remove('d-none');

        localStorage.setItem(deliveryConfig.storageKey, JSON.stringify({
            status: 'unavailable',
            pincode: pincode
        }));
    }

    function restoreSavedDeliveryState() {
        const els = getDeliveryElements();
        const saved = localStorage.getItem(deliveryConfig.storageKey);

        if (!saved) {
            return;
        }

        try {
            const parsed = JSON.parse(saved);

            if (parsed?.pincode) {
                els.pincodeInput.value = parsed.pincode;
            }

            if (parsed?.status === 'success' && parsed?.response) {
                renderDeliverySuccess(parsed.response);
            } else if (parsed?.status === 'unavailable') {
                renderDeliveryUnavailable(parsed.pincode || '');
            }
        } catch (e) {
            localStorage.removeItem(deliveryConfig.storageKey);
        }
    }

    function setDeliveryLoading(isLoading) {
        const els = getDeliveryElements();

        if (isLoading) {
            els.checkBtn.dataset.originalText = els.checkBtn.innerHTML;
            els.checkBtn.innerHTML = 'Checking...';
            els.checkBtn.disabled = true;
            els.checkBtn.classList.add('delivery-loading');
            els.pincodeInput.disabled = true;
        } else {
            els.checkBtn.innerHTML = els.checkBtn.dataset.originalText || 'Check';
            els.checkBtn.disabled = false;
            els.checkBtn.classList.remove('delivery-loading');
            els.pincodeInput.disabled = false;
        }
    }

    function checkDeliveryAvailability() {
        const els = getDeliveryElements();
        const wrapper = els.wrapper;
        const deliveryPincode = (els.pincodeInput.value || '').trim();
        const productWeight = wrapper.dataset.productWeight || '0.5';

        hideDeliveryError();

        if (!/^\d{6}$/.test(deliveryPincode)) {
            showDeliveryError('Please enter a valid 6-digit pincode.');
            return;
        }

        setDeliveryLoading(true);

        $.ajax({
            url: deliveryConfig.endpoint,
            method: 'POST',
            data: {
                _token: deliveryConfig.csrfToken,
                delivery_postcode: deliveryPincode,
                weight: productWeight,
                cod: 0
            },
            success: function (response) {
                setDeliveryLoading(false);

                const result = response?.data || {};
                const serviceable = !!result.serviceable;

                if (serviceable) {
                    renderDeliverySuccess(result);
                } else {
                    renderDeliveryUnavailable(deliveryPincode);
                }
            },
            error: function (xhr) {
                setDeliveryLoading(false);

                const message = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Unable to check delivery for this pincode right now.';
                showDeliveryError(message);
            }
        });
    }

    $(document).ready(function () {
        const els = getDeliveryElements();

        restoreSavedDeliveryState();

        $('#checkDeliveryBtn').on('click', function () {
            checkDeliveryAvailability();
        });

        $('#deliveryPincode').on('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
            hideDeliveryError();
        });

        $('#deliveryPincode').on('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkDeliveryAvailability();
            }
        });

        $('#changeDeliveryPincodeBtn, #retryDeliveryBtn').on('click', function () {
            localStorage.removeItem(deliveryConfig.storageKey);
            resetDeliverySections();
            els.pincodeInput.focus();
        });
    });
</script>
<script src="{{ asset('frontend_assets/js/product.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
    $(document).ready(function() {
        $("#recentBlogsCarousel .owl-carousel").owlCarousel({
            loop: true,
            margin: 12,
            nav: true,
            dots: true,
            autoplay: true,
            smartSpeed: 2000,
            responsive: {
                0: {
                    items: 1   // mobile
                },
                576: {
                    items: 2   // tablet
                },
                992: {
                    items: 2   // up to lg breakpoint
                },
                1200:{
                    items: 4
                }
            }
        });
    });

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

    // Open modal on star click
    document.querySelectorAll('.review-trigger').forEach(el => {
        el.addEventListener('click', function () {

            @if(!auth()->check())
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: 'Please login to add review'
                });
                return;
            @endif

            document.getElementById('reviewModal').style.display = 'flex';
        });
    });

    // Close modal
    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
    }

    // Star selection
    document.querySelectorAll('.star-input').forEach(star => {
        star.addEventListener('click', function () {
            let value = this.dataset.value;

            document.getElementById('selectedRating').value = value;

            document.querySelectorAll('.star-input').forEach(s => {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular');
            });

            for (let i = 0; i < value; i++) {
                document.querySelectorAll('.star-input')[i].classList.remove('fa-regular');
                document.querySelectorAll('.star-input')[i].classList.add('fa-solid');
            }
        });
    });

    document.getElementById('reviewForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch("{{ route('review.store') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {

                Swal.fire({
                    iconHtml: '<i class="fa-regular fa-circle-check fa-2x"></i>',
                    title: 'Review Added!',
                    text: 'Your review has been submitted successfully.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#8b1e2d',
                    customClass: {
                        popup: 'rounded-3 shadow',
                        title: 'fs-5 fw-bold',
                    }
                }).then(() => {
                    location.reload(); // ✅ refresh after success
                });

                closeReviewModal();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Something went wrong',
                    confirmButtonColor: '#8b1e2d',
                });
            }
        });
    });

    // Open all reviews modal
    document.querySelectorAll('.review-count').forEach(el => {
        el.addEventListener('click', function () {
            document.getElementById('allReviewsModal').style.display = 'flex';
        });
    });

    // Close modal
    function closeAllReviewsModal() {
        document.getElementById('allReviewsModal').style.display = 'none';
    }
</script>
@endpush