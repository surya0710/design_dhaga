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
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="fabric-tab" data-bs-toggle="tab" data-bs-target="#fabric-tab-pane"
                    type="button" role="tab" aria-controls="new-arrival-tab-pane" aria-selected="true">
                    <img src="frontend_assets/images/portfolio-icons/hand-painted-fabric-icon.png" class="img-fluid"
                        alt="fabric-icon">
                    <span>Fabric Painting</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="best-seller-tab" data-bs-toggle="tab" data-bs-target="#graphics-tab-pane"
                    type="button" role="tab" aria-controls="best-seller-tab-pane" aria-selected="false" tabindex="-1">
                    <img src="frontend_assets/images/portfolio-icons/Graphics-icons.png" class="img-fluid"
                        alt="graphics-icon">
                    <span>Graphics Gallery</span>
                </button>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="fabric-tab-pane" role="tabpanel">
            <!-- FILTER -->
            <div class="gallery-filter">
                <span class="active" data-filter="*">All</span>
                <span data-filter="Acrylic">Acrylic</span>
                <span data-filter="foil-painting">Foil</span>
                <span data-filter="leather-printing">Leather</span>
                <span data-filter="milky-printing">Milky</span>
                <span data-filter="puffy-printing">Puffy</span>
                <span data-filter="silky-printing">Silky</span>
                <span data-filter="stencil-printing">Stencil</span>
            </div>

            <!-- GALLERY -->
            <div class="container mb-2 px-3">
                <div class="gallery-grid">

                    <div class="gallery-item Acrylic">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/01.webp"
                            class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item Acrylic">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/02.webp"
                            class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item Acrylic">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/03.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/03.webp">
                        </a>
                    </div>
                    <div class="gallery-item Acrylic">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/04.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Acrylic Painting/04.webp">
                        </a>
                    </div>

                    <div class="gallery-item foil-painting">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/01.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item foil-painting">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/02.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item foil-painting">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/03.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/03.webp">
                        </a>
                    </div>
                    <div class="gallery-item foil-painting">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/04.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Foil Painting/04.webp">
                        </a>
                    </div>
                    <div class="gallery-item leather-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Leather painting/01.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Leather painting/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item leather-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Leather painting/02.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Leather painting/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item milky-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Milky painting/01.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Milky painting/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item milky-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Milky painting/02.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Milky painting/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item puffy-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Puffy painting/01.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Puffy painting/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item puffy-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Puffy painting/02.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Puffy painting/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item puffy-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Puffy painting/03.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Puffy painting/03.webp">
                        </a>
                    </div>
                    <div class="gallery-item silky-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Silk painting/01.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Silk painting/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item silky-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Silk painting/02.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Silk painting/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item stencil-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Stencil painting/01.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Stencil painting/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item stencil-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Stencil painting/02.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Stencil painting/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item stencil-printing">
                        <a href="frontend_assets/images/portfolio/fabric-painting-portfolio/Stencil painting/03.webp" class="glightbox">
                            <img src="frontend_assets/images/portfolio/fabric-painting-portfolio/Stencil painting/03.webp">
                        </a>
                    </div>
                </div>

            </div>

            <div class="sliding-text bg-dark py-3 px-2 w-100">
                <div class="scroll-container">
                    <div class="scroll-content">
                        <!-- ORIGINAL ITEMS -->
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Acrylic
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Foil
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Leather
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Milky
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Puffy
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Silky
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Stencil
                        </div>

                        <!-- DUPLICATED ITEMS FOR INFINITE LOOP -->
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Acrylic
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Foil
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Leather
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Milky
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Puffy
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Silky
                        </div>
                        <div class="item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                                <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                                l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                            </svg> Stencil
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab-pane" id="graphics-tab-pane" role="tabpanel">
            <!-- FILTER -->
            <div class="gallery-filter">
                <span class="active" data-filter="*">ALL</span>
                <span data-filter="social-media">Social Media</span>
                <span data-filter="logo-brand">Logo & Brand</span>
                <span data-filter="packaging">Packaging</span>
            </div>

            <!-- GALLERY -->
            <div class="container mb-2">
                <div class="gallery-grid">
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/01.webp"
                            class="glightbox">
                            <img src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/01.webp">
                        </a>
                    </div>
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/02.webp"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/02.webp">
                        </a>
                    </div>
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/a4-bella-monde-sm.webp"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/a4-bella-monde-sm.webp">
                        </a>
                    </div>
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/emrald.webp"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/emrald.webp">
                        </a>
                    </div>
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/herbnest 01.webp"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/herbnest 01.webp">
                        </a>
                    </div>
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/hotel.webp"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/hotel.webp">
                        </a>
                    </div>
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/HYPHEN-MEERUT.webp"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/HYPHEN-MEERUT.webp">
                        </a>
                    </div>
                    <div class="gallery-item social-media">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/insta scholarship 02.webp"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Content & Social Media Strategy/insta scholarship 02.webp">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/A4 FEBRIC NAMA.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/A4 FEBRIC NAMA.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/abroad-educo-landscape.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/abroad-educo-landscape.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/Bitamin.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/Bitamin.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/cake xpress.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/cake xpress.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/herbnest.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/herbnest.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/HOW TO COOK.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/HOW TO COOK.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/mssa.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/mssa.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/samshot.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/samshot.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/TEV.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/TEV.jpg">
                        </a>
                    </div>
                    <div class="gallery-item logo-brand">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/Ziddi Fitness.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Logo & Brand Identity System/Ziddi Fitness.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/Arogya-Delight.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/Arogya-Delight.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/bitamin-products.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/bitamin-products.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/cookies .jpg"
                            class="glightbox">
                            <img src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/cookies .jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/cubwalk.jpg"
                            class="glightbox">
                            <img src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/cubwalk.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/herbnest-products.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/herbnest-products.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/honey-wall.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/honey-wall.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/honey.jpg"
                            class="glightbox">
                            <img src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/honey.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/indri-dhoop.jpg"
                            class="glightbox">
                            <img
                                src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/indri-dhoop.jpg">
                        </a>
                    </div>
                    <div class="gallery-item packaging">
                        <a href="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/milk-pack.jpg"
                            class="glightbox">
                            <img src="frontend_assets/images/portfolio/graphics-gallery/Packaging Designs/milk-pack.jpg">
                        </a>
                    </div>
                </div>
            </div>

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

        </div>
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
    
        const normalizeHash = (hash) =>
            decodeURIComponent(hash || "")
                .replace(/^#/, "")
                .trim()
                .toLowerCase()
                .replace(/\s+/g, "-");
    
        const activateTabFromHash = () => {
            const normalized = normalizeHash(window.location.hash);
            if (!normalized) return;
    
            if (normalized === "fabric-painting") {
                const tab = document.getElementById("fabric-tab");
                if (tab) new bootstrap.Tab(tab).show();
            }
    
            if (normalized === "graphics-gallery") {
                const tab = document.getElementById("best-seller-tab");
                if (tab) new bootstrap.Tab(tab).show();
            }
        };
    
        // Update URL when tab changes
        document
            .querySelectorAll('#myTab button[data-bs-toggle="tab"]')
            .forEach((btn) => {
                btn.addEventListener("shown.bs.tab", function (event) {
                    const id = event.target.id;
    
                    if (id === "fabric-tab") {
                        history.replaceState(null, "", "#Fabric-Painting");
                    }
    
                    if (id === "best-seller-tab") {
                        history.replaceState(null, "", "#Graphics-Gallery");
                    }
                });
            });
    
        window.addEventListener("hashchange", activateTabFromHash);
    
        // 🔥 Activate immediately on DOM ready
        activateTabFromHash();
    });
</script>
@endpush