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
    @media (max-width: 768px) {
        .breadcrumb {
            justify-content: start!important;
        }
    }
</style>
@endpush
@php
    $gallery    = $product->galleryImages;
    $mainImage  = $product->image;
    $productID  = $product->id;
    $activeVariants = $product->activeVariants->values();
    $hasVariants = $activeVariants->isNotEmpty();
    $initialVariant = $activeVariants->sortBy('price')->first();
    $displayPrice = $initialVariant?->price ?? ($product->sale_price ?? $product->regular_price);
    $variantSizes = $activeVariants->pluck('size')->filter()->unique()->values();
    $variantFabrics = $activeVariants->pluck('fabric_type')->filter()->unique()->values();
    $variantOptions = $activeVariants->map(function ($variant) {
        return [
            'id' => $variant->id,
            'size' => $variant->size,
            'fabric_type' => $variant->fabric_type,
            'sku' => $variant->sku,
            'price' => (float) $variant->price,
            'regular_price' => (float) $variant->regular_price,
            'quantity' => (int) $variant->quantity,
        ];
    })->values();
@endphp
@section('schema')

@php
    $schemaPrice = $hasVariants
        ? ($initialVariant?->price ?? 0)
        : ($product->sale_price ?: $product->regular_price);

    $schemaSku = $hasVariants
        ? ($initialVariant?->sku ?? $product->sku)
        : $product->sku;

    $schemaAvailability = ($product->quantity > 0)
        ? 'https://schema.org/InStock'
        : 'https://schema.org/OutOfStock';

    $categoryName = $product->category->name ?? '';
@endphp

