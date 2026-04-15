@extends('frontend.layouts.app')
@section('title', 'Design Dhaga - Hand-Painted Fashion')

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

@section('content')
<div class="container py-2 category-icons">
    <div class="d-flex justify-content-center gap-3">
        @foreach($categories as $category)
            <div class="text-center">
                <a href="{{ route('shop.index', [$category->slug]) }}" class="text-decoration-none">
                    <img src="{{ asset('uploads/categories/'.$category->image) }}" alt="{{ $category->name }}') }}" class="img-fluid">
                    <h4>{{ $category->name }}</h4>
                </a>
            </div>
        @endforeach
    </div>
</div>
<!-- ================= Banner Slider ================= -->
<div id="homeSlider" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="frontend_assets/images/home-slider/slider-1.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption caption-left text-white">
                <h2>Hand-Painted Elegance <br>for the <strong>Modern Man</strong></h2>
                <p>Inspired by <i>culture</i>, and designed to stand apart</p>
                <a href="{{ route('shop.index', ['men']) }}" class="btn btn-outline-primary">Shop Now</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="frontend_assets/images/home-slider/slider-2.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption caption-right text-dark">
                <h2>Hand-Painted Stories, <br><strong>for Womens</strong></h2>
                <p>Every outfit is a canvas crafted by <br>skilled artists, who love timeless elegance.</p>
                <a href="{{ route('shop.index', ['women']) }}" class="btn btn-outline-primary">Shop Now</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="frontend_assets/images/home-slider/slider-3.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption caption-left text-white">
                <h2>Explore <strong>Twinning</strong> Styles</h2>
                <p>Hand-painted twinning outfits for <i><strong>couples<br> families, and little ones</strong></i></p>
                <a href="{{ route('store') }}" class="btn btn-outline-primary">Shop Now</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="frontend_assets/images/home-slider/slider-4.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption caption-right text-dark">
                <h2><strong>Little Outfits</strong>. Big Smiles</h2>
                <p>Hand-painted kidswear made with <br><strong><i>love, colors, and comfort</i></strong>.</p>
                <a href="{{ route('shop.index', ['kids']) }}" class="btn btn-outline-primary">Shop Now</a>
            </div>
        </div>
        <div class="carousel-item">
            <a href="{{ route('contact-us') }}#form">
                <img src="frontend_assets/images/home-slider/slider-5.jpg" class="d-block w-100" alt="...">
                <!--<div class="carousel-caption caption-center text-dark">-->
                <!--    <h2>Hand Painted<br><strong>Customize Design</strong></h2>-->
                <!--    <a href="" class="btn btn-outline-primary">Customize Your Outfit</a>-->
                <!--</div>-->
            </a>
        </div>
        <div class="carousel-item">
            <a href="{{ route('contact-us') }}#form">
                <img src="frontend_assets/images/home-slider/slider-6.jpg" class="d-block w-100" alt="...">
            </a>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#homeSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<section class="container d-none d-md-block" id="our-info">
    <div class="row">
        <div class="col-md-4 info-box px-5">
            <h1>Art Meets</h1>
            <p>Craftsmanship</p>
        </div>
        <div class="col-md-4 text-center info-box">
            <h1>Exclusive Designs</h1>
            <p>Premium Detailing</p>
        </div>
        <div class="col-md-4 text-right info-box px-5">
            <h1>Fully Customizable</h1>
            <p>Your Idea, Our Artwork</p>
        </div>
    </div>
</section>
<section class="features-box d-sm-block d-md-none">
    <div class="container">
        <div class="row feature-items">
            <div class="feature-item col">
                <img src="frontend_assets/images/easy-delivery-process.svg" class="mobile-icons" />
                <h4>Easy Delivery</h4>
            </div>

            <div class="feature-item col">
                <img src="frontend_assets/images/exquisite-product.svg" class="mobile-icons" />
                <h4>Exquisite Product</h4>
            </div>

            <div class="feature-item col">
                <img src="frontend_assets/images/intricate-design.svg" class="mobile-icons" />
                <h4>Intricate Design</h4>
            </div>
        </div>
    </div>
