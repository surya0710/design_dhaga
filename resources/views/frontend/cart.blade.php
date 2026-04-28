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
                    <p class="empty-cart-text">Looks like you haven't added anything yet.</p>

                    <a href="{{ url('/') }}" class="btn btn-dark">
                        Continue Shopping
                    </a>
                </div>
            </div>

        @else

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-lg-8">
                <div class="cart-box p-2">

                    @foreach($cartItems as $item)
                    <div class="cart-item d-lg-flex justify-content-between align-items-center gap-2">

                        {{-- PRODUCT --}}
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ asset('storage/'.$item['image']) }}" class="product-image">

                            <div>
                                <h5 class="product-name mb-0 mt-0">{{ $item['name'] }}</h5>

                                {{-- PRICE --}}
                                <div class="price-text">
                                    ₹{{ number_format($item['price'], 2) }}
                                </div>

                                <form method="POST" class="d-none d-sm-block" action="{{ route('cart.remove') }}" id="remove-product-{{ $item['id'] }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                    <button class="remove-btn remove-item-cart">Remove</button>
                                </form>
                            </div>
                        </div>

                        {{-- QTY (+ -) --}}
                        <div class="d-flex justify-content-space-between align-items-center">
                            <form method="POST" action="{{ route('cart.update') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">

                                <div class="qty-box mt-0">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, -1)">−</button>

                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" class="qty-input">

                                    <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                                </div>
                            </form>
                            <form method="POST" class="d-sm-block d-md-none" action="{{ route('cart.remove') }}" id="remove-product-{{ $item['id'] }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                <button class="remove-btn remove-item-cart">
                                    <i class="fa fa-trash"></i>&nbsp;
                                    Remove
                                </button>
                            </form>
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

                    {{-- DISCOUNT ROW (shows only when coupon is applied) --}}
                    @if(session('coupon'))
                    <div class="d-flex justify-content-between summary-row">
                        <span style="color: green;">Discount</span>
                        <span style="color: green;">−₹{{ number_format(session('coupon.discount'), 2) }}</span>
                    </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between summary-total">
                        <span>Total</span>
                        <span>₹{{ number_format($total - (session('coupon') ? session('coupon.discount') : 0), 2) }}</span>
                    </div>

                    {{-- COUPON SECTION --}}
                    <div class="mt-3" id="coupon-section">

                        @if(session('coupon'))

                            {{-- APPLIED STATE --}}
                            <div class="d-flex align-items-center justify-content-between p-2 rounded"
                                 style="background-color: #f0fff4; border: 1px solid #b7ebc0;">
                                <span style="color: green; font-size: 13px; font-weight: 500;">
                                    ✓ {{ session('coupon.code') }} applied
                                </span>
                                <form method="POST" action="{{ route('coupon.remove') }}">
                                    @csrf
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </div>

                        @else

                            {{-- INPUT STATE --}}
                            <form method="POST" action="{{ route('coupon.apply') }}" class="d-flex gap-1">
                                @csrf
                                <input type="text" name="code" placeholder="Enter coupon code" class="form-control form-control-sm" 
                                style="text-transform: uppercase; letter-spacing: 0.05em;" value="{{ old('code') }}" >
                                @error('code')
                                    <p class="mt-1" style="font-size: 12px; color: red;">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="btn btn-outline-dark btn-sm" style="white-space: nowrap;">
                                    Apply
                                </button>
                            </form>

                            @error('code')
                                <p class="mt-1" style="font-size: 12px; color: red;">{{ $message }}</p>
                            @enderror

                        @endif

                    </div>
                    {{-- END COUPON SECTION --}}

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
        let updateForm = button.closest('form');

        let current = parseInt(input.value);
        let newQty = current + change;

        let productId = updateForm.querySelector('input[name="product_id"]').value;

        // 👉 If quantity becomes 0 → remove item
        if (newQty <= 0) {
            let removeForm = document.getElementById('remove-product-' + productId);

            if (removeForm) {
                removeForm.submit();
            } else {
                console.error('Remove form not found for product:', productId);
            }
            return;
        }

        input.value = newQty;

        // auto submit update
        updateForm.submit();
    }
</script>

@endsection