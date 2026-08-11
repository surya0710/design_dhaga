@extends('layouts.admin')
@section('content')
<style>
    .ao-wrap { max-width: 1100px; }

    /*
     * Override admin form-style-1:
     * it turns every direct child into a left-label / right-field row
     * and forces display:flex (which also breaks step hiding).
     */
    #adminOrderForm.form-style-1,
    #adminOrderForm {
        display: block !important;
        width: 100%;
    }
    #adminOrderForm > * {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        align-items: stretch !important;
        gap: 0 !important;
    }
    #adminOrderForm > *:not(.ao-nav) > *:first-child {
        width: 100% !important;
        max-width: 100% !important;
    }
    #adminOrderForm fieldset {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
        width: 100% !important;
        margin: 0 !important;
    }
    #adminOrderForm fieldset .body-title {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
    }
    #adminOrderForm fieldset input,
    #adminOrderForm fieldset select,
    #adminOrderForm fieldset textarea,
    #adminOrderForm fieldset .select {
        width: 100% !important;
        max-width: 100% !important;
    }
    #adminOrderForm input:invalid,
    #adminOrderForm select:invalid,
    #adminOrderForm textarea:invalid {
        box-shadow: none !important;
        outline: none !important;
    }

    /* Stepper */
    .ao-stepper {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        margin-bottom: 28px;
        position: relative;
    }
    .ao-stepper::before {
        content: '';
        position: absolute;
        top: 18px;
        left: 12%;
        right: 12%;
        height: 2px;
        background: #e8e8e8;
        z-index: 0;
    }
    .ao-step {
        position: relative;
        z-index: 1;
        text-align: center;
        cursor: pointer;
        background: transparent;
        border: 0;
        padding: 0;
    }
    .ao-step .ao-num {
        width: 36px;
        height: 36px;
        margin: 0 auto 8px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        background: #fff;
        border: 2px solid #ddd;
        color: #888;
        transition: all .2s;
    }
    .ao-step .ao-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #999;
        letter-spacing: .02em;
    }
    .ao-step.is-active .ao-num {
        background: #1a1a1a;
        border-color: #1a1a1a;
        color: #fff;
    }
    .ao-step.is-active .ao-label { color: #1a1a1a; }
    .ao-step.is-done .ao-num {
        background: #fff;
        border-color: #1a1a1a;
        color: #1a1a1a;
    }
    .ao-step.is-done .ao-label { color: #555; }

    #adminOrderForm .ao-panel {
        display: none !important;
        width: 100% !important;
        animation: aoFade .2s ease;
    }
    #adminOrderForm .ao-panel.is-active {
        display: block !important;
    }
    @keyframes aoFade {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: none; }
    }

    .ao-panel-head {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #ececec;
    }
    .ao-panel-title {
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 4px;
        color: #1a1a1a;
    }
    .ao-panel-desc {
        font-size: 13px;
        color: #888;
        margin: 0;
    }
    .ao-divider {
        height: 1px;
        background: #ececec;
        margin: 22px 0;
        border: 0;
    }
    .ao-section-block {
        margin-bottom: 8px;
    }

    .ao-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 14px 16px;
        width: 100%;
    }
    .ao-full { grid-column: 1 / -1; }

    .ao-alert {
        display: none !important;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 18px;
        font-size: 13px;
    }
    .ao-alert.is-visible { display: block !important; }
    .ao-alert:empty { display: none !important; }

    .ao-field-invalid {
        border-color: #f87171 !important;
        outline: 1px solid #f87171;
    }

    /* Items */
    .ao-items { display: flex; flex-direction: column; gap: 12px; width: 100%; }
    .ao-item {
        background: #fff;
        border: 1px solid #e5e5e5 !important;
        border-radius: 12px;
        overflow: hidden;
        outline: none !important;
        box-shadow: none !important;
    }
    .ao-item-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        background: #f8f8f8;
        border-bottom: 1px solid #eee;
    }
    .ao-item-bar strong {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .ao-item-body { padding: 16px; }
    .ao-remove {
        background: transparent;
        border: 0;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        padding: 4px 0;
    }
    .ao-remove:hover { text-decoration: underline; }
    .ao-add-btn {
        width: 100%;
        margin-top: 4px;
        padding: 12px;
        border: 1px dashed #ccc;
        border-radius: 10px;
        background: #fafafa;
        color: #444;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }
    .ao-add-btn:hover {
        border-color: #999;
        background: #f3f3f3;
    }

    /* Shipping mode */
    .ao-mode {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 8px;
        width: 100%;
    }
    .ao-mode label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        cursor: pointer;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }
    .ao-mode label.active {
        border-color: #1a1a1a;
        box-shadow: inset 0 0 0 1px #1a1a1a;
    }
    .ao-mode label strong {
        display: block;
        font-size: 13px;
        color: #1a1a1a;
        margin-bottom: 2px;
    }
    .ao-mode label span {
        font-size: 12px;
        color: #888;
        line-height: 1.35;
    }
    .ao-mode input { margin-top: 3px; }

    .ao-ship-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        width: 100%;
    }
    .ao-ship-card {
        display: block;
        padding: 16px;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
    }
    .ao-ship-card.active {
        border-color: #1a1a1a;
        box-shadow: inset 0 0 0 1px #1a1a1a;
    }
    .ao-ship-card .ao-ship-type {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #888;
        margin-bottom: 6px;
    }
    .ao-ship-card .ao-ship-name {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    .ao-ship-card .ao-ship-meta {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: #555;
    }
    .ao-ship-card input { display: none; }
    .ao-fetch-row {
        display: flex !important;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
        width: 100%;
    }
    .ao-hint {
        font-size: 12px;
        color: #888;
        margin: 0;
    }
    .ao-hint.is-error { color: #b91c1c; }

    /* Review */
    .ao-review-layout {
        display: grid !important;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 20px;
        align-items: start;
        width: 100%;
    }
    .ao-summary {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .ao-block {
        border: 1px solid #ebebeb;
        border-radius: 12px;
        padding: 16px 18px;
        background: #fff;
    }
    .ao-block-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .ao-block-head h5 {
        margin: 0;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #888;
    }
    .ao-edit-link {
        border: 0;
        background: none;
        color: #1a1a1a;
        font-size: 12px;
        font-weight: 500;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
    }
    .ao-block p {
        margin: 0 0 4px;
        font-size: 13px;
        color: #333;
        line-height: 1.5;
    }
    .ao-block p:last-child { margin-bottom: 0; }
    .ao-muted { color: #888 !important; }
    .ao-item-line {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .ao-item-line:last-child { border-bottom: 0; padding-bottom: 0; }
    .ao-item-line:first-child { padding-top: 0; }
    .ao-item-line strong { font-size: 13px; color: #1a1a1a; }
    .ao-side {
        border: 1px solid #ebebeb;
        border-radius: 12px;
        padding: 18px;
        background: #fafafa;
        position: sticky;
        top: 16px;
    }
    .ao-side .ao-grid { grid-template-columns: 1fr; gap: 12px; }
    .ao-totals { margin-top: 16px; padding-top: 14px; border-top: 1px solid #e5e5e5; }
    .ao-totals-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        color: #555;
    }
    .ao-totals-row.total {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
    }

    /* Nav */
    #adminOrderForm .ao-nav {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        flex-wrap: nowrap !important;
        width: 100% !important;
    }
    #adminOrderForm .ao-nav > div {
        display: flex !important;
        flex-direction: row !important;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap !important;
        width: auto !important;
        max-width: none !important;
        flex: 0 0 auto !important;
    }
    #adminOrderForm .ao-nav .tf-button,
    #adminOrderForm .ao-nav a.tf-button {
        display: inline-flex !important;
        width: auto !important;
        min-width: 140px;
        margin: 0 !important;
        white-space: nowrap;
    }

    @media (max-width: 900px) {
        .ao-grid,
        .ao-mode,
        .ao-ship-grid,
        .ao-review-layout { grid-template-columns: 1fr; }
        .ao-stepper { grid-template-columns: repeat(4, 1fr); }
        .ao-step .ao-label { font-size: 10px; }
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap ao-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Create Order</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.orders') }}">
                        <div class="text-tiny">Orders</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Create Order</div></li>
            </ul>
        </div>

        <div class="wg-box">
            @if(session('error'))
                <div class="ao-alert is-visible" style="display:block;">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="ao-alert is-visible" style="display:block;">
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <div class="ao-stepper" id="orderStepper">
                <button type="button" class="ao-step is-active" data-step="1">
                    <span class="ao-num">1</span>
                    <span class="ao-label">Customer</span>
                </button>
                <button type="button" class="ao-step" data-step="2">
                    <span class="ao-num">2</span>
                    <span class="ao-label">Items</span>
                </button>
                <button type="button" class="ao-step" data-step="3">
                    <span class="ao-num">3</span>
                    <span class="ao-label">Shipping</span>
                </button>
                <button type="button" class="ao-step" data-step="4">
                    <span class="ao-num">4</span>
                    <span class="ao-label">Review</span>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.order.store') }}" id="adminOrderForm" novalidate>
                @csrf

                <div class="ao-alert" id="stepError" role="alert"></div>

                {{-- Step 1 --}}
                <div class="ao-panel is-active" data-panel="1">
                    <div class="ao-panel-head">
                        <h4 class="ao-panel-title">Customer & address</h4>
                        <p class="ao-panel-desc">Who is this order for, and where should it be delivered?</p>
                    </div>

                    <div class="ao-grid">
                        <fieldset class="name">
                            <div class="body-title">Name <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="name" id="name" value="{{ old('name') }}" required>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title">Email <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="email" name="email" id="email" value="{{ old('email') }}" required>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title">Phone <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="phone" id="phone" value="{{ old('phone') }}" required>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title">&nbsp;</div>
                            <label style="display:flex;align-items:center;gap:8px;min-height:42px;">
                                <input type="checkbox" name="link_user" value="1" {{ old('link_user') ? 'checked' : '' }}>
                                Link existing user by email
                            </label>
                        </fieldset>
                    </div>

                    <hr class="ao-divider">

                    <div class="ao-section-block">
                        <div class="ao-grid">
                            <fieldset class="name ao-full">
                                <div class="body-title">Address line 1 <span class="tf-color-1">*</span></div>
                                <input class="flex-grow" type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1') }}" required>
                            </fieldset>
                            <fieldset class="name ao-full">
                                <div class="body-title">Address line 2</div>
                                <input class="flex-grow" type="text" name="address_line_2" value="{{ old('address_line_2') }}">
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title">Landmark</div>
                                <input class="flex-grow" type="text" name="landmark" value="{{ old('landmark') }}">
                            </fieldset>
                            <fieldset class="category">
                                <div class="body-title">Address type</div>
                                <div class="select flex-grow">
                                    <select name="address_type">
                                        @foreach(['home' => 'Home', 'work' => 'Work', 'other' => 'Other'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('address_type', 'home') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title">Pincode <span class="tf-color-1">*</span></div>
                                <input class="flex-grow" type="text" name="pincode" id="pincode" value="{{ old('pincode') }}" required>
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title">City <span class="tf-color-1">*</span></div>
                                <input class="flex-grow" type="text" name="city" id="city" value="{{ old('city') }}" required>
                            </fieldset>
                            <fieldset class="category">
                                <div class="body-title">State <span class="tf-color-1">*</span></div>
                                <div class="select flex-grow">
                                    <select name="state" id="state" required>
                                        <option value="">Select state</option>
                                        @foreach($indianStates as $state)
                                            <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title">Country</div>
                                <input class="flex-grow" type="text" name="country" value="{{ old('country', 'India') }}">
                            </fieldset>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="ao-panel" data-panel="2">
                    <div class="ao-panel-head">
                        <h4 class="ao-panel-title">Order items</h4>
                        <p class="ao-panel-desc">Add catalog products with variants, or enter a custom line for made-to-order work.</p>
                    </div>

                    <div class="ao-items" id="orderItems"></div>
                    <button type="button" class="ao-add-btn" id="addItemBtn">+ Add another item</button>

                    <hr class="ao-divider">

                    <fieldset class="name">
                        <div class="body-title">Customization notes</div>
                        <textarea class="flex-grow" name="notes" id="notes" rows="3" placeholder="Measurements, artwork notes, special instructions…">{{ old('notes') }}</textarea>
                    </fieldset>
                </div>

                {{-- Step 3 --}}
                <div class="ao-panel" data-panel="3">
                    <div class="ao-panel-head">
                        <h4 class="ao-panel-title">Shipping</h4>
                        <p class="ao-panel-desc">Choose a Shiprocket option for this pincode, or enter shipping manually.</p>
                    </div>

                    <div class="ao-mode">
                        <label id="modeShiprocketLabel">
                            <input type="radio" name="shipping_mode" value="shiprocket" {{ old('shipping_mode', 'shiprocket') === 'shiprocket' ? 'checked' : '' }}>
                            <div>
                                <strong>Shiprocket</strong>
                                <span>Fetch live courier rates for the delivery pincode</span>
                            </div>
                        </label>
                        <label id="modeManualLabel">
                            <input type="radio" name="shipping_mode" value="manual" {{ old('shipping_mode') === 'manual' ? 'checked' : '' }}>
                            <div>
                                <strong>Manual</strong>
                                <span>Set courier name and shipping charge yourself</span>
                            </div>
                        </label>
                    </div>

                    <hr class="ao-divider">

                    <div id="shiprocketPanel">
                        <div class="ao-fetch-row">
                            <button type="button" class="tf-button w208" id="fetchShippingBtn">Get shipping options</button>
                            <p class="ao-hint" id="shippingOptionsMsg"></p>
                        </div>
                        <div class="ao-ship-grid" id="shippingOptions"></div>
                    </div>

                    <div id="manualPanel" style="display:none;">
                        <div class="ao-grid">
                            <fieldset class="name">
                                <div class="body-title">Shipping charge (₹) <span class="tf-color-1">*</span></div>
                                <input class="flex-grow" type="number" step="0.01" min="0" name="shipping_manual" id="shippingManual" value="{{ old('shipping', 0) }}">
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title">Courier name</div>
                                <input class="flex-grow" type="text" name="courier_name" id="courierName" value="{{ old('courier_name') }}" placeholder="e.g. Delhivery">
                            </fieldset>
                            <fieldset class="category">
                                <div class="body-title">Delivery type</div>
                                <div class="select flex-grow">
                                    <select name="delivery_type_manual" id="deliveryTypeManual">
                                        <option value="regular" {{ old('delivery_type') === 'regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="express" {{ old('delivery_type') === 'express' ? 'selected' : '' }}>Express</option>
                                    </select>
                                </div>
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title">ETA</div>
                                <input class="flex-grow" type="text" name="delivery_eta" id="deliveryEta" value="{{ old('delivery_eta') }}" placeholder="e.g. 5 days">
                            </fieldset>
                            <fieldset class="name ao-full">
                                <div class="body-title">Expected delivery date</div>
                                <input class="flex-grow" type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                            </fieldset>
                        </div>
                    </div>

                    <input type="hidden" name="shipping" id="shippingCharge" value="{{ old('shipping', 0) }}">
                    <input type="hidden" name="delivery_type" id="deliveryType" value="{{ old('delivery_type', 'regular') }}">
                    <input type="hidden" name="shiprocket_courier_id" id="shiprocketCourierId" value="{{ old('shiprocket_courier_id') }}">
                    <input type="hidden" name="delivery_label" id="deliveryLabel" value="{{ old('delivery_label') }}">
                </div>

                {{-- Step 4 --}}
                <div class="ao-panel" data-panel="4">
                    <div class="ao-panel-head">
                        <h4 class="ao-panel-title">Review & create</h4>
                        <p class="ao-panel-desc">Confirm the order details, set payment, then create.</p>
                    </div>

                    <div class="ao-review-layout">
                        <div class="ao-summary">
                            <div class="ao-block">
                                <div class="ao-block-head">
                                    <h5>Customer</h5>
                                    <button type="button" class="ao-edit-link" data-goto="1">Edit</button>
                                </div>
                                <p id="reviewCustomer">—</p>
                                <p class="ao-muted" id="reviewAddress">—</p>
                            </div>

                            <div class="ao-block">
                                <div class="ao-block-head">
                                    <h5>Items</h5>
                                    <button type="button" class="ao-edit-link" data-goto="2">Edit</button>
                                </div>
                                <div id="reviewItems"></div>
                                <p class="ao-muted" id="reviewNotes" style="margin-top:8px;"></p>
                            </div>

                            <div class="ao-block">
                                <div class="ao-block-head">
                                    <h5>Shipping</h5>
                                    <button type="button" class="ao-edit-link" data-goto="3">Edit</button>
                                </div>
                                <p id="reviewShipping">—</p>
                            </div>
                        </div>

                        <div class="ao-side">
                            <div class="ao-grid">
                                <fieldset class="name">
                                    <div class="body-title">Discount (₹)</div>
                                    <input class="flex-grow" type="number" step="0.01" min="0" name="coupon_discount" id="couponDiscount" value="{{ old('coupon_discount', 0) }}">
                                </fieldset>
                                <fieldset class="category">
                                    <div class="body-title">Payment method <span class="tf-color-1">*</span></div>
                                    <div class="select flex-grow">
                                        <select name="payment_method" id="payment_method" required>
                                            @foreach(['offline' => 'Offline / Cash', 'cod' => 'COD', 'bank_transfer' => 'Bank transfer', 'razorpay' => 'Razorpay (recorded)'] as $val => $label)
                                                <option value="{{ $val }}" {{ old('payment_method', 'offline') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </fieldset>
                                <fieldset class="category">
                                    <div class="body-title">Payment status <span class="tf-color-1">*</span></div>
                                    <div class="select flex-grow">
                                        <select name="payment_status" id="payment_status" required>
                                            <option value="paid" {{ old('payment_status', 'paid') === 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                    </div>
                                </fieldset>
                                <fieldset class="category">
                                    <div class="body-title">Order status <span class="tf-color-1">*</span></div>
                                    <div class="select flex-grow">
                                        <select name="order_status" id="order_status" required>
                                            <option value="confirmed" {{ old('order_status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="pending" {{ old('order_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="packed" {{ old('order_status') === 'packed' ? 'selected' : '' }}>Packed</option>
                                        </select>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="ao-totals">
                                <div class="ao-totals-row"><span>Subtotal</span><span id="dispSubtotal">₹0.00</span></div>
                                <div class="ao-totals-row"><span>Discount</span><span id="dispDiscount">₹0.00</span></div>
                                <div class="ao-totals-row"><span>GST (est.)</span><span id="dispGst">₹0.00</span></div>
                                <div class="ao-totals-row"><span>Shipping</span><span id="dispShipping">₹0.00</span></div>
                                <div class="ao-totals-row total"><span>Total</span><span id="dispTotal">₹0.00</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ao-nav">
                    <div>
                        <button type="button" class="tf-button style-2 w208" id="prevStepBtn" style="display:none;">Back</button>
                        <a href="{{ route('admin.orders') }}" class="tf-button style-2 w208" id="cancelBtn">Cancel</a>
                    </div>
                    <div class="ao-nav-right">
                        <button type="button" class="tf-button w208" id="nextStepBtn">Continue</button>
                        <button type="submit" class="tf-button w208" id="submitOrderBtn" style="display:none;">Create order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="itemRowTemplate">
    <div class="ao-item" data-item-index="__INDEX__">
        <div class="ao-item-bar">
            <strong>Item <span class="item-num">__NUM__</span></strong>
            <button type="button" class="ao-remove remove-item">Remove</button>
        </div>
        <div class="ao-item-body">
            <div class="ao-grid">
                <fieldset class="category ao-full">
                    <div class="body-title">Product</div>
                    <div class="select flex-grow">
                        <select class="product-select" name="items[__INDEX__][product_id]">
                            <option value="">Custom / not in catalog</option>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="category ao-full variant-wrap" style="display:none;">
                    <div class="body-title">Variant</div>
                    <div class="select flex-grow">
                        <select class="variant-select" name="items[__INDEX__][product_variant_id]">
                            <option value="">Select size / fabric</option>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow product-name" type="text" name="items[__INDEX__][product_name]" required placeholder="Product name">
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">SKU</div>
                    <input class="flex-grow item-sku" type="text" name="items[__INDEX__][sku]" placeholder="Optional">
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Size</div>
                    <input class="flex-grow item-size" type="text" name="items[__INDEX__][size]" placeholder="e.g. M">
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Fabric</div>
                    <input class="flex-grow item-fabric" type="text" name="items[__INDEX__][fabric_type]" placeholder="e.g. Cotton">
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Price (₹) <span class="tf-color-1">*</span></div>
                    <input class="flex-grow item-price" type="number" step="0.01" min="0" name="items[__INDEX__][price]" value="0" required>
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Qty <span class="tf-color-1">*</span></div>
                    <input class="flex-grow item-qty" type="number" min="1" name="items[__INDEX__][quantity]" value="1" required>
                </fieldset>
            </div>
        </div>
    </div>
</template>

<script>
(function () {
    const products = @json($productsPayload);
    const productsById = {};
    products.forEach(p => { productsById[p.id] = p; });

    const itemsEl = document.getElementById('orderItems');
    const template = document.getElementById('itemRowTemplate').innerHTML;
    const stepError = document.getElementById('stepError');
    const totalSteps = 4;
    let currentStep = 1;
    let itemIndex = 0;

    function money(n) {
        return '₹' + (Number(n) || 0).toFixed(2);
    }

    function clearFieldErrors(panel) {
        panel.querySelectorAll('.ao-field-invalid').forEach(el => el.classList.remove('ao-field-invalid'));
    }

    function markInvalid(el) {
        if (el) el.classList.add('ao-field-invalid');
    }

    function showStepError(message) {
        stepError.textContent = message || '';
        if (message) stepError.classList.add('is-visible');
        else stepError.classList.remove('is-visible');
    }

    function hideStepError() {
        stepError.textContent = '';
        stepError.classList.remove('is-visible');
    }

    function recalcTotals() {
        let subtotal = 0;
        itemsEl.querySelectorAll('.ao-item').forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty = parseInt(row.querySelector('.item-qty').value, 10) || 0;
            subtotal += price * qty;
        });
        const discount = Math.min(parseFloat(document.getElementById('couponDiscount').value) || 0, subtotal);
        const taxable = Math.max(subtotal - discount, 0);
        const gst = taxable * 0.05;
        const shipping = parseFloat(document.getElementById('shippingCharge').value) || 0;
        const total = taxable + gst + shipping;

        document.getElementById('dispSubtotal').textContent = money(subtotal);
        document.getElementById('dispDiscount').textContent = money(discount);
        document.getElementById('dispGst').textContent = money(gst);
        document.getElementById('dispShipping').textContent = money(shipping);
        document.getElementById('dispTotal').textContent = money(total);
    }

    function estimatedWeight() {
        let weight = 0;
        itemsEl.querySelectorAll('.ao-item').forEach(row => {
            const productId = row.querySelector('.product-select').value;
            const qty = parseInt(row.querySelector('.item-qty').value, 10) || 1;
            const productWeight = productId && productsById[productId]
                ? (productsById[productId].weight || 0.5)
                : 0.5;
            weight += productWeight * qty;
        });
        return Math.max(weight, 0.5);
    }

    function estimatedValue() {
        let value = 0;
        itemsEl.querySelectorAll('.ao-item').forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty = parseInt(row.querySelector('.item-qty').value, 10) || 0;
            value += price * qty;
        });
        return value;
    }

    function fillProductOptions(select) {
        products.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name + (p.sku ? ' (' + p.sku + ')' : '');
            select.appendChild(opt);
        });
    }

    function bindItemRow(row) {
        const productSelect = row.querySelector('.product-select');
        const variantWrap = row.querySelector('.variant-wrap');
        const variantSelect = row.querySelector('.variant-select');
        const nameInput = row.querySelector('.product-name');
        const skuInput = row.querySelector('.item-sku');
        const sizeInput = row.querySelector('.item-size');
        const fabricInput = row.querySelector('.item-fabric');
        const priceInput = row.querySelector('.item-price');

        fillProductOptions(productSelect);

        productSelect.addEventListener('change', function () {
            const product = productsById[this.value];
            variantSelect.innerHTML = '<option value="">Select size / fabric</option>';

            if (!product) {
                variantWrap.style.display = 'none';
                return;
            }

            nameInput.value = product.name;
            skuInput.value = product.sku || '';
            priceInput.value = product.price || 0;

            if (product.variants && product.variants.length) {
                variantWrap.style.display = '';
                product.variants.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.id;
                    opt.textContent = [v.size, v.fabric_type, '₹' + Number(v.price).toFixed(2)].filter(Boolean).join(' / ');
                    opt.dataset.size = v.size || '';
                    opt.dataset.fabric = v.fabric_type || '';
                    opt.dataset.sku = v.sku || '';
                    opt.dataset.price = v.price || 0;
                    variantSelect.appendChild(opt);
                });
            } else {
                variantWrap.style.display = 'none';
                sizeInput.value = '';
                fabricInput.value = '';
            }
            recalcTotals();
        });

        variantSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (!opt || !opt.value) return;
            sizeInput.value = opt.dataset.size || '';
            fabricInput.value = opt.dataset.fabric || '';
            skuInput.value = opt.dataset.sku || skuInput.value;
            priceInput.value = opt.dataset.price || priceInput.value;
            recalcTotals();
        });

        row.querySelector('.remove-item').addEventListener('click', function () {
            if (itemsEl.querySelectorAll('.ao-item').length <= 1) {
                showStepError('At least one item is required.');
                return;
            }
            row.remove();
            renumberItems();
            recalcTotals();
            hideStepError();
        });

        row.querySelector('.item-price').addEventListener('input', recalcTotals);
        row.querySelector('.item-qty').addEventListener('input', recalcTotals);
    }

    function renumberItems() {
        itemsEl.querySelectorAll('.ao-item').forEach((row, i) => {
            row.querySelector('.item-num').textContent = i + 1;
        });
    }

    function addItem() {
        const html = template
            .replaceAll('__INDEX__', String(itemIndex))
            .replaceAll('__NUM__', String(itemsEl.querySelectorAll('.ao-item').length + 1));
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        const row = wrap.firstElementChild;
        itemsEl.appendChild(row);
        bindItemRow(row);
        itemIndex++;
        recalcTotals();
    }

    function syncShippingMode() {
        const mode = document.querySelector('input[name="shipping_mode"]:checked').value;
        const isManual = mode === 'manual';
        document.getElementById('shiprocketPanel').style.display = isManual ? 'none' : '';
        document.getElementById('manualPanel').style.display = isManual ? '' : 'none';
        document.getElementById('modeShiprocketLabel').classList.toggle('active', !isManual);
        document.getElementById('modeManualLabel').classList.toggle('active', isManual);

        if (isManual) {
            document.getElementById('shippingCharge').value = document.getElementById('shippingManual').value || 0;
            document.getElementById('deliveryType').value = document.getElementById('deliveryTypeManual').value || 'regular';
            document.getElementById('shiprocketCourierId').value = '';
            document.getElementById('deliveryLabel').value = 'Manual';
        }
        recalcTotals();
    }

    function updateReview() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        document.getElementById('reviewCustomer').textContent = [name, email, phone].filter(Boolean).join(' · ') || '—';

        const address = [
            document.getElementById('address_line_1').value.trim(),
            document.querySelector('[name="address_line_2"]').value.trim(),
            document.getElementById('city').value.trim(),
            document.getElementById('state').value.trim(),
            document.getElementById('pincode').value.trim()
        ].filter(Boolean).join(', ');
        document.getElementById('reviewAddress').textContent = address || '—';

        const itemLines = [];
        itemsEl.querySelectorAll('.ao-item').forEach(row => {
            const pname = row.querySelector('.product-name').value.trim() || 'Untitled';
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty = parseInt(row.querySelector('.item-qty').value, 10) || 0;
            const size = row.querySelector('.item-size').value.trim();
            const fabric = row.querySelector('.item-fabric').value.trim();
            const meta = [size && ('Size ' + size), fabric && fabric].filter(Boolean).join(' · ');
            itemLines.push(
                '<div class="ao-item-line">' +
                    '<strong>' + pname + '</strong>' +
                    '<p class="ao-muted">' + qty + ' × ' + money(price) + ' = ' + money(price * qty) +
                    (meta ? ' · ' + meta : '') + '</p>' +
                '</div>'
            );
        });
        document.getElementById('reviewItems').innerHTML = itemLines.join('') || '<p class="ao-muted">No items</p>';

        const notes = document.getElementById('notes').value.trim();
        const notesEl = document.getElementById('reviewNotes');
        notesEl.textContent = notes ? ('Notes: ' + notes) : '';
        notesEl.style.display = notes ? '' : 'none';

        const mode = document.querySelector('input[name="shipping_mode"]:checked').value;
        const courier = document.getElementById('courierName').value.trim();
        const eta = document.getElementById('deliveryEta').value.trim();
        const shipping = money(document.getElementById('shippingCharge').value || 0);
        const dtype = document.getElementById('deliveryType').value;
        document.getElementById('reviewShipping').innerHTML =
            '<strong>' + (mode === 'manual' ? 'Manual shipping' : 'Shiprocket') + '</strong><br>' +
            '<span class="ao-muted">' + dtype +
            (courier ? ' · ' + courier : '') +
            ' · ' + shipping +
            (eta ? ' · ETA ' + eta : '') + '</span>';
    }

    function validateStep(step) {
        hideStepError();
        const panel = document.querySelector('.ao-panel[data-panel="' + step + '"]');
        clearFieldErrors(panel);

        if (step === 1) {
            const requiredIds = ['name', 'email', 'phone', 'address_line_1', 'pincode', 'city', 'state'];
            let firstInvalid = null;
            requiredIds.forEach(id => {
                const el = document.getElementById(id);
                if (!el || !String(el.value || '').trim()) {
                    markInvalid(el);
                    if (!firstInvalid) firstInvalid = el;
                }
            });
            const email = document.getElementById('email');
            if (email.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                markInvalid(email);
                firstInvalid = firstInvalid || email;
                showStepError('Enter a valid email address.');
                return false;
            }
            if (firstInvalid) {
                showStepError('Please fill all required customer and address fields.');
                firstInvalid.focus();
                return false;
            }
        }

        if (step === 2) {
            const rows = itemsEl.querySelectorAll('.ao-item');
            if (!rows.length) {
                showStepError('Add at least one order item.');
                return false;
            }
            let ok = true;
            let firstInvalid = null;
            rows.forEach(row => {
                const nameInput = row.querySelector('.product-name');
                const priceInput = row.querySelector('.item-price');
                const qtyInput = row.querySelector('.item-qty');
                if (!nameInput.value.trim()) {
                    markInvalid(nameInput);
                    ok = false;
                    firstInvalid = firstInvalid || nameInput;
                }
                if (priceInput.value === '' || parseFloat(priceInput.value) < 0) {
                    markInvalid(priceInput);
                    ok = false;
                    firstInvalid = firstInvalid || priceInput;
                }
                if (!qtyInput.value || parseInt(qtyInput.value, 10) < 1) {
                    markInvalid(qtyInput);
                    ok = false;
                    firstInvalid = firstInvalid || qtyInput;
                }
            });
            if (!ok) {
                showStepError('Each item needs a name, price, and quantity.');
                if (firstInvalid) firstInvalid.focus();
                return false;
            }
        }

        if (step === 3) {
            syncShippingMode();
            const mode = document.querySelector('input[name="shipping_mode"]:checked').value;
            if (mode === 'shiprocket') {
                if (!document.getElementById('shiprocketCourierId').value) {
                    showStepError('Get shipping options and select a courier, or switch to manual.');
                    return false;
                }
            } else {
                const charge = document.getElementById('shippingManual');
                if (charge.value === '' || parseFloat(charge.value) < 0) {
                    markInvalid(charge);
                    showStepError('Enter a valid shipping charge.');
                    charge.focus();
                    return false;
                }
            }
        }

        if (step === 4) {
            const method = document.getElementById('payment_method');
            const payStatus = document.getElementById('payment_status');
            const orderStatus = document.getElementById('order_status');
            if (!method.value || !payStatus.value || !orderStatus.value) {
                showStepError('Select payment method and statuses.');
                return false;
            }
        }

        return true;
    }

    function goToStep(step) {
        currentStep = step;
        document.querySelectorAll('.ao-panel').forEach(panel => {
            panel.classList.toggle('is-active', Number(panel.dataset.panel) === step);
        });
        document.querySelectorAll('.ao-step').forEach(el => {
            const s = Number(el.dataset.step);
            el.classList.toggle('is-active', s === step);
            el.classList.toggle('is-done', s < step);
        });

        document.getElementById('prevStepBtn').style.display = step > 1 ? '' : 'none';
        document.getElementById('cancelBtn').style.display = step === 1 ? '' : 'none';
        document.getElementById('nextStepBtn').style.display = step < totalSteps ? '' : 'none';
        document.getElementById('submitOrderBtn').style.display = step === totalSteps ? '' : 'none';

        if (step === 4) {
            updateReview();
            recalcTotals();
        }

        hideStepError();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('addItemBtn').addEventListener('click', addItem);
    document.getElementById('couponDiscount').addEventListener('input', recalcTotals);

    document.querySelectorAll('input[name="shipping_mode"]').forEach(r => {
        r.addEventListener('change', syncShippingMode);
    });
    document.getElementById('shippingManual').addEventListener('input', function () {
        if (document.querySelector('input[name="shipping_mode"]:checked').value === 'manual') {
            document.getElementById('shippingCharge').value = this.value || 0;
            recalcTotals();
        }
    });
    document.getElementById('deliveryTypeManual').addEventListener('change', function () {
        if (document.querySelector('input[name="shipping_mode"]:checked').value === 'manual') {
            document.getElementById('deliveryType').value = this.value;
        }
    });

    document.getElementById('fetchShippingBtn').addEventListener('click', async function () {
        const pincode = document.getElementById('pincode').value.trim();
        const msg = document.getElementById('shippingOptionsMsg');
        const box = document.getElementById('shippingOptions');
        box.innerHTML = '';
        msg.textContent = '';
        msg.classList.remove('is-error');

        if (!pincode) {
            msg.textContent = 'Add a pincode in Customer step first.';
            msg.classList.add('is-error');
            return;
        }

        msg.textContent = 'Fetching options…';
        this.disabled = true;

        try {
            const res = await fetch('{{ route('admin.order.delivery.options') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    pincode: pincode,
                    weight: estimatedWeight(),
                    declared_value: estimatedValue(),
                    cod: document.querySelector('select[name="payment_method"]').value === 'cod'
                })
            });
            const data = await res.json();
            if (!res.ok) {
                msg.textContent = data.message || 'Failed to fetch options.';
                msg.classList.add('is-error');
                return;
            }

            msg.textContent = '';
            ['regular', 'express'].forEach(type => {
                const opt = data[type];
                if (!opt) return;
                const card = document.createElement('label');
                card.className = 'ao-ship-card';
                card.innerHTML =
                    '<input type="radio" name="ship_option_ui" value="' + type + '">' +
                    '<div class="ao-ship-type">' + (type === 'express' ? 'Express' : 'Regular') + '</div>' +
                    '<div class="ao-ship-name">' + (opt.label || 'Courier') + '</div>' +
                    '<div class="ao-ship-meta"><span>₹' + Number(opt.charge).toFixed(2) + '</span><span>ETA ' + (opt.etd ?? '—') + ' days</span></div>';
                card.querySelector('input').addEventListener('change', function () {
                    document.querySelectorAll('.ao-ship-card').forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    document.getElementById('shippingCharge').value = opt.charge;
                    document.getElementById('deliveryType').value = type;
                    document.getElementById('shiprocketCourierId').value = opt.courier_id;
                    document.getElementById('deliveryLabel').value = type === 'express' ? 'Fastest' : 'Economical';
                    document.getElementById('deliveryEta').value = opt.etd ?? '';
                    document.getElementById('courierName').value = opt.label || '';
                    recalcTotals();
                });
                box.appendChild(card);
            });

            if (!box.children.length) {
                msg.textContent = 'No options returned for this pincode.';
                msg.classList.add('is-error');
            }
        } catch (e) {
            msg.textContent = 'Unable to fetch shipping options.';
            msg.classList.add('is-error');
        } finally {
            this.disabled = false;
        }
    });

    document.getElementById('nextStepBtn').addEventListener('click', function () {
        if (!validateStep(currentStep)) return;
        goToStep(Math.min(currentStep + 1, totalSteps));
    });

    document.getElementById('prevStepBtn').addEventListener('click', function () {
        goToStep(Math.max(currentStep - 1, 1));
    });

    document.querySelectorAll('.ao-step').forEach(el => {
        el.addEventListener('click', function () {
            const target = Number(this.dataset.step);
            if (target === currentStep) return;
            if (target < currentStep) {
                goToStep(target);
                return;
            }
            for (let s = currentStep; s < target; s++) {
                if (!validateStep(s)) {
                    goToStep(s);
                    return;
                }
            }
            goToStep(target);
        });
    });

    document.querySelectorAll('[data-goto]').forEach(btn => {
        btn.addEventListener('click', function () {
            goToStep(Number(this.dataset.goto));
        });
    });

    document.getElementById('adminOrderForm').addEventListener('submit', function (e) {
        for (let s = 1; s <= totalSteps; s++) {
            if (!validateStep(s)) {
                e.preventDefault();
                goToStep(s);
                return;
            }
        }
        syncShippingMode();
        if (document.querySelector('input[name="shipping_mode"]:checked').value === 'manual') {
            document.getElementById('shippingCharge').value = document.getElementById('shippingManual').value || 0;
            document.getElementById('deliveryType').value = document.getElementById('deliveryTypeManual').value || 'regular';
            document.getElementById('deliveryLabel').value = 'Manual';
        }
    });

    addItem();
    syncShippingMode();
    recalcTotals();
    hideStepError();
    goToStep(1);
})();
</script>
@endsection
