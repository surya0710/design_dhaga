@extends('frontend.layouts.app')

@section('title', $pageContent->meta_title ?? 'Portfolio')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Portfolio')

@section('og_description', $pageContent->meta_description ?? 'Design Dhaga portfolio gallery.')

@section('og_image', asset('frontend_assets/images/og-home.jpg'))

@push('extras')

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<style>

    /* =========================
        CATEGORY SECTION
    ========================== */

    .portfolio-category-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 10px 0;
        scrollbar-width: none;
    }

    .portfolio-category-wrapper::-webkit-scrollbar {
        display: none;
    }

    .portfolio-category-scroll {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 22px;
        min-width: max-content;
        padding: 0 15px;
    }

    .portfolio-category-card {
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 120px;
        max-width: 140px;
        transition: 0.3s ease;
    }

    .portfolio-category-image {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        overflow: hidden;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        transition: 0.3s ease;
    }

    .portfolio-category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .portfolio-category-card span {
        font-size: 17px;
        font-weight: 500;
        color: #222;
        text-align: center;
        line-height: 1.4;
    }
    .portfolio-category-card.active span {
        color: #c96b4b;
        font-weight: 700;
    }

    .portfolio-category-card.active .portfolio-category-image {
        border: 3px solid #c96b4b;
    }
    .portfolio-category-card:hover {
        transform: translateY(-4px);
    }
    .gallery-filter {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
        margin: 10px 0 20px;
    }

    .gallery-filter span {
        padding: 10px 18px;
        border-radius: 30px;
        background: #f4f4f4;
        cursor: pointer;
        transition: 0.3s ease;
        font-size: 15px;
    }

    .gallery-filter span.active,
    .gallery-filter span:hover {
        background: #c96b4b;
        color: #fff;
    }

    /* =========================
        GALLERY
    ========================== */

    .gallery-grid {
        column-count: 4;
        column-gap: 18px;
    }

    .gallery-item {
        break-inside: avoid;
        margin-bottom: 18px;
        overflow: hidden;
        border-radius: 14px;
    }

    .gallery-item img {
        width: 100%;
        display: block;
        border-radius: 14px;
        transition: 0.4s ease;
    }

    .gallery-item img:hover {
        transform: scale(1.04);
    }

    /* =========================
        MARQUEE
    ========================== */

    .brands-marquee-wrapper {
        width: 100%;
        overflow: hidden;
    }

    .brands-scroll-track {
        display: flex;
        width: max-content;
        animation: brands-scroll 35s linear infinite;
    }

    .brands-scroll-content {
        display: flex;
        align-items: center;
        gap: 40px;
        padding: 0 20px;
    }

    .brand-logo-item img {
        max-width: 90px;
        object-fit: contain;
    }

    @keyframes brands-scroll {

        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }

    }

    /* =========================
        MOBILE
    ========================== */

    @media (max-width: 992px) {

        .gallery-grid {
            column-count: 3;
        }

    }

    @media (max-width: 768px) {

        .portfolio-category-scroll {
            justify-content: flex-start;
            gap: 14px;
        }

        .portfolio-category-card {
            min-width: 90px;
            max-width: 100px;
        }

        .portfolio-category-image {
            width: 75px;
            height: 75px;
        }

        .portfolio-category-card span {
            font-size: 14px;
        }

        .gallery-grid {
            column-count: 2;
            column-gap: 12px;
        }

        .gallery-item {
            margin-bottom: 12px;
        }

        .gallery-filter {
            justify-content: flex-start;
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 5px;
        }

        .gallery-filter::-webkit-scrollbar {
            display: none;
        }

    }

    @media (max-width: 480px) {

        .gallery-grid {
            column-count: 2;
        }

        .portfolio-category-image {
            width: 65px;
            height: 65px;
        }

        .portfolio-category-card span {
            font-size: 13px;
        }

        .gallery-filter span {
            padding: 8px 14px;
            font-size: 13px;
        }

    }

</style>

@endpush

@section('content')

<section class="my-4">

    {{-- CATEGORY SECTION --}}
    <div class="d-flex justify-content-center portfolio-category">
        <ul class="nav nav-tabs custom-tabs border-0 gap-2" id="myTab" role="tablist">
            @foreach($portfolio as $category)
            <li class="nav-item" role="presentation">
                <a class="nav-link text-center {{ $activeCategory->id == $category->id ? 'active' : '' }}" href="{{ route('portfolio', $category->slug) }}">
                    <img src="{{ asset($category->image) }}" class="img-fluid" alt="{{ $category->name }}"> 
                    <p class="mb-0 mt-1">{{ $category->name }}</p>
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    @php
        $category = $activeCategory;
    @endphp

    {{-- FILTER --}}
    <div class="gallery-filter">
        <span class="active" data-filter="*">All</span>
        @foreach ($category->subcategories as $subcategory)
        <span data-filter="{{ $subcategory->slug }}">{{ $subcategory->name }}</span>
        @endforeach
    </div>

    {{-- GALLERY --}}
    <div class="container px-md-4 px-2 mb-4">
        <div class="gallery-grid">
            @foreach ($category->subcategories as $subcategory)
                @foreach ($subcategory->galleries as $gallery)
                <div class="gallery-item {{ $subcategory->slug }}">
                    <a href="{{ asset($gallery->image) }}" class="glightbox">
                        <img src="{{ asset($gallery->image) }}" loading="lazy" decoding="async" alt="Gallery Image">
                    </a>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
</section>

<script>
    const textData = [];
    @foreach($highlights as $highlight)

    textData.push(`
        <span>{{ $highlight->title }}</span>
        <img src="{{ Storage::url($highlight->emoji) }}" class="emoji" alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
    @endforeach

</script>

@endsection

@push('scripts')

<script>

    /* LIGHTBOX */

    GLightbox({
        loop: false,
        touchNavigation: true,
        preload: false
    });

    /* FILTER */

    const filterButtons = document.querySelectorAll('.gallery-filter span');

    const galleryItems = document.querySelectorAll('.gallery-item');

    function applyFilter(filter) {

        galleryItems.forEach(item => {

            if (filter === '*' || item.classList.contains(filter)) {

                item.style.display = 'block';

            } else {

                item.style.display = 'none';

            }

        });

    }

    filterButtons.forEach(btn => {

        btn.addEventListener('click', () => {

            filterButtons.forEach(b => b.classList.remove('active'));

            btn.classList.add('active');

            applyFilter(btn.dataset.filter);

        });

    });

</script>

@endpush