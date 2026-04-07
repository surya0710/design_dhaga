@extends('frontend.layouts.app')
@section('title', $blog->meta_title)

@section('meta_description', $blog->meta_description)

@section('meta_keywords', $blog->meta_keywords)

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

@section('content')

<div class="container">
    <div class="row gx-lg-5 px-xs-2">
        <div class="col-lg-9 mt-lg-3">
            <div class="mb-4">
                <h1 class="fw-bold">{{ $blog->title }}</h1>
            </div>

            <div class="rounded overflow-hidden mb-4">
                <img src="{{ asset('uploads/blogs/'.$blog->image) }}" class="img-fluid w-100" alt="{{ $blog->title }}" />
            </div>

            <div class="blog-content text-justify">
                {!! $blog->content !!}
            </div>
        </div>

        <div class="col-lg-3 mt-lg-3">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="h4 fw-bold mb-0">Featured Products</h2>
            </div>

            <div id="recentBlogsCarousel" class="carousel slide d-md-none" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($featuredProducts as $product)
                    @php $productUrl = getProductUrl($product); @endphp
                    <div class="carousel-item active">
                        <a href="{{ $productUrl }}"
                            class="text-decoration-none text-dark">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ Storage::url($product->image) }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}" />
                                </div>
                                <div class="card-body">
                                    <h3 class="h6 fw-bold mb-2">{{ $product->name }}</h3>
                                    <p class="small text-muted mb-0">
                                        {{ $product->sale_price }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev bg-white border rounded-circle shadow-sm" type="button"
                data-bs-target="#recentBlogsCarousel" data-bs-slide="prev" style="width: 44px;height: 44px;top: 50%;transform: translateY(-50%);">
                    <span class="carousel-control-prev-icon" style="filter: invert(1); width: 16px; height: 16px"
                        aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next bg-white border rounded-circle shadow-sm" type="button"
                data-bs-target="#recentBlogsCarousel" data-bs-slide="next" style="width: 44px;height: 44px;top: 50%;transform: translateY(-50%);">
                    <span class="carousel-control-next-icon" style="filter: invert(1); width: 16px; height: 16px"
                        aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <div class="d-none d-md-flex flex-column gap-4">
                @foreach($featuredProducts as $product)
                @php $productUrl = getProductUrl($product); @endphp
                <a href="{{ $productUrl }}" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm">
                        <div class="ratio ratio-4x3">
                            <img src="{{ Storage::url($product->image) }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}" />
                        </div>
                        <div class="card-body p-2">
                            <h3 class="h6 fw-bold mb-2">{{ $product->name }}</h3>
                            <p class="small text-muted mb-0">
                                ₹ {{ $product->sale_price }}
                            </p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection