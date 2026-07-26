@extends('frontend.layouts.app')

@section('title', $pageContent->meta_title ?? 'Portfolio')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Portfolio')

@section('og_description', $pageContent->meta_description ?? 'Design Dhaga portfolio gallery.')

@section('og_image', asset($pageContent->meta_image ?? 'og-home.jpg'))

@push('extras')

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" media="print" onload="this.media='all'">
<noscript>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
</noscript>

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
    .gallery-filter-wrapper {
        position: relative;
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

    .gallery-filter-arrow {
        display: none;
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 50%;
        background: #c96b4b;
        color: #fff;
        font-size: 18px;
        line-height: 1;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .gallery-filter-arrow svg {
        width: 16px;
        height: 16px;
        display: block;
        stroke: currentColor;
    }

    .gallery-filter-arrow:disabled {
        opacity: 0.35;
        cursor: not-allowed;
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

        .gallery-filter-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 0 20px;
            padding: 0 8px;
        }

        .gallery-filter {
            flex: 1;
            align-items: center;
            justify-content: flex-start;
            overflow-x: auto;
            flex-wrap: nowrap;
            scroll-behavior: smooth;
            margin: 0;
            padding: 0;
        }

        .gallery-filter span {
            flex: 0 0 auto;
        }

        .gallery-filter-arrow {
            display: inline-flex;
        }

        .gallery-filter::-webkit-scrollbar {
            display: none;
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
@if(!empty($pageContent?->heading))
    <div class="container">
        <div class="row text-center">
            <div class="col-12">
                <h1 class="home-heading">{{ $pageContent->heading }}</h1>
            </div>
        </div>
    </div>
@endif

<section class="my-1">
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
    <div class="gallery-filter-wrapper">
        <button type="button" class="gallery-filter-arrow gallery-filter-prev" aria-label="Scroll subcategories left">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M15 18l-6-6 6-6" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
        <div class="gallery-filter">
            <span class="active" data-filter="*">All</span>
            @foreach ($category->subcategories as $subcategory)
            <span data-filter="{{ $subcategory->slug }}">{{ $subcategory->name }}</span>
            @endforeach
        </div>
        <button type="button" class="gallery-filter-arrow gallery-filter-next" aria-label="Scroll subcategories right">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M9 6l6 6-6 6" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>

    {{-- GALLERY --}}
    <div class="container px-md-4 px-2 mb-4">
        <div class="gallery-grid">
            @foreach ($category->subcategories as $subcategory)
                @foreach ($subcategory->galleries as $gallery)
                <div class="gallery-item {{ $subcategory->slug }}">
                    <a href="{{ asset($gallery->image) }}" class="glightbox">
                        <img src="{{ asset($gallery->image) }}" loading="lazy" decoding="async" alt="{{ $gallery->alt_text ?: ($gallery->title ?? '') }}">
                    </a>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
    @if($activeCategory->id == 1)
    <div class="sliding-text bg-dark py-3 px-2 w-100">
        <div class="scroll-container">
            <div class="scroll-content">
                <!-- ORIGINAL ITEMS -->
                @foreach ($category->subcategories as $subcategory)
                <div class="item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                        <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                        l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                    </svg> {{ $subcategory->name }}
                </div>
                @endforeach
                @foreach ($category->subcategories as $subcategory)
                <div class="item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                        <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                        l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                    </svg> {{ $subcategory->name }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <!-- ✅ BRANDS MARQUEE - Pure CSS, no Owl Carousel -->
    <div class="brands-marquee-wrapper py-3">
        <div class="brands-scroll-track">
            <!-- ORIGINAL SET -->
            <div class="brands-scroll-content">
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/Aieraa-overseas.png') }}" alt="Aieraa Overseas">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/bpr-office.png') }}" alt="BPR Office">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/BTC.png') }}" alt="BTC">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/cake-express.png') }}" alt="Cake Express">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/herbnest.png') }}" alt="Herbnest">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/marketinglu.png') }}" alt="Marketinglu">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/mishee.png') }}" alt="Mishee">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/Panfire.png') }}" alt="Panfire">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/puri-bakers.png') }}" alt="Puri Bakers">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/student-traffic.png') }}" alt="Student Traffic">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/the-blish.png') }}" alt="The Blish">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/the-safe-trader.png') }}" alt="The Safe Trader">
                </div>
            </div>
            <!-- DUPLICATE SET for seamless infinite loop -->
            <div class="brands-scroll-content" aria-hidden="true">
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/Aieraa-overseas.png') }}" alt="Aieraa Overseas">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/bpr-office.png') }}" alt="BPR Office">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/BTC.png') }}" alt="BTC">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/cake-express.png') }}" alt="Cake Express">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/herbnest.png') }}" alt="Herbnest">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/marketinglu.png') }}" alt="Marketinglu">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/mishee.png') }}" alt="Mishee">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/Panfire.png') }}" alt="Panfire">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/puri-bakers.png') }}" alt="Puri Bakers">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/student-traffic.png') }}" alt="Student Traffic">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/the-blish.png') }}" alt="The Blish">
                </div>
                <div class="brand-logo-item">
                    <img src="{{ asset('frontend_assets/images/brands-logo/the-safe-trader.png') }}" alt="The Safe Trader">
                </div>
            </div>
        </div>
    </div>
    @endif
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

<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof GLightbox === 'function') {
        GLightbox({
            loop: false,
            touchNavigation: true,
            preload: false
        });
    }
});
</script>
<script>

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

    const filterScroller = document.querySelector('.gallery-filter');
    const filterPrev = document.querySelector('.gallery-filter-prev');
    const filterNext = document.querySelector('.gallery-filter-next');

    function updateFilterArrows() {
        if (!filterScroller || !filterPrev || !filterNext) {
            return;
        }

        const maxScroll = filterScroller.scrollWidth - filterScroller.clientWidth;
        const canScroll = maxScroll > 1;

        filterPrev.disabled = !canScroll || filterScroller.scrollLeft <= 1;
        filterNext.disabled = !canScroll || filterScroller.scrollLeft >= maxScroll - 1;
    }

    if (filterScroller && filterPrev && filterNext) {
        filterPrev.addEventListener('click', () => {
            filterScroller.scrollBy({ left: -180, behavior: 'smooth' });
        });

        filterNext.addEventListener('click', () => {
            filterScroller.scrollBy({ left: 180, behavior: 'smooth' });
        });

        filterScroller.addEventListener('scroll', updateFilterArrows);
        window.addEventListener('resize', updateFilterArrows);
        updateFilterArrows();
    }

</script>

@endpush