<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "Product",

    "name": {!! json_encode($product->name) !!},

    "description": {!! json_encode(strip_tags($product->short_description ?? $product->description)) !!},

    "image": [
        "{{ asset('storage/'.$product->image) }}"
    ],

    "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand ?? 'Design Dhaga' }}"
    },

    "sku": "{{ $schemaSku }}",

    @if(!empty($product->gtin))
    "gtin": "{{ $product->gtin }}",
    @endif

    @if(!empty($product->mpn))
    "mpn": "{{ $product->mpn }}",
    @endif

    @if(!empty($categoryName))
    "category": "{{ $categoryName }}",
    @endif

    @if(!empty($product->color))
    "color": "{{ $product->color }}",
    @endif

    @if(!empty($product->fabric_type))
    "material": "{{ $product->fabric_type }}",
    @endif

    "offers": {
        "@type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "INR",
        "price": "{{ $schemaPrice }}",
        "availability": "{{ $schemaAvailability }}",
        "itemCondition": "https://schema.org/NewCondition"
    }

    @if($product->reviews->count() > 0)
    ,

    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ number_format($averageRating,1) }}",
        "bestRating": "5",
        "worstRating": "1",
        "reviewCount": "{{ $product->reviews->count() }}"
    },

    "review": [
        @foreach($product->reviews->take(5) as $review)
        {
            "@type": "Review",
            "author": {
                "@type": "Person",
                "name": "{{ $review->user->name ?? 'Customer' }}"
            },
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "{{ $review->rating }}",
                "bestRating": "5"
            },
            "reviewBody": {!! json_encode($review->review ?? '') !!}
        }@if(!$loop->last),@endif
        @endforeach
    ]
    @endif

    @if($hasVariants && $activeVariants->count())
    ,
    "hasVariant": [
        @foreach($activeVariants as $variant)
        {
            "@type": "Product",
            "name": {!! json_encode($product->name) !!},
            "sku": "{{ $variant->sku }}",

            @if($variant->size)
            "size": "{{ $variant->size }}",
            @endif

            @if($variant->fabric_type)
            "material": "{{ $variant->fabric_type }}",
            @endif

            "offers": {
                "@type": "Offer",
                "priceCurrency": "INR",
                "price": "{{ $variant->price }}",
                "availability": "{{ $variant->quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
    @endif
}
</script>

@endsection

@section('content')
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
                        <div class="d-flex align-items-start justify-content-between">
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
                            @if ($hasVariants)
                                <span class="fw-bold text-black">₹ <span class="variant-price-current">{{ number_format($displayPrice, 0) }}</span></span>
                                <span class="text-decoration-line-through text-muted small ms-2">
                                    ₹ {{ number_format($product->regular_price, 0) }}
                                </span>
                                <span class="text-maroon ms-2 fw-semibold">
                                    Save {{ number_format((1 - ($product->sale_price / $product->regular_price)) * 100, 0) }}%
                                </span>
                            @elseif ($product->sale_price)
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
                        @if ($hasVariants)
                            <span class="fw-bold text-black">₹ <span class="variant-price-current">{{ number_format($displayPrice, 0) }}</span></span>
                            <span class="text-decoration-line-through text-muted small ms-2">
                                ₹ {{ number_format($product->regular_price, 0) }}
                            </span>
                            <span class="text-maroon ms-2 fw-semibold">
                                Save {{ number_format((1 - ($product->sale_price / $product->regular_price)) * 100, 0) }}%
                            </span>
                        @elseif ($product->sale_price)
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

                    @if ($hasVariants)
                        <div class="variant-picker mb-3" data-has-variants="1">
                            @if ($variantFabrics->isNotEmpty())
                                <div class="mb-2">
                                    <div class="fw-bold small text-uppercase text-muted mb-1">Fabric Type</div>
                                    <div class="d-flex flex-wrap gap-2" id="fabricOptions">
                                        @foreach($variantFabrics as $fabric)
                                            <button type="button" class="btn btn-sm btn-outline-dark variant-option" data-option-type="fabric" data-value="{{ $fabric }}">{{ $fabric }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($variantSizes->isNotEmpty())
                                <div class="mb-2">
                                    <div class="fw-bold small text-uppercase text-muted mb-1">Size</div>
                                    <div class="d-flex flex-wrap gap-2" id="sizeOptions">
                                        @foreach($variantSizes as $size)
                                            <button type="button" class="btn btn-sm btn-outline-dark variant-option" data-option-type="size" data-value="{{ $size }}">{{ $size }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($product->type == 1 && $country == "India")
                        <button id="addToCartBtn" class="btn bg-maroon text-white w-100 py-3 fw-bold btn-add-to-cart" onclick="handleAddToCart({{ $product->id }})">
                            <span class="btn-text">
                                Add To Cart &nbsp;|&nbsp; ₹ <span id="total">{{ number_format($displayPrice, 0, '.', '') }}</span>
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
                            <img src="{{ asset('storage/' . $icon->image) }}" width="40" height="40">
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
                                <p type="button" class="read-more-btn fw-bold d-none" data-target="faqAnswer{{ $key }}" data-expanded="0">
                                    <span class="read-more-span">•••</span>
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
                        <div class="card border-0 h-100">
                            <div class="ratio ratio-4x3">
                                <img src="{{ Storage::url($product->image) }}" loading="lazy" class="card-img-top object-fit-cover" alt="{{ $product->name }}" />
                            </div>
                            <div class="card-body">
                                <p class="mt-2 mb-0 text-left">{{ $product->name }}</p>
                                @include('frontend.partials.product-price', ['product' => $product])
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

        <img id="modalMainImage" style="width:100%;height:100%;object-fit:contain; cursor:grab; transition:transform 0.2s ease; touch-action:none;">

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

        <h4 class="mb-3" id="reviewModalTitle">Add Review</h4>

        <form id="reviewForm">
            @csrf

            <input type="hidden" name="review_id" id="review_id" value="">
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
            <div class="mb-3" id="currentReviewImageWrap" style="display:none;">
                <img id="currentReviewImage" src="" alt="Current review image" style="width:100px; height:100px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb;">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                    <label class="form-check-label small" for="remove_image">Remove current image</label>
                </div>
            </div>
            <div class="mb-3">
                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp,image/jpg">
            </div>

            <button type="submit" class="btn bg-maroon text-white w-100" id="reviewSubmitBtn">Submit Review</button>
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

                                @if(auth()->check() && auth()->id() === $review->user_id)
                                    <div style="display:flex; gap:8px; margin-top:10px;">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary edit-review-btn"
                                            data-review-id="{{ $review->id }}"
                                            data-rating="{{ $review->rating }}"
                                            data-review="{{ htmlspecialchars($review->review, ENT_QUOTES) }}"
                                            data-image="{{ $review->image ? asset('storage/'.$review->image) : '' }}">
                                            Edit
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger delete-review-btn"
                                            data-review-id="{{ $review->id }}">
                                            Delete
                                        </button>
                                    </div>
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
        "{{ asset('storage/' . $mainImage) }}",
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

    const productVariants = @json($variantOptions);
    const hasFabricOptions = productVariants.some(function (variant) {
        return !!variant.fabric_type;
    });
    const hasSizeOptions = productVariants.some(function (variant) {
        return !!variant.size;
    });
    let selectedSize = productVariants[0]?.size || '';
    let selectedFabric = productVariants[0]?.fabric_type || '';
    let selectedVariant = productVariants[0] || null;

    function formatPrice(value) {
        return new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(Number(value || 0));
    }

    function variantMatches(variant, size, fabric) {
        const sizeMatches = !variant.size || variant.size === size;
        const fabricMatches = !variant.fabric_type || variant.fabric_type === fabric;
        return sizeMatches && fabricMatches;
    }

    function findSelectedVariant() {
        return productVariants.find(function (variant) {
            return variantMatches(variant, selectedSize, selectedFabric);
        }) || null;
    }

    function firstVariantForFabric(fabric) {
        return productVariants.find(function (variant) {
            return !variant.fabric_type || variant.fabric_type === fabric;
        });
    }

    function isOptionAvailable(type, value) {
        if (type === 'fabric') {
            return productVariants.some(function (variant) {
                return variant.fabric_type === value;
            });
        }

        return productVariants.some(function (variant) {
            if (variant.size !== value) {
                return false;
            }

            return !hasFabricOptions || !variant.fabric_type || variant.fabric_type === selectedFabric;
        });
    }

    function selectFirstAvailableSizeForFabric() {
        const fallback = firstVariantForFabric(selectedFabric) || productVariants[0] || null;

        if (fallback) {
            selectedFabric = fallback.fabric_type || '';
            selectedSize = fallback.size || '';
            selectedVariant = fallback;
        }
    }

    function normalizeVariantSelection(changedType) {
        if (!productVariants.length) {
            selectedVariant = null;
            return;
        }

        if (changedType === 'fabric') {
            selectFirstAvailableSizeForFabric();
            return;
        }

        selectedVariant = findSelectedVariant();

        if (selectedVariant) {
            return;
        }

        if (changedType === 'size') {
            const fallback = productVariants.find(function (variant) {
                const sizeMatches = !variant.size || variant.size === selectedSize;
                const fabricMatches = !hasFabricOptions || !variant.fabric_type || variant.fabric_type === selectedFabric;
                return sizeMatches && fabricMatches;
            });

            if (fallback) {
                selectedFabric = fallback.fabric_type || '';
                selectedSize = fallback.size || '';
                selectedVariant = fallback;
                return;
            }
        }

        selectFirstAvailableSizeForFabric();
    }

    function renderVariantState(changedType = null) {
        normalizeVariantSelection(changedType);

        document.querySelectorAll('.variant-option').forEach(function (button) {
            const type = button.dataset.optionType;
            const value = button.dataset.value;
            const isSelected = (type === 'size' && value === selectedSize)
                || (type === 'fabric' && value === selectedFabric);
            const isAvailable = isOptionAvailable(type, value);

            button.disabled = !isAvailable;
            button.classList.toggle('disabled', !isAvailable);
            button.classList.toggle('active', isSelected);
            button.classList.toggle('btn-dark', isSelected);
            button.classList.toggle('btn-outline-dark', !isSelected);
        });

        if (!selectedVariant && productVariants.length) {
            const total = document.getElementById('total');
            if (total) total.textContent = 'Select option';
            document.getElementById('addToCartBtn')?.setAttribute('disabled', 'disabled');
            return;
        }

        if (selectedVariant) {
            document.querySelectorAll('.variant-price-current').forEach(function (el) {
                el.textContent = formatPrice(selectedVariant.price);
            });

            const total = document.getElementById('total');
            if (total) total.textContent = formatPrice(selectedVariant.price);

            //const skuText = document.getElementById('variantSkuText');
            // if (skuText) skuText.textContent = 'SKU: ' + selectedVariant.sku;

            const addButton = document.getElementById('addToCartBtn');
            if (addButton) addButton.removeAttribute('disabled');
        }
    }

    document.querySelectorAll('.variant-option').forEach(function (button) {
        button.addEventListener('click', function () {
            if (this.disabled) {
                return;
            }

            if (this.dataset.optionType === 'size') {
                selectedSize = this.dataset.value;
            } else {
                selectedFabric = this.dataset.value;
            }

            renderVariantState(this.dataset.optionType);
        });
    });

    renderVariantState();

    function handleAddToCart(productId) {
        const btn = document.getElementById('addToCartBtn');

        if (productVariants.length && !selectedVariant) {
            Swal.fire({
                icon: 'warning',
                title: 'Choose Options',
                text: 'Please select a valid fabric type and size.',
                confirmButtonColor: '#8b1e2d',
            });
            return;
        }

        btn.classList.add('loading');

        $.ajax({
            url: @json(route('cart.add')),
            method: 'POST',
            data: {
                _token: @json(csrf_token()),
                product_id: productId,
                product_variant_id: selectedVariant ? selectedVariant.id : null,
                quantity: 1,
            },
            success: function (response) {
                btn.classList.remove('loading');

                $('#cartBadge').text(response.cart_count);

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

    const reviewStoreUrl = "{{ route('review.store') }}";
    const reviewUpdateUrlTemplate = "{{ route('review.update', ['id' => '__ID__']) }}";
    const reviewDeleteUrlTemplate = "{{ route('review.destroy', ['id' => '__ID__']) }}";

    function setReviewStarRating(value) {
        document.getElementById('selectedRating').value = value;

        document.querySelectorAll('.star-input').forEach(s => {
            s.classList.remove('fa-solid');
            s.classList.add('fa-regular');
        });

        for (let i = 0; i < value; i++) {
            document.querySelectorAll('.star-input')[i].classList.remove('fa-regular');
            document.querySelectorAll('.star-input')[i].classList.add('fa-solid');
        }
    }

    function resetReviewForm() {
        document.getElementById('review_id').value = '';
        document.getElementById('selectedRating').value = '';
        document.querySelector('#reviewForm textarea[name="review"]').value = '';
        document.querySelector('#reviewForm input[name="image"]').value = '';
        document.getElementById('remove_image').checked = false;
        document.getElementById('currentReviewImageWrap').style.display = 'none';
        document.getElementById('currentReviewImage').src = '';
        document.getElementById('reviewModalTitle').textContent = 'Add Review';
        document.getElementById('reviewSubmitBtn').textContent = 'Submit Review';

        document.querySelectorAll('.star-input').forEach(s => {
            s.classList.remove('fa-solid');
            s.classList.add('fa-regular');
        });
    }

    function closeAllReviewsModal() {
        document.getElementById('allReviewsModal').style.display = 'none';
    }

    function showReviewAlert(options) {
        const reviewAlertIcons = {
            success: '<i class="fa-regular fa-circle-check fa-xl" style="color:#198754;"></i>',
            error: '<i class="fa-regular fa-circle-xmark fa-xl" style="color:#dc3545;"></i>',
            warning: '<i class="fa-solid fa-triangle-exclamation fa-xl" style="color:#ffc107;"></i>',
        };

        const { icon, customClass, didOpen, ...rest } = options;
        const alertOptions = {
            ...rest,
            customClass: {
                popup: 'rounded-3 shadow',
                title: 'fs-5 fw-bold',
                icon: 'swal-fa-icon',
                ...(customClass || {}),
            },
            didOpen: (popup) => {
                const container = document.querySelector('.swal2-container');
                if (container) {
                    container.style.zIndex = '100001';
                }

                // Keep review alert icon aligned with title/text spacing.
                const iconEl = popup.querySelector('.swal2-icon.swal-fa-icon');
                if (iconEl) {
                    iconEl.style.width = '3.25em';
                    iconEl.style.height = '3.25em';
                    iconEl.style.margin = '1em auto 0.9em';
                }

                const iconContentEl = popup.querySelector('.swal2-icon.swal-fa-icon .swal2-icon-content');
                if (iconContentEl) {
                    iconContentEl.style.fontSize = '2.1em';
                }

                if (typeof didOpen === 'function') {
                    didOpen(popup);
                }
            },
        };

        if (icon && reviewAlertIcons[icon]) {
            alertOptions.icon = icon;
            alertOptions.iconHtml = reviewAlertIcons[icon];
        }

        return Swal.fire(alertOptions);
    }

    function openAddReviewModal() {
        resetReviewForm();
        document.getElementById('reviewModal').style.display = 'flex';
    }

    function openEditReviewModal(button) {
        resetReviewForm();
        const reviewId = button.dataset.reviewId;
        const rating = button.dataset.rating;
        const reviewText = button.dataset.review;
        const imageUrl = button.dataset.image;

        document.getElementById('review_id').value = reviewId;
        document.querySelector('#reviewForm textarea[name="review"]').value = reviewText;
        setReviewStarRating(parseInt(rating, 10));
        document.getElementById('reviewModalTitle').textContent = 'Edit Review';
        document.getElementById('reviewSubmitBtn').textContent = 'Update Review';

        if (imageUrl) {
            document.getElementById('currentReviewImage').src = imageUrl;
            document.getElementById('currentReviewImageWrap').style.display = 'block';
        }

        closeAllReviewsModal();
        document.getElementById('reviewModal').style.display = 'flex';
    }

    // Open modal on star click
    document.querySelectorAll('.review-trigger').forEach(el => {
        el.addEventListener('click', function () {

            @if(!auth()->check())
                showReviewAlert({
                    icon: 'warning',
                    title: 'Login Required',
                    text: 'Please login to add review',
                    confirmButtonColor: '#8b1e2d',
                });
                return;
            @endif

            openAddReviewModal();
        });
    });

    // Close modal
    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
        resetReviewForm();
    }

    // Star selection
    document.querySelectorAll('.star-input').forEach(star => {
        star.addEventListener('click', function () {
            setReviewStarRating(parseInt(this.dataset.value, 10));
        });
    });

    document.getElementById('reviewForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const reviewId = document.getElementById('review_id').value;
        const formData = new FormData(this);
        const isEdit = Boolean(reviewId);
        const url = isEdit
            ? reviewUpdateUrlTemplate.replace('__ID__', reviewId)
            : reviewStoreUrl;

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        if (!formData.get('rating')) {
            showReviewAlert({
                icon: 'warning',
                title: 'Rating required',
                text: 'Please select a star rating.',
                confirmButtonColor: '#8b1e2d',
            });
            return;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showReviewAlert({
                    icon: 'success',
                    title: isEdit ? 'Review Updated!' : 'Review Added!',
                    text: isEdit ? 'Your review has been updated successfully.' : 'Your review has been submitted successfully.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#8b1e2d',
                }).then(() => {
                    location.reload();
                });

                closeReviewModal();
            } else {
                showReviewAlert({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Something went wrong',
                    confirmButtonColor: '#8b1e2d',
                });
            }
        });
    });

    $(document).on('click', '.edit-review-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openEditReviewModal(this);
    });

    $(document).on('click', '.delete-review-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const reviewId = this.dataset.reviewId;
        closeAllReviewsModal();

        showReviewAlert({
            icon: 'warning',
            title: 'Delete review?',
            text: 'This action cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#8b1e2d',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            fetch(reviewDeleteUrlTemplate.replace('__ID__', reviewId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
            .then(async (res) => {
                let data = {};
                try {
                    data = await res.json();
                } catch (error) {
                    data = {};
                }

                if (!res.ok) {
                    throw new Error(data.message || 'Could not delete review. Please try again.');
                }

                return data;
            })
            .then((data) => {
                if (data.success) {
                    showReviewAlert({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Your review has been deleted.',
                        confirmButtonColor: '#8b1e2d',
                    }).then(() => location.reload());
                } else {
                    throw new Error(data.message || 'Could not delete review.');
                }
            })
            .catch((error) => {
                showReviewAlert({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Could not delete review.',
                    confirmButtonColor: '#8b1e2d',
                });
            });
        });
    });

    // Open all reviews modal
    document.querySelectorAll('.review-count').forEach(el => {
        el.addEventListener('click', function () {
            document.getElementById('allReviewsModal').style.display = 'flex';
        });
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

            $(this).removeClass("align-content-center");

            $span.text("•••");
            $span.removeClass("show-less-text");
        } else {
            $answer.addClass("expanded");
            $(this).data("expanded", 1);

            $(this).addClass("align-content-center");

            $span.text("Show less");
            $span.addClass("show-less-text");
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