</section>
<section class="bg-body-primary py-4">
    <div class="container">

        <!-- Centered Tabs -->
        <div class="d-flex justify-content-center mb-3">
            <ul class="nav nav-tabs custom-tabs border-0 gap-2" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="new-arrival-tab" data-bs-toggle="tab" data-bs-target="#new-arrival-tab-pane" type="button" role="tab"
                    aria-controls="new-arrival-tab-pane" aria-selected="true">New Arrival</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="best-seller-tab" data-bs-toggle="tab" data-bs-target="#best-seller-tab-pane" type="button" role="tab"
                    aria-controls="best-seller-tab-pane" aria-selected="false">Best Seller</button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">

            <!-- New Arrival -->
            <div class="tab-pane fade show active" id="new-arrival-tab-pane" role="tabpanel">
                <div class="products-conatiner">
                    @foreach ($newArrivals as $product)
                    @php $url = getProductUrl($product); @endphp
                    <a class="product-item" href="{{ $url }}">
                        <img src="{{ Storage::url($product->image) }}" class="loaded" alt="{{ $product->name }}">
                        <p>{{ $product->name }}</p>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Best Seller -->
            <div class="tab-pane fade" id="best-seller-tab-pane" role="tabpanel">
                <div class="products-conatiner">
                    <a class="product-item" href="{{ route('store') }}">
                        <img src="frontend_assets/images/products/best-seller/Celebrity-Outfits-Hand-painted.jpg"
                            class="loaded" alt="">
                        <p>Celebrity Outfits Hand Painted</p>
                    </a>
                    <a class="product-item" href="{{ route('store') }}">
                        <img src="frontend_assets/images/products/best-seller/Couple-Couture.jpg" class="loaded" alt="">
                        <p>Couple Couture</p>
                    </a>
                    <a class="product-item" href="{{ route('store') }}">
                        <img src="frontend_assets/images/products/best-seller/mens-designer-dresses.jpg" class="loaded"
                            alt="">
                        <p>Mens Designer Dresses</p>
                    </a>
                    <a class="product-item" href="{{ route('store') }}">
                        <img src="frontend_assets/images/products/best-seller/Modren-art-Design-Saree.jpg" class="loaded"
                            alt="">
                        <p>Modren Art Design Saree</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-4 bg-body-secondary" id="your-idea-our-brush">
    <div class="container">
        <div class="d-flex align-items-center">
            <div class="col text-small-center">
                <img src="frontend_assets/images/hand-painting-fabric-image.png" alt="Custmize Now" class="w-80" />
            </div>
            <div class="col">
                <div class="py-md-3 px-3">
                    <h1>Your Idea. Our Brush.</h1>
                    <p class="text-justify">At Design Dhaga, we believe that fashion is a form of
                        self-expression. That's why we create outfits that reflect your personality, values, and
                        style. Whether you're looking for a statement piece or a wardrobe staple, our team of
                        skilled designers and artisans will work with you to bring your vision to life.
                    </p>
                    <ul class="unordered-list">
                        <li>Every outfit is hand-painted, one of a kind.</li>
                        <li>Your story guides every brushstroke.</li>
                        <li>No repeats. No templates. Just personal art</li>
                    </ul>
                    <a class="btn btn-outline-secondary view-all-btn mt-2" href="{{ route('contact-us') }}#form">Customize Now</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="sliding-text bg-dark py-3 px-2 w-100">
    <div class="scroll-container">
        <div class="scroll-content">
            <!-- ORIGINAL ITEMS -->
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Hand-painted fashion <img src="frontend_assets/images/emoji/Hand-painted-fashion.png"
                    class="emoji" alt="Hand Painted Fashion"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Custom Design <img src="frontend_assets/images/emoji/Custom-design.png" class="emoji"
                    alt="Custom Design"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Premium branding services <img src="frontend_assets/images/emoji/Premium.png" class="emoji"
                    alt="Premium Branding Services"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Made in India <img src="frontend_assets/images/emoji/india-flag.png" class="emoji"
                    alt="Made in India"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Made with heart <img src="frontend_assets/images/emoji/heart.png" class="emoji"
                    alt="Made with heart"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Loved by 400+ Customers <img src="frontend_assets/images/emoji/Customers.png" class="emoji"
                    alt="Premium Branding Services"></div>

            <!-- DUPLICATED ITEMS FOR INFINITE LOOP -->
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Hand-painted fashion <img src="frontend_assets/images/emoji/Hand-painted-fashion.png"
                    class="emoji" alt="Hand Painted Fashion"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Custom Design <img src="frontend_assets/images/emoji/Custom-design.png" class="emoji"
                    alt="Custom Design"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Premium branding services <img src="frontend_assets/images/emoji/Premium.png" class="emoji"
                    alt="Premium Branding Services"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Made in India <img src="frontend_assets/images/emoji/india-flag.png" class="emoji"
                    alt="Made in India"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Made with heart <img src="frontend_assets/images/emoji/heart.png" class="emoji"
                    alt="Made with heart"></div>
            <div class="item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="26 -26 100 125">
                    <path fill="#ffffff" d="M114.3,1.1L63.8,52.3c-1.7,1.8-4.6,1.8-6.3,0L36.7,31c-1.7-1.8-1.7-4.6,0-6.4c1.7-1.8,4.6-1.8,6.3,0l17.7,18.1
                            l47.4-48c1.7-1.8,4.6-1.8,6.3,0C116.1-3.5,116.1-0.7,114.3,1.1z"></path>
                </svg> Loved by 400+ Customers <img src="frontend_assets/images/emoji/Customers.png" class="emoji"
                    alt="Premium Branding Services"></div>
        </div>
    </div>
