@extends('frontend.layouts.app')
@section('title', 'Portfolio')

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade
clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs,
and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

@push('extras')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<style>
    /* Brands Marquee - mirrors the .sliding-text pattern */
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

    .brand-logo-item img:hover {
        filter: grayscale(0%);
        opacity: 1;
    }

    @keyframes brands-scroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
</style>
@endpush

@section('content')
<section class="my-4">
    <div class="d-flex justify-content-center portfolio-category">
        <ul class="nav nav-tabs custom-tabs border-0 gap-2" id="myTab" role="tablist">
            @foreach ($portfolio as $category)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $category->slug }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $category->slug }}-tab-pane"
                    type="button" role="tab" aria-controls="{{ $category->slug }}-tab-pane" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    <img src="{{ asset($category->image) }}" class="img-fluid" alt="">
                    <span>{{ $category->name }}</span>
                </button>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="tab-content" id="myTabContent">
        @foreach ($portfolio as $category)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $category->slug }}-tab-pane" role="tabpanel">
            <!-- FILTER -->
            <div class="gallery-filter">
                <span class="active" data-filter="*">All</span>
                @foreach ($category->subcategories as $subcategory)
                <span data-filter="{{ $subcategory->slug }}">{{ $subcategory->name }}</span>
                @endforeach
            </div>

            <!-- GALLERY -->
            <div class="container mb-2 px-3">
                <div class="gallery-grid">
                    @foreach ($category->subcategories as $subcategory)
                    @foreach ($subcategory->galleries as $gallery)
                    <div class="gallery-item {{ $gallery->subcategory->slug }}">
                        <a href="{{ asset($gallery->image) }}"
                            class="glightbox">
                            <img src="{{ asset($gallery->image) }}" alt="">
                        </a>
                    </div>
                    @endforeach
                    @endforeach
                </div>

            </div>

            @if($category->name == "Fabric Painting")
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
                    </div>
                </div>
            </div>
            @endif

            @if($category->name == "Graphics Gallery")
            <!-- ✅ BRANDS MARQUEE - Pure CSS, no Owl Carousel -->
            <div class="brands-marquee-wrapper py-3">
                <div class="brands-scroll-track">
                    <!-- ORIGINAL SET -->
                    <div class="brands-scroll-content">
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/Aieraa-overseas.png" alt="Aieraa Overseas">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/bpr-office.png" alt="BPR Office">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/BTC.png" alt="BTC">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/cake-express.png" alt="Cake Express">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/herbnest.png" alt="Herbnest">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/marketinglu.png" alt="Marketinglu">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/mishee.png" alt="Mishee">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/Panfire.png" alt="Panfire">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/puri-bakers.png" alt="Puri Bakers">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/student-traffic.png" alt="Student Traffic">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/the-blish.png" alt="The Blish">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/the-safe-trader.png" alt="The Safe Trader">
                        </div>
                    </div>
                    <!-- DUPLICATE SET for seamless infinite loop -->
                    <div class="brands-scroll-content" aria-hidden="true">
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/Aieraa-overseas.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/bpr-office.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/BTC.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/cake-express.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/herbnest.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/marketinglu.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/mishee.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/Panfire.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/puri-bakers.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/student-traffic.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/the-blish.png" alt="">
                        </div>
                        <div class="brand-logo-item">
                            <img src="frontend_assets/images/brands-logo/the-safe-trader.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
        
    </div>
</section>
@endsection

@push('scripts')
<script>
    /* Lightbox */
    GLightbox({
        loop: true
    });

    /* Filter buttons */
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

    /* Filter click */
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.dataset.filter;
            applyFilter(filter);
        });
    });

    /* Nav link click → reset gallery */
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {

            // Activate "All" filter
            filterButtons.forEach(b => b.classList.remove('active'));
            const allBtn = document.querySelector('.gallery-filter span[data-filter="*"]');

            if (allBtn) {
                allBtn.classList.add('active');
            }

            // Show all items
            applyFilter('*');
        });
    });

    document.addEventListener("DOMContentLoaded", function () {

        if (!window.bootstrap || !window.bootstrap.Tab) return;

        // Open tab from URL hash
        function activateTabFromHash() {

            let hash = window.location.hash.replace('#', '');

            if (!hash) return;

            const tabTrigger = document.querySelector(
                `#${hash}-tab`
            );

            if (tabTrigger) {
                new bootstrap.Tab(tabTrigger).show();
            }
        }

        // Change URL when tab changes
        document.querySelectorAll('#myTab button[data-bs-toggle="tab"]')
            .forEach((btn) => {

                btn.addEventListener('shown.bs.tab', function (e) {

                    const id = e.target.id.replace('-tab', '');

                    history.replaceState(
                        null,
                        null,
                        `#${id}`
                    );
                });

            });

        // Listen hash changes
        window.addEventListener("hashchange", activateTabFromHash);

        // Initial load
        activateTabFromHash();

    });
</script>
@endpush