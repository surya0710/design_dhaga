@extends('frontend.layouts.app')

@section('title', 'Cart - Design Dhaga')

@section('content')

<main class="cart-page">
    <div class="container">

        <div class="text-center">
            <h1 class="cart-title">Your Cart</h1>
        </div>

        @if($cartItems->isEmpty())

            <div class="empty-cart-box">
                <div>
                    <h2 class="empty-cart-title">Your cart is empty</h2>
                    <p class="empty-cart-text">Looks like you haven’t added anything yet.</p>

                    <a href="{{ url('/') }}" class="btn btn-dark">
                        Continue Shopping
                    </a>
                </div>
            </div>

        @else

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-lg-8">
                <div class="cart-box p-4">

                    @foreach($cartItems as $item)
                    <div class="cart-item d-lg-flex justify-content-between align-items-center">

                        {{-- PRODUCT --}}
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ asset('storage/'.$item['image']) }}" class="product-image">

                            <div>
                                <h5 class="product-name">{{ $item['name'] }}</h5>

                                {{-- PRICE --}}
                                <div class="price-text">
                                    ₹{{ number_format($item['price'], 2) }}
                                </div>

                                <form method="POST" action="{{ route('cart.remove') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                    <button class="remove-btn">Remove</button>
                                </form>
                            </div>
                        </div>

                        {{-- QTY (+ -) --}}
                        <div>
                            <form method="POST" action="{{ route('cart.update') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">

                                <div class="qty-box">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, -1)">−</button>

                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="qty-input">

                                    <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                                </div>
                            </form>
                        </div>

                        {{-- TOTAL --}}
                        <div class="total-text">
                            ₹{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>

                    </div>
                    @endforeach

                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">
                <div class="summary-box">
                    <h4 class="summary-title">Order Summary</h4>

                    <div class="d-flex justify-content-between summary-row">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between summary-total">
                        <span>Total</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout') }}" class="btn btn-dark w-100 mt-4 checkout-btn">
                        Proceed to Checkout
                    </a>
                </div>
            </div>

        </div>

        @endif

    </div>
</main>

<script>
    function changeQty(button, change) {
        let input = button.parentElement.querySelector('input');
        let form = button.closest('form');

        let current = parseInt(input.value);
        let newQty = current + change;

        if (newQty < 1) return;

        input.value = newQty;

        // auto submit
        form.submit();
    }
</script>

@endsection