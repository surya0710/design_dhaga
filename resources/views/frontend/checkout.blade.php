@extends('frontend.layouts.app')

@section('title', 'Checkout - Design Dhaga')

@section('content')

<style>
    .checkout-page {
        min-height: 78vh;
        padding: 40px 0 60px;
    }

    .checkout-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 30px;
    }

    .checkout-card,
    .order-summary-card {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .checkout-card {
        padding: 32px;
    }

    .order-summary-card {
        padding: 28px;
        position: sticky;
        top: 30px;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 22px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control,
    textarea.form-control {
        border-radius: 12px;
        border: 1px solid #ddd;
        min-height: 48px;
        padding: 12px 14px;
        box-shadow: none !important;
    }

    textarea.form-control {
        min-height: 110px;
        resize: none;
    }

    .form-control:focus {
        border-color: #222;
    }

    .summary-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 20px;
    }

    .summary-item {
        display: flex;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-item-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #eee;
        background: #fafafa;
    }

    .summary-item-name {
        font-size: 0.97rem;
        font-weight: 600;
        color: #222;
        margin-bottom: 4px;
        line-height: 1.4;
    }

    .summary-item-meta {
        font-size: 0.9rem;
        color: #777;
    }

    .summary-price {
        margin-left: auto;
        white-space: nowrap;
        font-weight: 600;
        color: #222;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: #444;
        font-size: 0.98rem;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.08rem;
        font-weight: 700;
        color: #111;
    }

    .online-pay-box {
        border: 1px solid #ececec;
        border-radius: 14px;
        padding: 14px 16px;
        background: #fcfcfc;
        margin: 24px 0 18px;
    }

    .online-pay-title {
        font-weight: 700;
        color: #222;
        margin-bottom: 4px;
    }

    .online-pay-desc {
        color: #777;
        font-size: 0.92rem;
        margin: 0;
    }

    .place-order-btn {
        width: 100%;
        border-radius: 12px;
        padding: 13px;
        font-size: 1rem;
        font-weight: 600;
    }

    .secure-note {
        font-size: 0.9rem;
        color: #777;
        text-align: center;
        margin-top: 12px;
    }

    .required-mark {
        color: #c0392b;
    }

    .checkout-note {
        color: #6f6f6f;
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 0;
    }

    @media (max-width: 991px) {
        .order-summary-card {
            position: static;
            margin-top: 24px;
        }

        .checkout-card {
            padding: 22px;
        }
    }
</style>

<main class="checkout-page">
    <div class="container">
        <div class="text-center">
            <h1 class="checkout-title">Checkout</h1>
        </div>

        <form id="checkout-form">
            @csrf

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="checkout-card">
                        <h4 class="section-title">Billing Details</h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="required-mark">*</span></label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control"
                                    value="{{ old('name', $defaultAddress->full_name ?? (auth()->check() ? auth()->user()->name : '')) }}"
                                    placeholder="Enter your full name"
                                >
                                <small class="text-danger error-text" data-error="name"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="required-mark">*</span></label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                                    placeholder="Enter your email"
                                >
                                <small class="text-danger error-text" data-error="email"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="required-mark">*</span></label>
                                <input
                                    type="text"
                                    name="phone"
                                    id="phone"
                                    class="form-control"
                                    value="{{ old('phone', $defaultAddress->phone ?? (auth()->check() ? (auth()->user()->phone ?? '') : '')) }}"
                                    placeholder="Enter your phone number"
                                >
                                <small class="text-danger error-text" data-error="phone"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">City <span class="required-mark">*</span></label>
                                <input
                                    type="text"
                                    name="city"
                                    id="city"
                                    class="form-control"
                                    value="{{ old('city', $defaultAddress->city ?? '') }}"
                                    placeholder="Enter your city"
                                >
                                <small class="text-danger error-text" data-error="city"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">State <span class="required-mark">*</span></label>
                                <input
                                    type="text"
                                    name="state"
                                    id="state"
                                    class="form-control"
                                    value="{{ old('state', $defaultAddress->state ?? '') }}"
                                    placeholder="Enter your state"
                                >
                                <small class="text-danger error-text" data-error="state"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode <span class="required-mark">*</span></label>
                                <input
                                    type="text"
                                    name="pincode"
                                    id="pincode"
                                    class="form-control"
                                    value="{{ old('pincode', $defaultAddress->pincode ?? '') }}"
                                    placeholder="Enter pincode"
                                >
                                <small class="text-danger error-text" data-error="pincode"></small>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Full Address <span class="required-mark">*</span></label>
                                <textarea
                                    name="address"
                                    id="address"
                                    class="form-control"
                                    placeholder="House no, street, area, landmark..."
                                >{{ old('address', $defaultAddress->full_address ?? '') }}</textarea>
                                <small class="text-danger error-text" data-error="address"></small>
                            </div>
                        </div>

                        <div class="online-pay-box">
                            <div class="online-pay-title">Online Payment Only</div>
                            <p class="online-pay-desc">Securely pay using Razorpay with UPI, cards, netbanking and supported wallets.</p>
                        </div>

                        <p class="checkout-note">
                            Please review your billing details carefully before proceeding to payment.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <h4 class="summary-title">Order Summary</h4>

                        @foreach($cartItems as $item)
                            <div class="summary-item">
                                <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="summary-item-image">

                                <div>
                                    <div class="summary-item-name">{{ $item['name'] }}</div>
                                    <div class="summary-item-meta">Qty: {{ $item['quantity'] }}</div>
                                </div>

                                <div class="summary-price">
                                    ₹{{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </div>

                            <div class="summary-row">
                                <span>GST</span>
                                <span>₹{{ number_format($gstData['gst_amount'], 2) }}</span>
                            </div>

                            <div class="summary-row">
                                <span>Shipping</span>
                                <span>{{ $shipping == 0 ? 'Free' : '₹'.number_format($shipping, 2) }}</span>
                            </div>

                            <hr>

                            <div class="summary-total">
                                <span>Total</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>

                            <button type="button" id="rzp-pay-btn" class="btn btn-dark place-order-btn mt-4">
                                Place Order
                            </button>

                            <div class="secure-note">
                                Safe and secure payment via Razorpay
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    document.getElementById('rzp-pay-btn').addEventListener('click', function () {
        const button = this;
        button.disabled = true;
        button.innerText = 'Please wait...';

        document.querySelectorAll('.error-text').forEach(function(el) {
            el.innerText = '';
        });

        let form = document.getElementById('checkout-form');
        let formData = new FormData(form);

        fetch("{{ route('razorpay.order') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    Object.keys(data.errors).forEach(function (key) {
                        const errorBox = document.querySelector('[data-error="' + key + '"]');
                        if (errorBox) {
                            errorBox.innerText = data.errors[key][0];
                        }
                    });
                } else {
                    alert(data.message || 'Something went wrong.');
                }

                button.disabled = false;
                button.innerText = 'Place Order';
                return;
            }

            let options = {
                key: data.key,
                amount: data.amount,
                currency: data.currency,
                name: "Design Dhaga",
                description: "Order Payment",
                image: "{{ asset('frontend/images/logo.png') }}",
                order_id: data.razorpay_order_id,
                handler: function (response) {
                    fetch("{{ route('razorpay.verify') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                            local_order_id: data.local_order_id
                        })
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.status) {
                            window.location.href = result.redirect_url;
                        } else {
                            alert(result.message || 'Payment verification failed.');
                            button.disabled = false;
                            button.innerText = 'Place Order';
                        }
                    })
                    .catch(() => {
                        alert('Payment verification failed.');
                        button.disabled = false;
                        button.innerText = 'Place Order';
                    });
                },
                prefill: {
                    name: data.customer.name,
                    email: data.customer.email,
                    contact: data.customer.phone
                },
                theme: {
                    color: "#111111"
                },
                modal: {
                    ondismiss: function () {
                        button.disabled = false;
                        button.innerText = 'Place Order';
                    }
                }
            };

            let rzp = new Razorpay(options);
            rzp.open();
        })
        .catch(() => {
            alert('Unable to initiate payment.');
            button.disabled = false;
            button.innerText = 'Place Order';
        });
    });
</script>

@endsection