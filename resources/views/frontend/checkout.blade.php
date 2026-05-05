@extends('frontend.layouts.app')

@section('title', 'Checkout - Design Dhaga')

@section('content')

<style>
    :root {
        --ink: #111111;
        --ink-soft: #444444;
        --ink-muted: #888888;
        --ink-faint: #bbbbbb;
        --surface: #ffffff;
        --surface-raised: #f7f6f4;
        --surface-hover: #f2f1ef;
        --border: #e5e4e2;
        --border-strong: #222222;
        --success: #1a7a4a;
        --error: #c0392b;
        --radius-sm: 8px;
        --radius-md: 14px;
        --radius-lg: 20px;
        --shadow-md: 0 4px 16px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
        --sticky-bar-height: 80px;
    }

    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: var(--font-body); }

    /* PAGE */
    .co-page {
        min-height: 100vh;
        background: var(--surface-raised);
        padding: 32px 0 calc(var(--sticky-bar-height) + 32px);
    }

    .co-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .co-header h1 {
        font-family: var(--font-display);
        font-size: 1.7rem;
        font-weight: 800;
        color: var(--ink);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .co-header-badge {
        background: var(--ink);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 3px 9px;
        border-radius: 100px;
    }

    /* PROGRESS */
    .co-progress {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 22px;
    }

    .co-progress-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--border);
        transition: all 0.3s ease;
    }

    .co-progress-dot.done { background: var(--success); }
    .co-progress-dot.active { background: var(--ink); width: 20px; border-radius: 3px; }

    .co-progress-label {
        font-size: 0.75rem;
        color: var(--ink-muted);
        margin-left: 6px;
        font-weight: 500;
    }

    /* ACCORDION STEPS */
    .co-steps { display: flex; flex-direction: column; gap: 10px; }

    .co-step {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .co-step.is-active {
        border-color: var(--border-strong);
        box-shadow: var(--shadow-md);
    }

    .co-step.is-done { border-color: var(--success); }

    .co-step-head {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 22px;
        user-select: none;
    }

    .co-step-num {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--surface-raised);
        border: 1.5px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display);
        font-weight: 800; font-size: 0.85rem;
        color: var(--ink-muted);
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .co-step.is-active .co-step-num { background: var(--ink); border-color: var(--ink); color: white; }
    .co-step.is-done .co-step-num { background: var(--success); border-color: var(--success); color: white; }
    .co-step-num svg { display: none; }
    .co-step.is-done .co-step-num svg { display: block; }
    .co-step.is-done .co-step-num span { display: none; }

    .co-step-title-wrap { flex: 1; min-width: 0; }

    .co-step-label {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--ink-faint);
        margin-bottom: 1px;
    }

    .co-step.is-active .co-step-label,
    .co-step.is-done .co-step-label { color: var(--ink-muted); }

    .co-step-title {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: var(--ink);
    }

    .co-step-summary {
        font-size: 0.82rem;
        color: var(--ink-muted);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .co-step-edit {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--ink-muted);
        background: var(--surface-raised);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 5px 12px;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
        display: none;
    }

    .co-step.is-done .co-step-edit { display: block; }
    .co-step-edit:hover { background: var(--surface-hover); border-color: var(--ink-soft); color: var(--ink); }

    .co-step-body {
        padding: 0 22px 24px;
    }

    /* FORM */
    .co-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    .co-field-full { grid-column: 1 / -1; }
    .co-field { display: flex; flex-direction: column; gap: 5px; }

    .co-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ink-soft);
        letter-spacing: 0.02em;
    }

    .co-label .req { color: var(--error); margin-left: 2px; }

    .co-input, .co-textarea {
        font-family: var(--font-body);
        font-size: 0.95rem;
        color: var(--ink);
        background: var(--surface-raised);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 11px 14px;
        outline: none;
        transition: border-color 0.15s, background 0.15s;
        width: 100%;
    }

    .co-input:focus, .co-textarea:focus {
        border-color: var(--border-strong);
        background: var(--surface);
    }

    .co-input.has-error, .co-textarea.has-error {
        border-color: var(--error);
        background: #fff8f7;
    }

    .co-textarea { min-height: 88px; resize: none; }

    .co-error {
        font-size: 0.77rem;
        color: var(--error);
        font-weight: 500;
        min-height: 16px;
        display: block;
    }

    /* CONTINUE BUTTON */
    .co-continue-btn {
        font-family: var(--font-display);
        font-size: 0.92rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        background: var(--ink);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 13px 28px;
        cursor: pointer;
        transition: opacity 0.15s, transform 0.1s;
        margin-top: 6px;
    }

    .co-continue-btn:hover { opacity: 0.88; }
    .co-continue-btn:active { transform: scale(0.98); }
    .co-continue-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

    /* DELIVERY OPTIONS */
    .delivery-loading {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--ink-muted);
        font-size: 0.9rem;
        padding: 12px 0;
    }

    .delivery-spinner {
        width: 18px; height: 18px;
        border: 2px solid var(--border);
        border-top-color: var(--ink);
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .delivery-opts {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .delivery-opt {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: var(--surface-raised);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px 18px;
        cursor: pointer;
        transition: all 0.18s ease;
        position: relative;
    }

    .delivery-opt:hover { border-color: var(--ink-soft); background: var(--surface-hover); }

    .delivery-opt.is-selected {
        border-color: var(--ink);
        background: var(--surface);
        box-shadow: 0 0 0 3px rgba(17,17,17,0.06);
    }

    .delivery-opt-radio {
        width: 18px; height: 18px;
        border: 2px solid var(--ink-faint);
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 2px;
        display: flex;
        align-items: center; justify-content: center;
        transition: border-color 0.15s;
    }

    .delivery-opt.is-selected .delivery-opt-radio { border-color: var(--ink); }

    .delivery-opt-radio::after {
        content: '';
        width: 8px; height: 8px;
        background: var(--ink);
        border-radius: 50%;
        opacity: 0;
        transform: scale(0);
        transition: all 0.15s;
    }

    .delivery-opt.is-selected .delivery-opt-radio::after { opacity: 1; transform: scale(1); }

    .delivery-opt-info { flex: 1; }

    .delivery-opt-name {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 3px;
    }

    .delivery-opt-meta { font-size: 0.82rem; color: var(--ink-muted); line-height: 1.5; }

    .delivery-opt-price {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 800;
        color: var(--ink);
        flex-shrink: 0;
    }

    .delivery-opt-price.is-free { color: var(--success); }

    .delivery-opt-badge {
        position: absolute;
        top: -1px; right: 14px;
        background: var(--ink);
        color: white;
        font-size: 0.63rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 0 0 6px 6px;
    }

    /* PAYMENT NOTICE */
    .payment-notice {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--surface-raised);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px 16px;
        margin-bottom: 16px;
    }

    .payment-notice-icon {
        width: 36px; height: 36px;
        background: var(--ink);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .payment-notice-text { font-size: 0.85rem; color: var(--ink-soft); line-height: 1.5; }
    .payment-notice-text strong { color: var(--ink); display: block; }

    /* ORDER SUMMARY */
    .co-summary-card {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 24px;
        position: sticky;
        top: 20px;
    }

    .co-summary-title {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .co-summary-title small {
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--ink-muted);
    }

    .co-summary-items { display: flex; flex-direction: column; }

    .co-summary-item {
        display: flex;
        gap: 11px;
        padding: 11px 0;
        border-bottom: 1px solid var(--border);
    }

    .co-summary-item:last-child { border-bottom: none; }

    .co-summary-img {
        width: 54px; height: 54px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        border: 1px solid var(--border);
        flex-shrink: 0;
    }

    .co-summary-item-info { flex: 1; min-width: 0; }

    .co-summary-item-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--ink);
        line-height: 1.4;
        margin-bottom: 3px;
    }

    .co-summary-item-qty { font-size: 0.78rem; color: var(--ink-muted); }

    .co-summary-item-price {
        font-family: var(--font-display);
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--ink);
        flex-shrink: 0;
    }

    .co-summary-breakdown { margin-top: 14px; display: flex; flex-direction: column; gap: 8px; }

    .co-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.86rem;
        color: var(--ink-soft);
    }

    .co-summary-row.is-discount { color: var(--success); }

    .co-summary-divider { border: none; border-top: 1px solid var(--border); margin: 6px 0; }

    .co-summary-total {
        display: flex;
        justify-content: space-between;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 800;
        color: var(--ink);
    }

    .secure-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.76rem;
        color: var(--ink-muted);
        margin-top: 10px;
    }

    /* STICKY BOTTOM BAR */
    .co-sticky-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 999;
        background: rgba(255,255,255,0.96);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-top: 1px solid var(--border);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 -8px 30px rgba(0,0,0,0.1);
    }

    .co-sticky-total { flex: 1; }

    .co-sticky-total-label {
        font-size: 0.7rem;
        color: var(--ink-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
    }

    .co-sticky-total-amount {
        font-family: var(--font-display);
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--ink);
        line-height: 1.2;
    }

    .co-place-order-btn {
        font-family: var(--font-display);
        font-size: 0.92rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        background: var(--ink);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 14px 26px;
        cursor: pointer;
        transition: opacity 0.15s, transform 0.1s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .co-place-order-btn:hover { opacity: 0.88; }
    .co-place-order-btn:active { transform: scale(0.97); }
    .co-place-order-btn:disabled { opacity: 0.3; cursor: not-allowed; transform: none; }

    /* DESKTOP: hide sticky, show inline btn */
    @media (min-width: 992px) {
        .co-sticky-bar { display: none; }
        .co-summary-place-btn-wrap { display: block; }
    }

    @media (max-width: 991px) {
        .co-summary-place-btn-wrap { display: none; }
        .co-form-grid { grid-template-columns: 1fr; }
        .co-summary-card { position: static; margin-top: 0; }
        .co-page { padding-top: 20px; }
    }
    #goToTop,
    .floating-whatsapp{
        display: none!important;
    }
</style>

@php
    $couponDiscount = $couponDiscount ?? 0;
    $isFreeShipping = session('coupon.free_shipping') ?? false;
    $taxableAmount = max($subtotal - $couponDiscount, 0);

    // Normalise gstData so Blade never throws on missing keys
    $gstData = $gstData ?? [];
    $gstData = array_merge([
        'gst_amount'  => 0,
        'gst_type'    => 'igst',
        'igst_rate'   => 0,
        'igst_amount' => 0,
        'cgst_rate'   => 0,
        'cgst_amount' => 0,
        'sgst_rate'   => 0,
        'sgst_amount' => 0,
    ], $gstData);

    // Recompute total defensively: taxable + gst + shipping
    $safeTotal = $taxableAmount + $gstData['gst_amount'] + ($shipping ?? 0);
@endphp

<main class="co-page">
    <div class="container">
        <form id="checkout-form">
            @csrf
            <input type="hidden" name="delivery_type" id="delivery_type">
            <input type="hidden" name="shipping_charge" id="shipping_charge" value="{{ $shipping ?? 0 }}">
            <input type="hidden" name="shiprocket_courier_id" id="shiprocket_courier_id">

            <div class="row g-3">

                {{-- LEFT: STEPS --}}
                <div class="col-lg-7">

                    <div class="co-steps">

                        {{-- STEP 1 --}}
                        <div class="co-step is-active" id="step-1">
                            <div class="co-step-head">
                                <div class="co-step-num">
                                    <span>1</span>
                                    <svg width="13" height="10" viewBox="0 0 13 10" fill="none"><path d="M1 5L5 9L12 1" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="co-step-title-wrap">
                                    <div class="co-step-label">Step 1</div>
                                    <div class="co-step-title">Delivery Address</div>
                                    <div class="co-step-summary" id="step-1-summary" style="display:none;"></div>
                                </div>
                                <button type="button" class="co-step-edit" onclick="editStep(1)">Edit</button>
                            </div>
                            <div class="co-step-body" id="step-1-body">
                                <div class="co-form-grid">
                                    <div class="co-field">
                                        <label class="co-label">Full Name <span class="req">*</span></label>
                                        <input type="text" name="name" id="name" class="co-input"
                                            value="{{ old('name', $defaultAddress->full_name ?? (auth()->check() ? auth()->user()->name : '')) }}"
                                            placeholder="Your full name">
                                        <span class="co-error" data-error="name"></span>
                                    </div>
                                    <div class="co-field">
                                        <label class="co-label">Email Address <span class="req">*</span></label>
                                        <input type="email" name="email" id="email" class="co-input"
                                            value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                                            placeholder="you@email.com">
                                        <span class="co-error" data-error="email"></span>
                                    </div>
                                    <div class="co-field">
                                        <label class="co-label">Phone Number <span class="req">*</span></label>
                                        <input type="text" name="phone" id="phone" class="co-input"
                                            value="{{ old('phone', $defaultAddress->phone ?? (auth()->check() ? (auth()->user()->phone ?? '') : '')) }}"
                                            placeholder="10-digit mobile number">
                                        <span class="co-error" data-error="phone"></span>
                                    </div>
                                    <div class="co-field">
                                        <label class="co-label">Pincode <span class="req">*</span></label>
                                        <input type="text" name="pincode" id="pincode" class="co-input"
                                            value="{{ old('pincode', $defaultAddress->pincode ?? '') }}"
                                            placeholder="6-digit pincode" maxlength="6">
                                        <span class="co-error" data-error="pincode"></span>
                                    </div>
                                    <div class="co-field">
                                        <label class="co-label">City <span class="req">*</span></label>
                                        <input type="text" name="city" id="city" class="co-input"
                                            value="{{ old('city', $defaultAddress->city ?? '') }}"
                                            placeholder="City">
                                        <span class="co-error" data-error="city"></span>
                                    </div>
                                    <div class="co-field">
                                        <label class="co-label">State <span class="req">*</span></label>
                                        <input type="text" name="state" id="state" class="co-input"
                                            value="{{ old('state', $defaultAddress->state ?? '') }}"
                                            placeholder="State">
                                        <span class="co-error" data-error="state"></span>
                                    </div>
                                    <div class="co-field co-field-full">
                                        <label class="co-label">Full Address <span class="req">*</span></label>
                                        <textarea name="address" id="address" class="co-textarea"
                                            placeholder="House no., street, area, landmark...">{{ old('address', $defaultAddress->full_address ?? '') }}</textarea>
                                        <span class="co-error" data-error="address"></span>
                                    </div>
                                </div>
                                <button type="button" class="co-continue-btn" id="btn-continue-1" onclick="continueToDelivery()">
                                    Continue to Delivery
                                </button>
                            </div>
                        </div>

                        {{-- STEP 2 --}}
                        <div class="co-step" id="step-2">
                            <div class="co-step-head">
                                <div class="co-step-num">
                                    <span>2</span>
                                    <svg width="13" height="10" viewBox="0 0 13 10" fill="none"><path d="M1 5L5 9L12 1" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="co-step-title-wrap">
                                    <div class="co-step-label">Step 2</div>
                                    <div class="co-step-title">Delivery Options</div>
                                    <div class="co-step-summary" id="step-2-summary" style="display:none;"></div>
                                </div>
                                <button type="button" class="co-step-edit" onclick="editStep(2)">Edit</button>
                            </div>
                            <div class="co-step-body" id="step-2-body" style="display:none;">
                                <div class="delivery-loading" id="delivery-loading">
                                    <div class="delivery-spinner"></div>
                                    <span>Fetching available options...</span>
                                </div>
                                <div class="delivery-opts" id="delivery-opts-wrap" style="display:none;"></div>
                                <span class="co-error" data-error="delivery_type" style="margin-top:6px;display:block;"></span>
                                <button type="button" class="co-continue-btn" id="btn-continue-2" onclick="continueToPayment()" style="margin-top:16px;">
                                    Continue to Payment
                                </button>
                            </div>
                        </div>

                        {{-- STEP 3 --}}
                        <div class="co-step" id="step-3">
                            <div class="co-step-head">
                                <div class="co-step-num">
                                    <span>3</span>
                                    <svg width="13" height="10" viewBox="0 0 13 10" fill="none"><path d="M1 5L5 9L12 1" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="co-step-title-wrap">
                                    <div class="co-step-label">Step 3</div>
                                    <div class="co-step-title">Payment</div>
                                </div>
                            </div>
                            <div class="co-step-body" id="step-3-body" style="display:none;">
                                <div class="payment-notice">
                                    <div class="payment-notice-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                            <rect x="2" y="6" width="20" height="14" rx="3" stroke="white" stroke-width="2"/>
                                            <path d="M2 10H22" stroke="white" stroke-width="2"/>
                                            <circle cx="7" cy="15" r="1.5" fill="white"/>
                                        </svg>
                                    </div>
                                    <div class="payment-notice-text">
                                        <strong>Razorpay Secure Checkout</strong>
                                        Pay with UPI, cards, net banking or wallets.
                                    </div>
                                </div>
                                <div id="gst-section"></div>
                                <div class="co-summary-place-btn-wrap" style="margin-top:18px;">
                                    <button type="button" id="rzp-pay-btn-desktop" class="co-place-order-btn" style="width:100%;" disabled>
                                        Pay &amp; Place Order
                                    </button>
                                    <div class="secure-badge" style="justify-content:center;margin-top:10px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 2L4 7v6c0 5.5 3.5 10.7 8 12 4.5-1.3 8-6.5 8-12V7L12 2z" stroke="currentColor" stroke-width="2"/></svg>
                                        100% secure &amp; encrypted
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- RIGHT: ORDER SUMMARY --}}
                <div class="col-lg-5">
                    <div class="co-summary-card">
                        <div class="co-summary-title">
                            Order Summary
                            <small>({{ count($cartItems) }} {{ count($cartItems) == 1 ? 'item' : 'items' }})</small>
                        </div>

                        <div class="co-summary-items">
                            @foreach($cartItems as $item)
                            <div class="co-summary-item">
                                <img src="{{ asset('storage/'. $item->product->image ) }}" class="co-summary-img" alt="{{ $item->product->name }}">
                                <div class="co-summary-item-info">
                                    <div class="co-summary-item-name">{{ $item->product->name }}</div>
                                    <div class="co-summary-item-qty">Qty: {{ $item['quantity'] }}</div>
                                </div>
                                <div class="co-summary-item-price">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                            </div>
                            @endforeach
                        </div>

                        <div class="co-summary-breakdown">
                            <div class="co-summary-row">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </div>

                            @if($couponDiscount > 0 && !$isFreeShipping)
                            <div class="co-summary-row is-discount">
                                <span>Coupon Discount</span>
                                <span>− ₹{{ number_format($couponDiscount, 2) }}</span>
                            </div>
                            @endif

                            <div class="co-summary-row" id="shipping-row">
                                <span>Shipping</span>
                                <span id="summary-shipping">
                                    {{ $isFreeShipping ? 'FREE' : (isset($shipping) && $shipping > 0 ? '₹'.number_format($shipping, 2) : '—') }}
                                </span>
                            </div>

                            <div id="gst-summary-section">
                                @if(isset($gstData) && $gstData['gst_amount'] > 0)
                                    @if($gstData['gst_type'] === 'cgst_sgst')
                                        <div class="co-summary-row">
                                            <span>CGST ({{ $gstData['cgst_rate'] }}%)</span>
                                            <span>₹{{ number_format($gstData['cgst_amount'], 2) }}</span>
                                        </div>
                                        <div class="co-summary-row">
                                            <span>SGST ({{ $gstData['sgst_rate'] }}%)</span>
                                            <span>₹{{ number_format($gstData['sgst_amount'], 2) }}</span>
                                        </div>
                                    @else
                                        <div class="co-summary-row">
                                            <span>IGST ({{ $gstData['igst_rate'] }}%)</span>
                                            <span>₹{{ number_format($gstData['igst_amount'], 2) }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <hr class="co-summary-divider">

                            <div class="co-summary-total">
                                <span>Total</span>
                                <span>₹<span id="summary-total">{{ number_format($safeTotal, 2) }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</main>

{{-- STICKY BAR (mobile) --}}
<div class="co-sticky-bar" id="co-sticky-bar">
    <div class="co-sticky-total">
        <div class="co-sticky-total-label">Total</div>
        <div class="co-sticky-total-amount">₹<span id="sticky-total">{{ number_format($safeTotal, 2) }}</span></div>
    </div>
    <button type="button" id="rzp-pay-btn-mobile" class="co-place-order-btn" disabled>
        Place Order
    </button>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    const subtotal = {{ (float) $subtotal }};
    const couponDiscount = {{ (float) ($couponDiscount ?? 0) }};
    const taxableAmount = {{ (float) $taxableAmount }};
    const isFreeShipping = {{ $isFreeShipping ? 'true' : 'false' }};
    let currentGstAmount = {{ (float) ($gstData['gst_amount'] ?? 0) }};
    let currentShippingCharge = {{ (float) ($shipping ?? 0) }};

    function fmt(v) { return Number(v).toFixed(2); }

    // ── SCROLL TO ELEMENT ─────────────────────────────────────
    function scrollToEl(el, offset) {
        if (!el) return;
        offset = offset || 80;
        const top = el.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
    }

    function scrollToFirstError() {
        const errs = document.querySelectorAll('.co-error');
        for (let i = 0; i < errs.length; i++) {
            if (errs[i].textContent.trim()) {
                scrollToEl(errs[i], 100);
                return;
            }
        }
    }

    // ── OPEN/CLOSE STEP BODIES ────────────────────────────────
    function showStepBody(num, show) {
        const body = document.getElementById('step-' + num + '-body');
        if (body) body.style.display = show ? '' : 'none';
    }

    function activateStep(num) {
        for (let i = 1; i <= 3; i++) {
            const el = document.getElementById('step-' + i);
            el.classList.remove('is-active');
            // Close bodies of non-active steps that aren't "done"
            if (i !== num && !el.classList.contains('is-done')) {
                showStepBody(i, false);
            }
        }
        const target = document.getElementById('step-' + num);
        target.classList.add('is-active');
        showStepBody(num, true);
        setTimeout(function() { scrollToEl(target, 80); }, 120);
    }

    function markDone(num, summaryText) {
        const el = document.getElementById('step-' + num);
        el.classList.remove('is-active');
        el.classList.add('is-done');
        const summary = document.getElementById('step-' + num + '-summary');
        if (summary && summaryText) {
            summary.textContent = summaryText;
            summary.style.display = 'block';
        }
        showStepBody(num, false);
    }

    function editStep(num) {
        for (let i = num; i <= 3; i++) {
            const el = document.getElementById('step-' + i);
            el.classList.remove('is-done', 'is-active');
            const summary = document.getElementById('step-' + i + '-summary');
            if (summary) summary.style.display = 'none';
        }

        // If editing step 1, reset delivery state
        if (num <= 1) {
            document.getElementById('delivery_type').value = '';
            document.getElementById('shiprocket_courier_id').value = '';
            document.getElementById('delivery-opts-wrap').innerHTML = '';
            document.getElementById('delivery-opts-wrap').style.display = 'none';
            document.getElementById('delivery-loading').style.display = 'flex';
            setPlaceOrderEnabled(false);
        }

        activateStep(num);
    }

    // ── CLEAR / SHOW ERRORS ───────────────────────────────────
    function clearErrors() {
        document.querySelectorAll('.co-error').forEach(function(el) { el.textContent = ''; });
        document.querySelectorAll('.co-input, .co-textarea').forEach(function(el) { el.classList.remove('has-error'); });
    }

    function showErrors(errors) {
        Object.keys(errors).forEach(function(key) {
            const errorEl = document.querySelector('[data-error="' + key + '"]');
            const inputEl = document.getElementById(key);
            if (errorEl) errorEl.textContent = errors[key][0];
            if (inputEl) inputEl.classList.add('has-error');
        });
        scrollToFirstError();
    }

    // ── STEP 1 CONTINUE ───────────────────────────────────────
    function continueToDelivery() {
        clearErrors();

        const fields = ['name', 'email', 'phone', 'pincode', 'city', 'state', 'address'];
        let hasError = false;

        fields.forEach(function(field) {
            const input = document.getElementById(field);
            if (!input || !input.value.trim()) {
                const errorEl = document.querySelector('[data-error="' + field + '"]');
                if (errorEl) errorEl.textContent = 'This field is required.';
                if (input) input.classList.add('has-error');
                hasError = true;
            }
        });

        if (hasError) { scrollToFirstError(); return; }

        const btn = document.getElementById('btn-continue-1');
        btn.disabled = true;
        btn.textContent = 'Checking...';

        const formData = new FormData(document.getElementById('checkout-form'));

        fetch("{{ route('checkout.delivery.options') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
            body: formData
        })
        .then(function(response) {
            var ok = response.ok;
            var status = response.status;
            return response.text().then(function(text) {
                var data;
                try { data = JSON.parse(text); } catch(e) {
                    throw new Error('JSON parse error (status ' + status + '): ' + text.substring(0, 200));
                }
                return { ok: ok, data: data };
            });
        })
        .then(function(result) {
            btn.disabled = false;
            btn.textContent = 'Continue to Delivery';

            if (!result.ok) {
                if (result.data.errors) showErrors(result.data.errors);
                else alert(result.data.message || 'Request failed.');
                return;
            }

            var data = result.data;

            // Guard: must have at least one delivery option
            if (!data.regular && !data.express) {
                alert('No delivery options returned for this address.');
                return;
            }

            const name = document.getElementById('name').value;
            const city = document.getElementById('city').value;
            const pincode = document.getElementById('pincode').value;
            markDone(1, name + ' · ' + city + ' – ' + pincode);

            renderDeliveryOptions(data);
            fetchGst();
            activateStep(2);
            setPlaceOrderEnabled(true);
        })
        .catch(function(err) {
            btn.disabled = false;
            btn.textContent = 'Continue to Delivery';
            console.error('Delivery fetch error:', err);
            alert('Error: ' + (err.message || 'Unable to fetch delivery options.'));
        });
    }

    // ── RENDER DELIVERY OPTIONS ───────────────────────────────
    function renderDeliveryOptions(data) {
        const wrap = document.getElementById('delivery-opts-wrap');
        const loading = document.getElementById('delivery-loading');
        loading.style.display = 'none';

        let html = '';

        if (data.regular) {
            const charge = isFreeShipping ? 0 : data.regular.charge;
            const priceHtml = isFreeShipping
                ? '<span class="delivery-opt-price is-free">FREE</span>'
                : '<span class="delivery-opt-price">₹' + fmt(charge) + '</span>';
            html += '<div class="delivery-opt" id="opt-regular" onclick="selectDeliveryOpt(this, \'regular\', ' + charge + ', \'' + (data.regular.courier_id || '') + '\')">'
                + '<div class="delivery-opt-radio"></div>'
                + '<div class="delivery-opt-info">'
                + '<div class="delivery-opt-name">Standard Delivery</div>'
                + '<div class="delivery-opt-meta">' + (data.regular.label || 'Economical option') + (data.regular.etd ? ' &middot; ETA: <strong>' + data.regular.etd + ' Days</strong>' : '') + '</div>'
                + '</div>'
                + priceHtml
                + '</div>';
        }

        if (data.express && !isFreeShipping) {
            html += '<div class="delivery-opt" id="opt-express" onclick="selectDeliveryOpt(this, \'express\', ' + data.express.charge + ', \'' + (data.express.courier_id || '') + '\')">'
                + '<span class="delivery-opt-badge">Fastest</span>'
                + '<div class="delivery-opt-radio"></div>'
                + '<div class="delivery-opt-info">'
                + '<div class="delivery-opt-name">Express Delivery</div>'
                + '<div class="delivery-opt-meta">' + (data.express.label || 'Priority handling') + (data.express.etd ? ' &middot; ETA: <strong>' + data.express.etd + ' Days</strong>' : '') + '</div>'
                + '</div>'
                + '<span class="delivery-opt-price">₹' + fmt(data.express.charge) + '</span>'
                + '</div>';
        }

        if (!html) {
            wrap.innerHTML = '<p style="color:var(--ink-muted);font-size:0.9rem;padding:8px 0;">No delivery options available for this pincode.</p>';
        } else {
            wrap.innerHTML = html;
        }

        wrap.style.display = 'flex';

        // Auto-select regular
        if (data.regular) {
            const regularOpt = document.getElementById('opt-regular');
            if (regularOpt) {
                selectDeliveryOpt(regularOpt, 'regular', isFreeShipping ? 0 : data.regular.charge, data.regular.courier_id || '');
            }
        }
    }

    function selectDeliveryOpt(el, type, charge, courierId) {
        document.querySelectorAll('.delivery-opt').forEach(function(o) { o.classList.remove('is-selected'); });
        el.classList.add('is-selected');
        document.getElementById('delivery_type').value = type;
        document.getElementById('shiprocket_courier_id').value = courierId;
        currentShippingCharge = isFreeShipping ? 0 : Number(charge);
        updateSummary();
    }

    // ── STEP 2 CONTINUE ───────────────────────────────────────
    function continueToPayment() {
        clearErrors();

        if (!document.getElementById('delivery_type').value) {
            const errorEl = document.querySelector('[data-error="delivery_type"]');
            if (errorEl) errorEl.textContent = 'Please select a delivery option.';
            scrollToFirstError();
            return;
        }

        const type = document.getElementById('delivery_type').value;
        const typeLabel = type === 'express' ? 'Express Delivery' : 'Standard Delivery';
        const shippingLabel = isFreeShipping ? 'FREE' : '₹' + fmt(currentShippingCharge);
        markDone(2, typeLabel + ' · ' + shippingLabel);

        activateStep(3);
        setPlaceOrderEnabled(true);
    }

    // ── SUMMARY ───────────────────────────────────────────────
    function updateSummary() {
        const shipping = isFreeShipping ? 0 : currentShippingCharge;
        const total = taxableAmount + currentGstAmount + shipping;

        document.getElementById('summary-shipping').textContent = isFreeShipping ? 'FREE' : '₹' + fmt(shipping);
        document.getElementById('summary-total').textContent = fmt(total);
        document.getElementById('sticky-total').textContent = fmt(total);
        document.getElementById('shipping_charge').value = shipping;
    }

    // ── GST ───────────────────────────────────────────────────
    function fetchGst() {
        const pincode = document.getElementById('pincode').value;
        const state = document.getElementById('state').value;
        if (pincode.length !== 6) return;

        fetch("{{ route('checkout.calculate.gst') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({ pincode: pincode, taxable_amount: taxableAmount, state: state })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            currentGstAmount = data.gst_amount;
            renderGst(data);
            updateSummary();
        })
        .catch(function() {});
    }

    function renderGst(data) {
        let summaryHtml = '';
        let stepHtml = '';

        if (data.gst_type === 'cgst_sgst') {
            summaryHtml = '<div class="co-summary-row"><span>CGST (' + data.cgst_rate + '%)</span><span>₹' + fmt(data.cgst_amount) + '</span></div>'
                        + '<div class="co-summary-row"><span>SGST (' + data.sgst_rate + '%)</span><span>₹' + fmt(data.sgst_amount) + '</span></div>';
            stepHtml = '<p style="font-size:0.82rem;color:var(--ink-muted);margin:0;">CGST + SGST applied (intra-state)</p>';
        } else {
            summaryHtml = '<div class="co-summary-row"><span>IGST (' + data.igst_rate + '%)</span><span>₹' + fmt(data.igst_amount) + '</span></div>';
            stepHtml = '<p style="font-size:0.82rem;color:var(--ink-muted);margin:0;">IGST applied (inter-state)</p>';
        }

        const summaryGstEl = document.getElementById('gst-summary-section');
        if (summaryGstEl) summaryGstEl.innerHTML = summaryHtml;

        const stepGstEl = document.getElementById('gst-section');
        if (stepGstEl) stepGstEl.innerHTML = stepHtml;
    }

    // ── ENABLE/DISABLE ORDER BUTTONS ─────────────────────────
    function setPlaceOrderEnabled(enabled) {
        document.getElementById('rzp-pay-btn-mobile').disabled = !enabled;
        document.getElementById('rzp-pay-btn-desktop').disabled = !enabled;
    }

    // ── INITIATE PAYMENT ──────────────────────────────────────
    function initiatePayment(btn) {
        clearErrors();

        // Re-validate address
        const fields = ['name', 'email', 'phone', 'pincode', 'city', 'state', 'address'];
        let hasError = false;
        fields.forEach(function(field) {
            const input = document.getElementById(field);
            if (!input || !input.value.trim()) {
                const errorEl = document.querySelector('[data-error="' + field + '"]');
                if (errorEl) errorEl.textContent = 'This field is required.';
                if (input) input.classList.add('has-error');
                hasError = true;
            }
        });

        if (hasError) { editStep(1); setTimeout(scrollToFirstError, 200); return; }

        if (!document.getElementById('delivery_type').value) {
            editStep(2);
            setTimeout(function() {
                const errorEl = document.querySelector('[data-error="delivery_type"]');
                if (errorEl) errorEl.textContent = 'Please select a delivery option.';
                scrollToFirstError();
            }, 200);
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Please wait...';
        document.getElementById('rzp-pay-btn-mobile').disabled = true;
        document.getElementById('rzp-pay-btn-desktop').disabled = true;

        function resetButtons() {
            setPlaceOrderEnabled(true);
            document.getElementById('rzp-pay-btn-mobile').textContent = 'Place Order';
            document.getElementById('rzp-pay-btn-desktop').textContent = 'Pay & Place Order';
        }

        const formData = new FormData(document.getElementById('checkout-form'));

        fetch("{{ route('razorpay.order') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
            body: formData
        })
        .then(function(response) {
            var ok = response.ok;
            return response.text().then(function(text) {
                var data;
                try { data = JSON.parse(text); } catch(e) {
                    throw new Error('JSON parse error: ' + text.substring(0, 200));
                }
                return { ok: ok, data: data };
            });
        })
        .then(function(result) {
            if (!result.ok) {
                resetButtons();
                if (result.data.errors) showErrors(result.data.errors);
                else alert(result.data.message || 'Something went wrong.');
                return;
            }

            const d = result.data;
            const options = {
                key: d.key,
                amount: d.amount,
                currency: d.currency,
                name: "Design Dhaga",
                description: "Order Payment",
                image: "{{ asset('frontend/images/logo.png') }}",
                order_id: d.razorpay_order_id,
                handler: function(response) {
                    fetch("{{ route('razorpay.verify') }}", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                            local_order_id: d.local_order_id
                        })
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(result) {
                        if (result.status) {
                            window.location.href = result.redirect_url;
                        } else {
                            resetButtons();
                            alert(result.message || 'Payment verification failed.');
                        }
                    });
                },
                prefill: { name: d.customer.name, email: d.customer.email, contact: d.customer.phone },
                theme: { color: "#111111" },
                modal: {
                    ondismiss: function() { resetButtons(); }
                }
            };

            new Razorpay(options).open();
        })
        .catch(function() {
            resetButtons();
            alert('Unable to initiate payment. Please try again.');
        });
    }

    document.getElementById('rzp-pay-btn-mobile').addEventListener('click', function() { initiatePayment(this); });
    document.getElementById('rzp-pay-btn-desktop').addEventListener('click', function() { initiatePayment(this); });

    // Clear error on input
    document.querySelectorAll('.co-input, .co-textarea').forEach(function(el) {
        el.addEventListener('input', function() {
            this.classList.remove('has-error');
            const key = this.name;
            const errorEl = document.querySelector('[data-error="' + key + '"]');
            if (errorEl) errorEl.textContent = '';
        });
    });
</script>

@endsection