</section>
<section class="bg-body-primary py-4" id="graphics-section">
    <div class="container">
        <div class="d-flex align-items-center reverse-sm">
            <div class="col">
                <div class="py-md-3 px-3">
                    <h1>Design That Speaks Your Identity</h1>
                    <p class="text-justify">At Design Dhaga, we see visual design as a powerful language one that expresses a brand’s values, personality, and purpose. From logos and brand identities to digital creatives, every design is crafted from scratch through close collaboration, ensuring it reflects you, not passing trends. Guided by your ideas and refined with intention, our work is personal, original, and meaningful designed to communicate your identity with clarity and authenticity.</p>
                    <ul class="unordered-list">
                        <li>Your ideas shape every detail</li>
                        <li>Created from scratch, with intention</li>
                        <li>Personal, original, and meaningful</li>
                        <li>Designed to reflect identity</li>
                    </ul>
                    <a class="btn btn-outline-secondary view-all-btn mt-2" href="{{ route('contact-us') }}#form">Customize Now</a>
                </div>
            </div>
            <div class="col text-md-right text-small-center">
                <img src="frontend_assets/images/graphics-image.png" alt="Custmize Now" class="customize-image" />
            </div>
        </div>
    </div>
</section>
<section class="container-fluid py-3" id="who-we-are">
    <div class="row px-4">
        <h2 class="mb-0">Where Art, Fabric & Design Come Together</h2>
        <p class="mt-2">A closer look at who we are.</p>
    </div>

    <div class="row mt-2 px-3">
        <div id="whoWeAreSlider">
            <div class="owl-carousel owl-theme">

                <div class="item">
                    <img src="frontend_assets/images/our-story.jpg" class="w-100 border rounded" alt="Our Story">
                    <a href="{{ route('about-us') }}">
                        <div class="item-box ">
                            <span>Our Story</span>
                        </div>
                    </a>
                </div>

                <div class="item">
                    <img src="frontend_assets/images/hand-painted-portfolio.jpg" class="w-100 border rounded" alt="Hand-Painted Portfolio">
                    <a href="{{ route('portfolio') }}">
                        <div class="item-box ">
                            <span>Hand-Painted Portfolio</span>
                        </div>
                    </a>
                </div>

                <div class="item">
                    <img src="frontend_assets/images/our-online-store.jpg" class="w-100 border rounded" alt="Our Online Store">
                    <a href="#">
                        <div class="item-box ">
                            <span>Our Online Store</span>
                        </div>
                    </a>
                </div>

                <div class="item">
                    <img src="frontend_assets/images/graphic-design-portfolio.jpg" class="w-100 border rounded" alt="Graphic Design Portfolio">
                    <a href="{{ route('portfolio') }}">
                        <div class="item-box">
                            <span>Graphic Design Portfolio</span>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
