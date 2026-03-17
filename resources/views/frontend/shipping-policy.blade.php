@extends('frontend.layouts.app')
@section('title', 'Order & Shipping Policy')

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

    @section('content')
        <section class="policy-page py-5">
            <div class="container">
                <h1>Order & Shipping Policy – Design Dhaga</h1>

                <p>For shipping, we use reliable courier company to ship the products. Usually the courier partner will call you before delivery. If you are not available on delivery, you can call them back and arrange a new delivery or you can pick it up at their location.</p>
                <p>We will not be responsible for any delay caused by any unforeseen delay by courier companies in delivering packages to the customer, or if the customer is unavailable at the time of delivery.</p>

                <hr>

                <p>
                    For any queries, please contact us at:
                    <a href="mailto:artinfo@designdhaga.com">artinfo@designdhaga.com</a>
                </p>
            </div>
        </section>
    @endsection