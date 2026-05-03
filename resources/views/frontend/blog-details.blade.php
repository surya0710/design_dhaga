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

            <div id="recentBlogsCarousel" class="d-lg-none">
                <div class="owl-carousel">
                    @foreach($featuredProducts as $product)
                    @php $productUrl = getProductUrl($product); @endphp
                    <div>
                        <a href="{{ $productUrl }}" class="text-decoration-none text-dark">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="ratio ratio-4x3">
                                    <img src="{{ Storage::url($product->image) }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}" />
                                </div>
                                <div class="card-body">
                                    <h3 class="h6 fw-bold mb-2 mt-0">{{ $product->name }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="d-none d-lg-flex flex-column gap-4">
                @foreach($featuredProducts as $product)
                @php $productUrl = getProductUrl($product); @endphp
                <a href="{{ $productUrl }}" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm">
                        <div class="ratio ratio-4x3">
                            <img src="{{ Storage::url($product->image) }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}" />
                        </div>
                        <div class="card-body p-2">
                            <h3 class="h6 fw-bold mb-2 mt-0">{{ $product->name }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
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
        $shareTitle = urlencode($blog->title);
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
                }
            }
        });
    });
</script>
<script>
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
</script>
@endpush