<section class="bg-body-primary py-3" id="inspired-by-art">
    <div class="container">
        <div class="row">
            <h2 class="text-center">Inspired By Art, <strong>Powered By Design</strong></h2>
            <p class="text-center">Read the Story and Meet The Makers</p>
        </div>
        <div class="row">
            <div class="col text-center">
                <img alt="Timeless" src="frontend_assets/images/icons/TimeLess icon.svg" />
                <h4>Timeless</h4>
            </div>
            <div class="col text-center">
                <img alt="Timeless" src="frontend_assets/images/icons/Easy Icon.svg" />
                <h4>Easy</h4>
            </div>
            <div class="col text-center">
                <img alt="Timeless" src="frontend_assets/images/icons/Honest icon.svg" />
                <h4>Honest</h4>
            </div>
        </div>
        <div class="row text-center mt-4">
            <p>Together, let's discover a better life</p>
            <p class="mb-0">#DESIGNDHAGA #HANDPAINT #GRAPHICS</p>
        </div>
    </div>
</section>
<section class="container-fluid py-3" id="who-we-are">
    <div class="row px-3">
        <h3 class="text-center mb-4">What People Say About Us</h3>
    </div>

    <div class="row px-3">
        <div class="owl-carousel owl-theme testimonials-carousel">

            <!-- ITEM 1 -->
            <div class="item">
                <div class="testimonial-card">
                    <div class="testimonial-img">
                        <img src="frontend_assets/images/testimonials/meena.jpeg" alt="Meena">
                    </div>
                    <div class="testimonial-content">
                        <div class="rating-badge">
                            <span><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i></span>
                        </div>
                        <h4>Meena</h4>
                        <p>
                            Awesome experience, you also try friends. Beautiful hand-painted suit
                            I bought from Design Dhaga and it is fabulous 🫰
                            <br><br>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ITEM 2 -->
            <div class="item">
                <div class="testimonial-card">
                    <div class="testimonial-img">
                        <img src="frontend_assets/images/testimonials/priyanka.jpg" alt="Priyanka">
                    </div>
                    <div class="testimonial-content">
                        <div class="rating-badge">
                            <span><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i></span>
                        </div>
                        <h4>Priyanka</h4>
                        <p>
                            I customized the same coord set for me and my daughter.
                            Seriously when we wear this hand-painted outfit,
                            everyone asks about the art and artist.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ITEM 3 -->
            <div class="item">
                <div class="testimonial-card">
                    <div class="testimonial-img">
                        <img src="frontend_assets/images/testimonials/sakshi.jpg" alt="Sakshi">
                    </div>
                    <div class="testimonial-content">
                        <div class="rating-badge">
                            <span><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i></span>
                        </div>
                        <h4>Sakshi</h4>
                        <p>
                            Amazing! Everyone should try this beautiful artistic dress
                            designed by Design Dhaga. Pure hand-painting and I love it 😘
                            <br><br>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ITEM 4 -->
            <div class="item">
                <div class="testimonial-card">
                    <div class="testimonial-img">
                        <img src="frontend_assets/images/testimonials/punam-mathur.jpeg" alt="Punam Mathur">
                    </div>
                    <div class="testimonial-content">
                        <div class="rating-badge">
                            <span><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i><i class="fas fa-star" style="color:gold"></i></span>
                        </div>
                        <h4>Punam Mathur</h4>
                        <p>
                            Excellent work and lovely colours. I have worn it 8–10 times
                            and the colours are exactly what I received.
                            Truly lovely customization 😘
                            <br><br>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script>
    $(document).ready(function() {
        $("#whoWeAreSlider .owl-carousel").owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            dots: true,
            autoplay: false,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            smartSpeed: 800,

            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                1200: {
                    items: 4
                }
            }
        });
    });

    $(document).ready(function() {
        $(".testimonials-carousel").owlCarousel({
            loop: true,
            margin: 0,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3500,
            autoplayHoverPause: true,
            smartSpeed: 800,

            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1200: {
                    items: 4
                }
            }
        });
    });
</script>
@endpush