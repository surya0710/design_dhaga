@extends('frontend.layouts.app')

@section('title', 'Cart - Design Dhaga')

@section('content')

<main>
  <div class="cart-header text-center py-4">
    <h1>Your Cart</h1>
  </div>

  <div class="container">

    @if($cartItems->isEmpty())

        {{-- EMPTY CART --}}
        <div class="text-center py-5">
            <h2>Your cart is empty</h2>
            <p class="text-muted">Looks like you haven’t added anything yet.</p>

            <a href="{{ url('/') }}" class="btn btn-dark mt-3">
                Continue Shopping
            </a>
        </div>

    @else

    <div class="row">

      {{-- LEFT --}}
      <div class="col-lg-8">

        @foreach($cartItems as $item)
        <div class="cart-item border-bottom py-3 d-flex justify-content-between align-items-center">

          {{-- PRODUCT --}}
          <div class="d-flex gap-3 align-items-center">
            <img src="{{ asset('storage/'.$item['image']) }}" width="80">

            <div>
              <h5>{{ $item['name'] }}</h5>

              {{-- REMOVE --}}
              <form method="POST" action="{{ route('cart.remove') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                <button class="btn btn-sm text-danger">Remove</button>
              </form>
            </div>
          </div>

          {{-- PRICE --}}
          <div>
            ₹{{ $item['price'] }}
          </div>

          {{-- QTY --}}
          <div>
            <form method="POST" action="{{ route('cart.update') }}">
              @csrf
              <input type="hidden" name="product_id" value="{{ $item['id'] }}">
              <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width:60px;">
              <button class="btn btn-sm btn-dark">Update</button>
            </form>
          </div>

          {{-- TOTAL --}}
          <div>
            ₹{{ $item['price'] * $item['quantity'] }}
          </div>

        </div>
        @endforeach

      </div>

      {{-- RIGHT --}}
      <div class="col-lg-4">

        <div class="border p-4">
          <h4>Order Summary</h4>

          <div class="d-flex justify-content-between">
            <span>Subtotal</span>
            <span>₹{{ $subtotal }}</span>
          </div>

          <hr>

          <div class="d-flex justify-content-between">
            <strong>Total</strong>
            <strong>₹{{ $total }}</strong>
          </div>

          <a href="{{ route('checkout') }}" class="btn btn-dark w-100 mt-3" disabled>
            Checkout
          </a>
        </div>

      </div>

    </div>

    @endif

  </div>
</main>

@endsection