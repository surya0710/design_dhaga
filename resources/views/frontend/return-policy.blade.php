@extends('frontend.layouts.app')
@section('title', 'Return Policy')

@section('meta_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')

@section('meta_keywords', 'hand-painted clothes, custom fashion, premium branding, design dhaga, fashion brand, handmade clothing, made in India')

@section('og_title', 'Design Dhaga - Hand-Painted Fashion')
@section('og_description', 'Design Dhaga is a premium fashion brand that offers hand-painted clothes, custom designs, and premium branding services. Our products are handcrafted in India and loved by 400+ customers.')
@section('og_image', asset('frontend_assets/images/og-home.jpg'))

    @section('content')
        <section class="policy-page py-5">
            <div class="container">
                <h1>No Return Policy – Design Dhaga</h1>

                <p>
                    At <strong>Design Dhaga</strong>, every product is handcrafted with precision,
                    creativity, and care. Due to the nature of our work—custom hand-painted fabrics,
                    personalized designs, and made-to-order creations—we follow a strict
                    <strong>No Return, No Exchange</strong> policy.
                </p>

                <h3>1. No Returns or Exchanges</h3>
                <p>
                    Once an order is placed and processed, it cannot be returned, replaced,
                    or exchanged under any circumstances, except in the case of damage
                    during transit.
                </p>

                <h3>2. Custom & Hand-Painted Products</h3>
                <p>
                    All our items are handcrafted and personalized as per customer requirements.
                    Slight variations in color, texture, or design are a natural part of hand painting
                    and are not considered defects. Therefore, returns for such reasons
                    will not be accepted.
                </p>

                <h3>3. Damage During Delivery</h3>
                <p>If the product arrives in a damaged condition, customers must:</p>
                <ul>
                    <li>Email us at <a href="mailto:artinfo@designdhaga.com">artinfo@designdhaga.com</a></li>
                    <li>Send an unboxing video along with photos within <strong>24 hours</strong> of delivery</li>
                </ul>
                <p>
                    If verified, we will provide a suitable resolution as per our policy.
                </p>

                <h3>4. Order Cancellation</h3>
                <p>
                    Orders once placed cannot be cancelled as work on custom designs
                    starts immediately after order confirmation.
                </p>

                <h3>5. Color & Display Disclaimer</h3>
                <p>
                    Colors may vary slightly due to screen settings, device differences,
                    or lighting conditions. Such variations do not qualify for returns
                    or refunds.
                </p>

                <h3>6. Customer Responsibility</h3>
                <p>
                    Customers are responsible for providing accurate measurements,
                    design references, spelling (for name-based artworks),
                    and delivery details at the time of ordering.
                    Design Dhaga is not responsible for errors made by the customer.
                </p>

                <hr>

                <p>
                    For any queries, please contact us at:
                    <a href="mailto:artinfo@designdhaga.com">artinfo@designdhaga.com</a>
                </p>
            </div>
        </section>
    @endsection