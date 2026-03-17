@extends('frontend.layouts.app')
@section('title', $category->meta_title)

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

    @push('extras')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/frontend_assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/frontend_assets/owl.theme.default.min.css">
    @endpush

    @section('content')
        <section class="m-2">
            <div class="container-fluid mt-4">
                <div class="row">
                    <h3 class="text-center">{{ $category->name }}</h3>
                </div>
                <div class="products-conatiner mt-2">
                    @if(count($products) > 0)
                        @foreach($products as $product)
                            <a class="product-item" href="{{ route('shop.product' , [$category->slug, $product->category->slug, $product->slug]) }}">
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="loaded">
                                <p>{{ $product->name }}</p>
                            </a>
                        @endforeach
                    @else
                        <p>There are no products to display.</p>
                    @endif
                </div>
            </div>
        </section>
    @endsection