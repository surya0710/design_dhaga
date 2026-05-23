@extends('frontend.layouts.app')

@section('title', $pageContent->meta_title ?? 'Portfolio')

@section('meta_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', $pageContent->meta_keywords ?? 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade
clothing, made in India')

@section('og_title', $pageContent->meta_title ?? 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', $pageContent->meta_description ?? 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('og_image', asset('frontend_assets/images/og-home.jpg'))

@push('extras')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<style>
    .brands-marquee-wrapper {
        width: 100%;
        overflow: hidden;
    }

    .brands-scroll-track {
        display: flex;
        width: max-content;
        animation: brands-scroll 40s linear infinite;
    }

    .brands-scroll-track:hover {
        animation-play-state: paused;
    }

    .brands-scroll-content {
        display: flex;
        align-items: center;
        gap: 40px;
        padding: 0 20px;
    }

    .brand-logo-item {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-logo-item img {
        width: 100%;
        max-width: 90px;
        object-fit: contain;
        transition: filter 0.3s ease, opacity 0.3s ease;
    }

    @keyframes brands-scroll {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }
</style>
@endpush

@section('content')

<section class="my-4">

    {{-- CATEGORY NAVIGATION --}}
    <div class="d-flex justify-content-center portfolio-category mb-4">

        <ul class="nav nav-tabs custom-tabs border-0 gap-2">

            @foreach ($portfolio as $category)

            <li class="nav-item">

                <a href="{{ route('portfolio', $category->slug) }}" class="nav-link {{ $activeCategory->id == $category->id ? 'active' : '' }}">

                    <img src="{{ asset($category->image) }}" class="img-fluid" alt="{{ $category->name }}">

                    <span>{{ $category->name }}</span>

                </a>

            </li>

            @endforeach

        </ul>

    </div>

    {{-- ACTIVE CATEGORY --}}
    @php
        $category = $activeCategory;
    @endphp

    {{-- FILTER --}}
    <div class="gallery-filter">

        <span class="active" data-filter="*">All</span>

        @foreach ($category->subcategories as $subcategory)

        <span data-filter="{{ $subcategory->slug }}">
            {{ $subcategory->name }}
        </span>

        @endforeach

    </div>

    {{-- GALLERY --}}
    <div class="container mb-2 px-3">

        <div class="gallery-grid">

            @foreach ($category->subcategories as $subcategory)

                @foreach ($subcategory->galleries as $gallery)

                <div class="gallery-item {{ $subcategory->slug }}">

                    <a href="{{ asset($gallery->image) }}"
                        class="glightbox">

                        <img src="{{ asset($gallery->image) }}"
                            alt="">

                    </a>

                </div>

                @endforeach

            @endforeach

        </div>

    </div>

    {{-- FABRIC PAINTING SECTION --}}
    @if($category->name == "Fabric Painting")

    <div class="sliding-text bg-dark py-3 px-2 w-100">

        <div class="scroll-container">

            <div class="scroll-content">

                @foreach ($category->subcategories as $subcategory)

                <div class="item">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="26 -26 100 125">

                        <path fill="#ffffff"
                            d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>

                    </svg>

                    {{ $subcategory->name }}

                </div>

                @endforeach

            </div>

        </div>

    </div>

    @endif

    {{-- GRAPHICS GALLERY --}}
    @if($category->name == "Graphics Gallery")

    <div class="brands-marquee-wrapper py-3">

        <div class="brands-scroll-track">

            {{-- ORIGINAL --}}
            <div class="brands-scroll-content">

                @php
                    $logos = [
                        'Aieraa-overseas.png',
                        'bpr-office.png',
                        'BTC.png',
                        'cake-express.png',
                        'herbnest.png',
                        'marketinglu.png',
                        'mishee.png',
                        'Panfire.png',
                        'puri-bakers.png',
                        'student-traffic.png',
                        'the-blish.png',
                        'the-safe-trader.png'
                    ];
                @endphp

                @foreach($logos as $logo)

                <div class="brand-logo-item">

                    <img src="{{ asset('frontend_assets/images/brands-logo/' . $logo) }}"
                        alt="Brand Logo">

                </div>

                @endforeach

            </div>

            {{-- DUPLICATE --}}
            <div class="brands-scroll-content" aria-hidden="true">

                @foreach($logos as $logo)

                <div class="brand-logo-item">

                    <img src="{{ asset('frontend_assets/images/brands-logo/' . $logo) }}"
                        alt="Brand Logo">

                </div>

                @endforeach

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

        <img src="{{ Storage::url($highlight->emoji) }}"
            class="emoji"
            alt="{{ $highlight->alt_text ?? $highlight->title }}">
    `);

    @endforeach

</script>

@endsection

@push('scripts')

<script>

    /* Lightbox */
    GLightbox({
        loop: true
    });

    /* Gallery Filter */
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