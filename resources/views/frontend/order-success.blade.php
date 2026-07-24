@extends('frontend.layouts.app')

@section('title', 'Order Confirmed - Design Dhaga')

@section('content')
@php
    $itemCount = $order->items->sum('quantity');
    $paidAt = $order->paid_at ?? $order->created_at;
@endphp

<style>
    .os-page {
        --ink: #111111;
        --ink-soft: #444444;
        --ink-muted: #888888;
        --surface: #ffffff;
        --surface-raised: #f7f6f4;
        --border: #e5e4e2;
        --success: #1a7a4a;
        --success-soft: #e8f5ee;
        background: var(--surface-raised);
        padding: 2rem 0 3.5rem;
        min-height: 70vh;
    }

    .os-wrap { max-width: 920px; }

    .os-hero {
        text-align: center;
        margin-bottom: 1.75rem;
        animation: osFadeUp 0.55s ease both;
    }

    .os-check {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: var(--success-soft);
        color: var(--success);
        display: grid;
        place-items: center;
        animation: osPop 0.5s cubic-bezier(0.2, 0.9, 0.3, 1.2) both;
    }

    .os-check svg { width: 28px; height: 28px; }

    .os-hero h1 {
        font-family: var(--font-display, Georgia, serif);
        font-size: clamp(1.6rem, 3vw, 2.1rem);
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -0.02em;
        margin: 0 0 0.4rem;
    }

    .os-hero p {
        color: var(--ink-muted);
        margin: 0 auto;
        max-width: 34rem;
        line-height: 1.55;
        font-size: 0.98rem;
    }

    .os-hero strong { color: var(--ink-soft); font-weight: 600; }

    .os-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.5rem 0.75rem;
        margin-top: 1.1rem;
        animation: osFadeUp 0.55s ease 0.08s both;
    }

    .os-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--ink-soft);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
    }

    .os-chip i { font-size: 0.75rem; opacity: 0.75; }

    .os-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 1rem;
        animation: osFadeUp 0.55s ease 0.14s both;
    }

    @media (max-width: 767.98px) {
        .os-grid { grid-template-columns: 1fr; }
        .os-page { padding-top: 1.25rem; }
    }

    .os-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.15rem 1.2rem;
    }

    .os-card-title {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--ink-muted);
        margin: 0 0 0.9rem;
    }

    .os-item {
        display: flex;
        gap: 0.9rem;
        padding: 0.85rem 0;
        border-bottom: 1px solid var(--border);
    }

    .os-item:first-of-type { padding-top: 0; }
    .os-item:last-child { border-bottom: 0; padding-bottom: 0; }

    .os-item img {
        width: 72px;
        height: 90px;
        object-fit: cover;
        border-radius: 10px;
        background: var(--surface-raised);
        flex-shrink: 0;
    }

    .os-item-body { flex: 1; min-width: 0; }

    .os-item-name {
        font-size: 0.95rem;
        font-weight: 650;
        color: var(--ink);
        margin: 0 0 0.25rem;
        line-height: 1.35;
    }

    .os-item-meta {
        font-size: 0.8rem;
        color: var(--ink-muted);
        line-height: 1.45;
        margin-bottom: 0.35rem;
    }

    .os-item-row {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        font-size: 0.86rem;
        color: var(--ink-soft);
    }

    .os-item-row strong { color: var(--ink); font-weight: 700; }

    .os-address {
        font-style: normal;
        color: var(--ink-soft);
        font-size: 0.92rem;
        line-height: 1.55;
        margin: 0;
    }

    .os-address .os-name {
        color: var(--ink);
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    .os-address .os-contact {
        margin-top: 0.55rem;
        font-size: 0.84rem;
        color: var(--ink-muted);
    }

    .os-summary-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.4rem 0;
        font-size: 0.9rem;
        color: var(--ink-soft);
    }

    .os-summary-row.is-discount { color: var(--success); }
    .os-summary-row.is-total {
        border-top: 1px solid var(--border);
        margin-top: 0.45rem;
        padding-top: 0.75rem;
        color: var(--ink);
        font-size: 1.02rem;
        font-weight: 700;
    }

    .os-note {
        margin-top: 0.9rem;
        padding: 0.75rem 0.85rem;
        background: var(--surface-raised);
        border-radius: 10px;
        font-size: 0.82rem;
        color: var(--ink-soft);
        line-height: 1.45;
    }

    .os-note i { color: var(--ink-muted); margin-right: 0.25rem; }

    .os-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        justify-content: center;
        margin-top: 1.5rem;
        animation: osFadeUp 0.55s ease 0.22s both;
    }

    .os-actions .btn {
        min-width: 150px;
        border-radius: 10px;
        padding: 0.65rem 1.1rem;
        font-weight: 600;
    }

    @keyframes osFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes osPop {
        from { opacity: 0; transform: scale(0.7); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<main class="os-page">
    <div class="container os-wrap">
        <div class="os-hero">
            <div class="os-check" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <h1>Thank you for your order</h1>
            <p>
                Order <strong>#{{ $order->id }}</strong> is confirmed.
                A receipt was sent to <strong>{{ $order->email }}</strong>.
            </p>
            <div class="os-meta">
                <span class="os-chip"><i class="fa-regular fa-calendar"></i> {{ optional($paidAt)->format('d M Y, g:i A') }}</span>
                <span class="os-chip"><i class="fa-solid fa-check"></i> {{ ucfirst($order->payment_status) }}</span>
                <span class="os-chip"><i class="fa-solid fa-bag-shopping"></i> {{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'items' }}</span>
                @if($order->expected_delivery_date)
                    <span class="os-chip"><i class="fa-regular fa-clock"></i> Delivery by {{ $order->expected_delivery_date->format('d M Y') }}</span>
                @endif
            </div>
        </div>

        <div class="os-grid">
            <div class="d-flex flex-column gap-3">
                <section class="os-card">
                    <h2 class="os-card-title">Items ordered</h2>
                    @foreach($order->items as $item)
                        @php
                            $image = $item->product_image
                                ? asset('storage/' . ltrim($item->product_image, '/'))
                                : asset('frontend_assets/images/logo/favicon.jpg');
                        @endphp
                        <div class="os-item">
                            <img src="{{ $image }}" alt="{{ $item->product_name }}">
                            <div class="os-item-body">
                                <h3 class="os-item-name">{{ $item->product_name }}</h3>
                                @if($item->fabric_type || $item->size || $item->sku)
                                    <div class="os-item-meta">
                                        @if($item->fabric_type) Fabric: {{ $item->fabric_type }}@endif
                                        @if($item->size){{ $item->fabric_type ? ' · ' : '' }}Size: {{ $item->size }}@endif
                                        @if($item->sku){{ ($item->fabric_type || $item->size) ? ' · ' : '' }}SKU: {{ $item->sku }}@endif
                                    </div>
                                @endif
                                <div class="os-item-row">
                                    <span>Qty {{ $item->quantity }} × ₹{{ number_format((float) $item->price, 2) }}</span>
                                    <strong>₹{{ number_format((float) $item->total, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </section>

                <section class="os-card">
                    <h2 class="os-card-title">Delivery address</h2>
                    <address class="os-address">
                        <div class="os-name">{{ $order->name }}</div>
                        <div>{{ $order->full_address }}</div>
                        <div class="os-contact">
                            @if($order->phone)<div><i class="fa-solid fa-phone"></i> {{ $order->phone }}</div>@endif
                            @if($order->email)<div><i class="fa-regular fa-envelope"></i> {{ $order->email }}</div>@endif
                        </div>
                    </address>
                    @if($order->delivery_label || $order->courier_name || $order->delivery_eta)
                        <div class="os-note">
                            <i class="fa-solid fa-truck"></i>
                            @if($order->delivery_label){{ $order->delivery_label }}@endif
                            @if($order->courier_name){{ $order->delivery_label ? ' · ' : '' }}{{ $order->courier_name }}@endif
                            @if($order->delivery_eta){{ ($order->delivery_label || $order->courier_name) ? ' · ' : '' }}ETA {{ $order->delivery_eta }}@endif
                        </div>
                    @endif
                </section>
            </div>

            <div class="d-flex flex-column gap-3">
                <section class="os-card">
                    <h2 class="os-card-title">Payment summary</h2>
                    <div class="os-summary-row">
                        <span>Subtotal</span>
                        <span>₹{{ number_format((float) $order->subtotal, 2) }}</span>
                    </div>
                    @if((float) $order->coupon_discount > 0)
                        <div class="os-summary-row is-discount">
                            <span>Discount{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</span>
                            <span>−₹{{ number_format((float) $order->coupon_discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="os-summary-row">
                        <span>Shipping</span>
                        <span>
                            @php $shipping = (float) ($order->delivery_charge ?: $order->shipping); @endphp
                            {{ $shipping > 0 ? '₹'.number_format($shipping, 2) : 'Free' }}
                        </span>
                    </div>
                    @if((float) $order->gst_amount > 0)
                        <div class="os-summary-row">
                            <span>
                                @if(($order->gst_type ?? '') === 'cgst_sgst')
                                    GST (CGST {{ rtrim(rtrim(number_format((float)$order->cgst_rate, 2), '0'), '.') }}% + SGST {{ rtrim(rtrim(number_format((float)$order->sgst_rate, 2), '0'), '.') }}%)
                                @else
                                    GST{{ $order->igst_rate ? ' (IGST '.rtrim(rtrim(number_format((float)$order->igst_rate, 2), '0'), '.').'%)' : '' }}
                                @endif
                            </span>
                            <span>₹{{ number_format((float) $order->gst_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="os-summary-row is-total">
                        <span>Total paid</span>
                        <span>₹{{ number_format((float) $order->total, 2) }}</span>
                    </div>
                    <div class="os-note">
                        <i class="fa-solid fa-credit-card"></i>
                        Paid via {{ ucfirst($order->payment_method ?? 'Razorpay') }}
                        @if($order->razorpay_payment_id)
                            · Ref {{ $order->razorpay_payment_id }}
                        @endif
                    </div>
                </section>

                <section class="os-card">
                    <h2 class="os-card-title">What’s next</h2>
                    <div class="os-note mb-0" style="margin-top:0;">
                        We’ll pack your order and email shipping updates to <strong>{{ $order->email }}</strong>.
                        You can track progress anytime from your account.
                    </div>
                </section>
            </div>
        </div>

        <div class="os-actions">
            <a href="{{ route('home') }}" class="btn btn-dark">Continue shopping</a>
            <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-outline-dark" target="_blank">View invoice</a>
            <a href="{{ route('account.index') }}" class="btn btn-outline-dark">My orders</a>
        </div>
    </div>
</main>
@endsection

@push('scripts')
{{-- Google Customer Reviews: survey opt-in (order confirmation only) --}}
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
<script>
  window.renderOptIn = function() {
    window.gapi.load('surveyoptin', function() {
      window.gapi.surveyoptin.render({
        "merchant_id": {{ (int) $merchantId }},
        "order_id": @json((string) $order->id),
        "email": @json($order->email),
        "delivery_country": @json($deliveryCountry),
        "estimated_delivery_date": @json($estimatedDeliveryDate)
      });
    });
  };
</script>
@endpush
