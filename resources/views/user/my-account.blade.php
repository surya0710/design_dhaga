@extends('frontend.layouts.app')
@section('title', 'My Account - Design Dhaga')

@section('meta_description', 'Create your Design Dhaga account to access exclusive handcrafted collections and manage
your orders.')
@section('meta_keywords', 'register, sign up, design dhaga, fashion brand, handmade clothing, made in india')
@section('og_title', 'Register - Design Dhaga')
@section('og_description', 'Create your Design Dhaga account to access exclusive handcrafted collections.')
@section('og_image', asset('frontend_assets/images/og-register.jpg'))

@section('content')
<style>
    :root {
        --primary-color: #212529;
        --bg-color: #f8f9fa;
        --card-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    body {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background-color: var(--bg-color);
        color: #4a5568;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .nav-pills .nav-link {
        color: #6c757d;
        font-weight: 500;
        padding: 12px 20px;
        border-radius: 12px;
        transition: all 0.3s ease;
        margin-bottom: 8px;
    }

    .nav-pills .nav-link:hover {
        background-color: #e9ecef;
        color: var(--primary-color);
        transform: translateX(5px);
    }

    .nav-pills .nav-link.active {
        background-color: var(--primary-color);
        color: #fff;
        box-shadow: 0 4px 12px rgba(33, 37, 41, 0.3);
    }

    .nav-pills .nav-link i {
        font-size: 1.1rem;
    }

    @media (max-width: 575.98px) {
        .nav-pills {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .nav-pills .nav-link {
            margin-bottom: 0;
            text-align: center;
            justify-content: center;
        }
    }

    .btn-dark {
        background-color: var(--primary-color);
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-dark:hover {
        background-color: #000;
        transform: scale(1.02);
    }

    .form-control,
    .form-select {
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(33, 37, 41, 0.1);
        border-color: var(--primary-color);
    }

    .form-control-plaintext {
        font-weight: 500;
        color: #2d3748;
    }

    .stat-card-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 50%;
        margin: 0 auto 10px;
        color: var(--primary-color);
        transition: 0.3s;
    }

    .card:hover .stat-card-icon {
        background: var(--primary-color);
        color: #fff;
    }

    .account-tabs .tab-pane {
        display: none;
    }

    .account-tabs .tab-pane.show.active {
        display: block;
    }

    /* ── Tracking Modal ── */
    .tracking-modal .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }

    .tracking-modal .modal-dialog {
        max-width: 580px;
    }

    .tracking-modal .modal-content {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 28px 70px rgba(15, 23, 42, 0.24);
        overflow: hidden;
    }

    /* Header */
    .tracking-header {
        background: #ffffff;
        color: #111827;
        padding: 18px 22px;
        border-bottom: 1px solid #edf2f7;
    }

    .tracking-header h5 {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 2px;
    }

    .tracking-header small {
        font-size: 12px;
        color: #6b7280;
    }

    /* Delivery summary banner */
    .tracking-delivery-summary {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f0fdf4;
        border-bottom: 1px solid #bbf7d0;
        padding: 14px 22px;
    }

    .tracking-delivery-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #bbf7d0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #047857;
        font-size: 16px;
    }

    .tracking-delivery-primary {
        font-size: 13px;
        font-weight: 700;
        color: #047857;
        margin-bottom: 1px;
    }

    .tracking-delivery-secondary {
        font-size: 12px;
        color: #065f46;
    }

    /* Progress Steps */
    .tracking-steps-section {
        padding: 20px 22px 4px;
    }

    .tracking-steps-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 16px;
    }

    .tracking-steps-row {
        display: flex;
        align-items: flex-start;
        position: relative;
        margin-bottom: 22px;
    }

    /* Grey base track — runs between dot centres. Each dot centre is at 12.5% of total.
       So track starts at 12.5% and spans 75% (ending at 87.5%). */
    .tracking-steps-row::before {
        content: '';
        position: absolute;
        top: 13px;
        left: 12.5%;
        width: 75%;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }

    /* Active fill track. Width is set via JS as a % of the 75% span.
       For 4 steps: confirmed=0%, packed=33%, shipped=66%, delivered=100%
       The fill goes left: 12.5%, and its width = (progress/100) * 75% of container */
    .tracking-steps-row .track-fill {
        position: absolute;
        top: 13px;
        left: 12.5%;
        height: 2px;
        background: #111827;
        z-index: 0;
        transition: width 0.4s ease;
        border-radius: 999px;
    }

    .tracking-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .tracking-step-dot {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        background: #fff;
        margin: 0 auto 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 12px;
    }

    .tracking-step.done .tracking-step-dot,
    .tracking-step.active .tracking-step-dot {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .tracking-step-label {
        font-size: 11px;
        font-weight: 600;
        color: #9ca3af;
    }

    .tracking-step.done .tracking-step-label,
    .tracking-step.active .tracking-step-label {
        color: #111827;
    }

    /* Info strip (delivered date / ETA only — no courier clutter) */
    .tracking-info-strip {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 10px 22px 18px;
        font-size: 12px;
        color: #6b7280;
    }

    .tracking-info-strip i { color: #9ca3af; }
    .tracking-info-strip strong { color: #111827; font-weight: 600; }

    /* Timeline */
    .tracking-timeline {
        border-top: 1px solid #f1f5f9;
        padding: 18px 22px 22px;
        max-height: 300px;
        overflow-y: auto;
    }

    .tracking-timeline::-webkit-scrollbar { width: 4px; }
    .tracking-timeline::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
    .tracking-timeline::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .tracking-section-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 12px;
    }

    /* ── Flipkart-style grouped milestone timeline ── */
    .tl-group {
        display: flex;
        gap: 14px;
        position: relative;
        padding-bottom: 20px;
    }

    .tl-group:last-child { padding-bottom: 0; }

    .tl-spine {
        position: relative;
        flex-shrink: 0;
        width: 12px;
        margin-top: 4px;
    }

    .tl-group:not(:last-child) .tl-spine::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 13px;
        bottom: -20px;
        width: 2px;
        background: #e5e7eb;
        z-index: 0;
    }

    .tl-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #047857;
        background: #047857;
        position: relative;
        z-index: 1;
    }

    .tl-dot.inactive {
        background: #fff;
        border-color: #d1d5db;
    }

    .tl-body { flex: 1; min-width: 0; }

    .tl-head {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 4px;
    }

    .tl-label {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
    }

    .tl-group.inactive .tl-label { color: #6b7280; }

    .tl-date {
        font-size: 12px;
        color: #9ca3af;
    }

    .tl-sub {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.6;
        padding-left: 2px;
    }

    .tl-sub-time {
        font-size: 11px;
        color: #9ca3af;
    }

    /* Skeleton loader */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .orders-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .orders-toolbar h4 { margin: 0; color: #111827; }
    .orders-toolbar .subtitle { color: #6b7280; font-size: 13px; margin-top: 2px; }

    .order-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        background: #fff;
    }

    .order-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
    }

    .order-id { color: #111827; font-weight: 800; font-size: 16px; }

    .order-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 14px;
        color: #6b7280;
        font-size: 12px;
        margin-top: 4px;
    }

    .order-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 800;
        text-transform: capitalize;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .order-status-pill::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .order-status-pending   { background: #f3f4f6; color: #4b5563; border-color: #e5e7eb; }
    .order-status-confirmed { background: #eef6ff; color: #075985; border-color: #bae6fd; }
    .order-status-packed    { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }
    .order-status-shipped   { background: #eef2ff; color: #3730a3; border-color: #c7d2fe; }
    .order-status-delivered { background: #ecfdf5; color: #047857; border-color: #bbf7d0; }
    .order-status-cancelled { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }

    .order-card-body { padding: 20px; }

    .order-product-img {
        width: 92px;
        height: 112px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .order-product-title {
        color: #111827;
        font-size: 17px;
        font-weight: 800;
        line-height: 1.35;
        margin: 0 0 6px;
    }

    .shipment-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #374151;
        background: #f3f4f6;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
    }

    /* ── Redesigned Order Progress (no phantom line) ── */
    .order-progress {
        position: relative;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-top: 18px;
        padding-top: 10px;
    }

    /* Grey background track — only between first and last dot centres (~12% to ~88%) */
    .order-progress-track {
        position: absolute;
        top: 22px;
        left: 12.5%;
        width: 75%;
        height: 3px;
        border-radius: 999px;
        background: #e5e7eb;
    }

    /* Active fill — width set inline via style attribute */
    .order-progress-fill {
        position: absolute;
        top: 22px;
        left: 12.5%;
        height: 3px;
        border-radius: 999px;
        background: #111827;
        transition: width 0.4s ease;
    }

    .progress-step {
        position: relative;
        z-index: 1;
        text-align: center;
        color: #9ca3af;
        font-size: 11px;
        font-weight: 700;
    }

    .progress-dot {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        margin: 0 auto 7px;
        border: 2px solid #e5e7eb;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }

    .progress-step.done .progress-dot,
    .progress-step.active .progress-dot {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .progress-step.done,
    .progress-step.active {
        color: #111827;
    }

    .order-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .order-action-btn {
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 12px;
    }

    @media (max-width: 767.98px) {
        .orders-toolbar,
        .order-card-head {
            flex-direction: column;
            align-items: stretch;
        }

        .order-product-img { width: 76px; height: 92px; }
        .tracking-info-grid { grid-template-columns: 1fr; }
        .order-progress { overflow-x: auto; min-width: 420px; }
    }
</style>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card text-center p-4 mb-4">
                <div class="position-relative mx-auto mb-3">
                    @if(Auth::user()->avatar != null)
                    <img src="{{ Auth::user()->avatar }}" width="80" class="rounded-circle border border-3 border-white shadow-sm" />
                    @else
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="80" class="rounded-circle border border-3 border-white shadow-sm" />
                    @endif
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2"></span>
                </div>
                <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                <small class="text-muted">{{ Auth::user()->email }}</small>
            </div>

            <div class="card p-3">
                <div class="nav flex-column nav-pills" role="tablist">
                    <button class="nav-link active d-flex justify-content-between align-items-center" data-bs-toggle="pill" data-bs-target="#dashboard">
                        <span><i class="bi bi-grid-fill me-2"></i> Dashboard</span>
                        <i class="bi bi-chevron-right small"></i>
                    </button>
                    <button class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="pill" data-bs-target="#profile">
                        <span><i class="bi bi-person-fill me-2"></i> My Profile</span>
                        <i class="bi bi-chevron-right small"></i>
                    </button>
                    <button class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="pill" data-bs-target="#addresses">
                        <span><i class="bi bi-geo-alt-fill me-2"></i> Addresses</span>
                        <i class="bi bi-chevron-right small"></i>
                    </button>
                    <button class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="pill" data-bs-target="#orders">
                        <span><i class="bi bi-bag-fill me-2"></i> My Orders</span>
                        <i class="bi bi-chevron-right small"></i>
                    </button>
                    <button class="nav-link d-flex justify-content-between align-items-center">
                        <form method="post" action="{{ route('account.logout') }}" id="logoutForm">
                            @csrf
                            <span onclick="document.getElementById('logoutForm').submit()">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </span>
                        </form>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="tab-content account-tabs">

                {{-- Dashboard Tab --}}
                <div class="tab-pane fade show active" id="dashboard">
                    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                        <div class="col">
                            <div class="card text-center p-3 h-100">
                                <div class="stat-card-icon">
                                    <i class="bi bi-box-seam fs-4"></i>
                                </div>
                                <h6 class="text-muted small text-uppercase fw-bold">Orders</h6>
                                <h3 class="fw-bold mb-0">{{ count($orders) }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center p-3 h-100">
                                <div class="stat-card-icon">
                                    <i class="bi bi-wallet2 fs-4"></i>
                                </div>
                                <h6 class="text-muted small text-uppercase fw-bold">Spend</h6>
                                <h3 class="fw-bold mb-0">₹ {{ $totalSpend }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="card p-4 h-100 d-flex flex-column justify-content-center align-items-start">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-dark text-white rounded p-2 me-3">
                                        <i class="bi bi-receipt fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Recent Orders</h5>
                                        <small class="text-muted">Track, return or exchange items</small>
                                    </div>
                                </div>
                                <button class="btn btn-outline-dark w-100 mt-2" onclick="document.querySelector('[data-bs-target=\'#orders\']').click()">
                                    View Order History
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card p-4 h-100">
                                <h5 class="fw-bold mb-1">Shipping</h5>
                                <p class="text-muted small mb-3">Manage your delivery locations</p>
                                <button class="btn btn-dark w-100 mt-auto" onclick="document.querySelector('[data-bs-target=\'#addresses\']').click()">
                                    Manage Addresses
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profile Tab --}}
                <div class="tab-pane fade" id="profile">
                    <div class="card p-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="fw-bold mb-0">Personal Information</h4>
                            <button class="btn btn-sm btn-dark text-white fw-bold" onclick="enableEdit()" id="editBtn">
                                Edit Details <i class="bi bi-pencil-square me-1"></i>
                            </button>
                        </div>
                        <form id="profileForm">
                            <div class="row g-4">
                                @php $name = explode(' ', Auth::user()->name); @endphp
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">First Name</label>
                                    <input type="text" class="form-control-plaintext" value="{{ $name[0] }}" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Last Name</label>
                                    <input type="text" class="form-control-plaintext" value="{{ $name[1] ?? '' }}" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Email Address</label>
                                    <input type="email" class="form-control-plaintext" value="{{ Auth::user()->email }}" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Mobile</label>
                                    <input type="text" class="form-control-plaintext" value="{{ Auth::user()->mobile ?? '' }}" readonly />
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4 d-none" id="actionButtons">
                                <button type="button" class="btn btn-light" onclick="cancelEdit()">Cancel</button>
                                <button type="submit" class="btn btn-dark">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Addresses Tab --}}
                <div class="tab-pane fade" id="addresses">
                    <div id="addressList">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="fw-bold mb-1">My Addresses</h4>
                                <p class="text-muted mb-0">Manage the addresses used for checkout and delivery.</p>
                            </div>
                            <button type="button" class="btn btn-dark" onclick="showForm()">
                                <i class="bi bi-plus-lg me-1"></i> Add Address
                            </button>
                        </div>

                        <div class="row g-4">
                            @forelse($addresses as $address)
                            <div class="col-md-6">
                                <div class="card p-4 h-100">
                                    <div class="d-flex justify-content-between">
                                        @if($address->is_default == 1)
                                        <span class="badge bg-dark">Default</span>
                                        @else
                                        <form method="POST" action="{{ route('account.addresses.default', $address) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-dark">Make Default</button>
                                        </form>
                                        @endif
                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><button class="dropdown-item" type="button" onclick="toggleAddressEdit({{ $address->id }})">Edit</button></li>
                                                <li>
                                                    <form method="POST" action="{{ route('account.addresses.delete', $address) }}" onsubmit="return confirm('Delete this address?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold">{{ $address->full_name }}</h5>
                                        <span>{{ ucwords(str_replace('_', ' ', $address->address_type)) }}</span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        {{ $address->address_line_1 }}, {{ $address->address_line_2 }},
                                        {{ $address->landmark }}, {{ $address->city }}, {{ $address->state }},
                                        {{ $address->country }}, {{ $address->pincode }}
                                    </p>
                                    <p class="text-muted small mt-2 mb-0">
                                        <i class="bi bi-telephone me-1"></i>{{ $address->phone }}
                                    </p>

                                    <form id="addressEdit{{ $address->id }}" class="mt-3 d-none" method="POST" action="{{ route('account.addresses.update', $address) }}">
                                        @csrf
                                        @method('PUT')
                                        @include('user.partials.address-form-fields', ['address' => $address])
                                        <div class="d-flex gap-2 mt-3">
                                            <button type="submit" class="btn btn-dark btn-sm">Save</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAddressEdit({{ $address->id }})">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="card p-5 text-center">
                                    <div class="mb-3 text-muted"><i class="bi bi-geo-alt fs-1"></i></div>
                                    <h4>No Addresses Yet</h4>
                                    <p class="text-muted">Add your first address to speed up checkout.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>

                        @if($addresses->isEmpty())
                            <div class="card p-4 mt-4" id="emptyAddressForm">
                                <h4 class="fw-bold mb-3">Add Address</h4>
                                <form method="POST" action="{{ route('account.addresses.store') }}">
                                    @csrf
                                    @include('user.partials.address-form-fields', ['address' => null])
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="submit" class="btn btn-dark">Save Address</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if($addresses->isNotEmpty())
                        <div id="addressForm" class="card p-4 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="fw-bold mb-0">Add Address</h4>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="hideForm()">Back</button>
                            </div>
                            <form method="POST" action="{{ route('account.addresses.store') }}">
                                @csrf
                                @include('user.partials.address-form-fields', ['address' => null])
                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-dark">Save Address</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="hideForm()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Orders Tab --}}
                <div class="tab-pane fade" id="orders">
                    <div class="orders-toolbar">
                        <div>
                            <h4 class="fw-bold">Your Orders</h4>
                            <div class="subtitle">Track shipments, view invoices, and review recent purchases.</div>
                        </div>
                        <span class="badge bg-dark rounded-pill px-3 py-2">{{ count($orders) }} Orders</span>
                    </div>
                    <div id="ordersList">
                        <div class="row g-3">
                            @forelse($orders as $order)
                                @foreach($order->items as $item)
                                @php
                                    $statusSteps = ['confirmed', 'packed', 'shipped', 'delivered'];
                                    $normalizedStatus = strtolower($order->order_status ?? 'pending');
                                    $trackingStatus = strtolower((string) ($order->tracking_status ?? ''));

                                    if ($order->delivered_at || str_contains($trackingStatus, 'deliver')) {
                                        $normalizedStatus = 'delivered';
                                    } elseif (str_contains($trackingStatus, 'transit') || str_contains($trackingStatus, 'shipped') || str_contains($trackingStatus, 'pickup')) {
                                        $normalizedStatus = 'shipped';
                                    }

                                    $statusIndex = array_search($normalizedStatus, $statusSteps);
                                    $statusIndex = $statusIndex === false ? 0 : $statusIndex;

                                    // Fill width: spans from first to last dot (75% of container)
                                    // Each step advances by 25% of 75% = 18.75% of total
                                    $fillWidths = [0, 25, 50, 75];
                                    $fillWidth = $normalizedStatus === 'cancelled' ? 0 : $fillWidths[$statusIndex];

                                    $statusClass = 'order-status-' . $normalizedStatus;
                                    $statusLabel = $normalizedStatus === 'delivered'
                                        ? 'Delivered'
                                        : ucwords(str_replace('_', ' ', $order->order_status));
                                    $productImage = $item->product_image ? asset('storage/' . $item->product_image) : 'https://via.placeholder.com/180x220/f3f4f6/9ca3af?text=Design+Dhaga';
                                @endphp
                                <div class="col-12">
                                    <div class="order-card">
                                        <div class="order-card-head">
                                            <div>
                                                <div class="order-id">Order #{{ $order->id }}</div>
                                                <div class="order-meta">
                                                    <span><i class="bi bi-calendar3 me-1"></i>{{ ist($order->created_at)?->format('M d, Y') }}</span>
                                                    <span><i class="bi bi-credit-card me-1"></i>{{ ucfirst($order->payment_status) }}</span>
                                                    @if($order->expected_delivery_date)
                                                        <span><i class="bi bi-clock-history me-1"></i>Expected {{ $order->expected_delivery_date->format('M d') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="order-status-pill {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        <div class="order-card-body">
                                            <div class="row g-4 align-items-start">

                                                {{-- Product Image --}}
                                                <div class="col-md-2 col-4">
                                                    <img src="{{ $productImage }}" class="order-product-img" alt="{{ $item->product_name }}">
                                                </div>

                                                {{-- Product Info --}}
                                                <div class="col-md-5 col-8">
                                                    <h5 class="order-product-title">{{ $item->product_name }}</h5>
                                                    @if($item->size || $item->fabric_type || $item->sku)
                                                        <div class="text-muted small">
                                                            @if($item->fabric_type) Fabric: {{ $item->fabric_type }} @endif
                                                            @if($item->size) {{ $item->fabric_type ? ' | ' : '' }}Size: {{ $item->size }} @endif
                                                            @if($item->sku) {{ ($item->fabric_type || $item->size) ? ' | ' : '' }}SKU: {{ $item->sku }} @endif
                                                        </div>
                                                    @endif
                                                    <div class="text-muted small">Qty: {{ $item->quantity }} | Item total ₹{{ number_format($item->total, 2) }}</div>

                                                    @if($order->awb_code)
                                                        <div class="shipment-chip">
                                                            <i class="bi bi-truck"></i>
                                                            {{ $order->courier_name ?? 'Courier' }} | AWB {{ $order->awb_code }}
                                                        </div>
                                                    @else
                                                        <div class="shipment-chip">
                                                            <i class="bi bi-box-seam"></i>
                                                            Shipment will be assigned soon
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Tracking Progress --}}
                                                <div class="col-md-3">
                                                    @if($normalizedStatus === 'cancelled')
                                                        <div class="alert alert-danger py-2 px-3 small mb-0">
                                                            This order has been cancelled.
                                                        </div>
                                                    @else
                                                        <div class="order-progress">
                                                            <div class="order-progress-track"></div>
                                                            <div class="order-progress-fill" style="width: {{ $fillWidth }}%;"></div>
                                                            @foreach($statusSteps as $stepIndex => $step)
                                                                <div class="progress-step {{ $stepIndex < $statusIndex ? 'done' : ($stepIndex === $statusIndex ? 'active' : '') }}">
                                                                    <div class="progress-dot">
                                                                        <i class="bi {{ $stepIndex <= $statusIndex ? 'bi-check' : 'bi-circle' }}"></i>
                                                                    </div>
                                                                    {{ ucfirst($step) }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Actions --}}
                                                <div class="col-md-2">
                                                    <div class="order-actions">
                                                        <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-outline-dark order-action-btn">
                                                            <i class="bi bi-receipt me-1"></i> Invoice
                                                        </a>

                                                        @if($order->order_status === 'delivered')
                                                            
                                                        @elseif($order->awb_code && $order->order_status === 'shipped')
                                                            <button class="btn btn-dark order-action-btn" onclick="openTracking('{{ $order->awb_code }}', '#{{ $order->id }}', '{{ $order->id }}')">
                                                                <i class="bi bi-geo-alt me-1"></i> Track
                                                            </button>

                                                        @else
                                                            <button class="btn btn-light order-action-btn" disabled>
                                                                <i class="bi bi-hourglass-split me-1"></i> Awaiting AWB
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>

                                            @if($order->delivered_at)
                                                <div class="mt-3 text-success small fw-semibold">
                                                    <i class="bi bi-check-circle-fill me-1"></i>
                                                    Delivered on {{ ist($order->delivered_at)?->format('M d, Y') }}
                                                </div>
                                            @elseif($order->delivery_eta)
                                                <div class="mt-3 text-muted small">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Estimated delivery: {{ $order->delivery_eta }} days after dispatch.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @empty
                                <div class="col-12">
                                    <div class="card p-5 text-center">
                                        <div class="mb-3 text-muted">
                                            <i class="bi bi-bag-x fs-1"></i>
                                        </div>
                                        <h4>No Orders Yet</h4>
                                        <p class="text-muted">Start shopping to see your orders here.</p>
                                        <a href="{{ route('home') }}" class="btn btn-dark mt-2">Start Shopping</a>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ── Tracking Modal ── --}}
<div class="modal fade tracking-modal" id="trackingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Header --}}
            <div class="tracking-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5><i class="bi bi-geo-alt-fill me-2"></i> Order Tracking</h5>
                        <small id="trackingOrderId">Loading...</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>

            {{-- Delivery Summary Banner --}}
            <div id="trackingDeliverySummary" style="display:none;" class="tracking-delivery-summary">
                <div class="tracking-delivery-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="tracking-delivery-primary" id="trackingDeliveryPrimary">Package delivered</div>
                    <div class="tracking-delivery-secondary" id="trackingDeliverySecondary"></div>
                </div>
            </div>

            {{-- In-transit banner --}}
            <div id="trackingTransitSummary" style="display:none; background:#eff6ff; border-bottom:1px solid #bfdbfe; padding:14px 22px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:36px;height:36px;border-radius:50%;background:#fff;border:1px solid #bfdbfe;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#1d4ed8;font-size:16px;">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#1d4ed8;" id="trackingTransitPrimary">On the way</div>
                        <div style="font-size:12px;color:#1e40af;" id="trackingTransitSecondary"></div>
                    </div>
                </div>
            </div>

            {{-- Progress Steps --}}
            <div class="tracking-steps-section" id="trackingStepsSection">
                <div class="tracking-steps-label">Shipment progress</div>
                <div class="tracking-steps-row" id="trackingStepsRow">
                    <div class="track-fill" id="trackFill" style="width:0%;"></div>
                    <div class="tracking-step" id="step-confirmed">
                        <div class="tracking-step-dot"><i class="bi bi-circle"></i></div>
                        <div class="tracking-step-label">Confirmed</div>
                    </div>
                    <div class="tracking-step" id="step-packed">
                        <div class="tracking-step-dot"><i class="bi bi-circle"></i></div>
                        <div class="tracking-step-label">Packed</div>
                    </div>
                    <div class="tracking-step" id="step-shipped">
                        <div class="tracking-step-dot"><i class="bi bi-circle"></i></div>
                        <div class="tracking-step-label">Shipped</div>
                    </div>
                    <div class="tracking-step" id="step-delivered">
                        <div class="tracking-step-dot"><i class="bi bi-circle"></i></div>
                        <div class="tracking-step-label">Delivered</div>
                    </div>
                </div>
            </div>

            {{-- Info strip (date/ETA only) --}}
            <div class="tracking-info-strip" id="trackingInfoStrip" style="display:none;">
                <i class="bi bi-calendar-check"></i>
                <span id="trackingInfoStripText"></span>
            </div>

            {{-- Timeline --}}
            <div class="tracking-timeline" id="trackingTimeline">
                <p class="text-muted text-center py-3">
                    <span class="skeleton d-inline-block" style="height:14px;width:200px;">&nbsp;</span>
                </p>
            </div>

        </div>
    </div>
</div>
<script>
    const textData = [];
    @foreach($highlights as $highlight)
    textData.push(`<span>{{ $highlight->title }}</span>
             <img src="{{ Storage::url($highlight->emoji) }}"
                  class="emoji"
                  alt="{{ $highlight->alt_text ?? $highlight->title }}">`);
    @endforeach
</script>
@endsection

@push('scripts')
<script>
    const trackUrl = "{{ url('order/track') }}";

    function openTracking(awbCode, orderId, localOrderId = null) {
        document.getElementById('trackingOrderId').textContent = 'Order ' + orderId;
        document.getElementById('trackingDeliverySummary').style.display = 'none';
        document.getElementById('trackingTransitSummary').style.display = 'none';
        document.getElementById('trackingInfoStrip').style.display = 'none';

        // Reset steps
        ['confirmed','packed','shipped','delivered'].forEach(s => {
            const el = document.getElementById('step-' + s);
            if (el) {
                el.className = 'tracking-step';
                el.querySelector('.tracking-step-dot').innerHTML = '<i class="bi bi-circle"></i>';
            }
        });
        document.getElementById('trackFill').style.width = '0%';

        document.getElementById('trackingTimeline').innerHTML = `
            <p class="text-center py-4 text-muted">
                <i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite;display:inline-block;"></i>
                &nbsp;Fetching tracking details...
            </p>`;

        const modal = new bootstrap.Modal(document.getElementById('trackingModal'));
        modal.show();

        fetch(`${trackUrl}/${awbCode}`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) throw new Error(res.message || 'Failed to fetch tracking');
                renderTracking(res.data, orderId, awbCode, localOrderId);
            })
            .catch(err => {
                document.getElementById('trackingTimeline').innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                        <p class="mb-0">${err.message}</p>
                    </div>`;
            });
    }

    function esc(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;').replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;').replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function normalizeTrackingStatus(status) {
        const lower = String(status ?? '').toLowerCase();
        if (lower.includes('deliver')) return 'delivered';
        if (lower.includes('out for')) return 'shipped';
        if (lower.includes('transit') || lower.includes('pickup') || lower.includes('shipped') || lower.includes('manifest')) return 'shipped';
        if (lower.includes('packed') || lower.includes('ready') || lower.includes('assigned')) return 'packed';
        if (lower.includes('cancel') || lower.includes('rto')) return 'cancelled';
        return 'shipped';
    }

    function activityTitle(act) {
        const raw = act['sr-status-label'] || act['sr_status_label'] || act.activity || act.status || act['sr-status'];
        const lower = String(raw ?? '').toLowerCase();
        if (lower.includes('deliver')) return 'Delivered';
        if (lower.includes('out for')) return 'Out for delivery';
        if (lower.includes('pickup')) return 'Picked up by courier';
        if (lower.includes('transit') || lower.includes('shipped')) return 'In transit';
        if (lower.includes('manifest') || lower.includes('assign')) return 'Shipment booked';
        if (/^\d+$/.test(String(raw ?? ''))) return 'Shipment update';
        return raw || 'Shipment update';
    }

    function updateOrderCardStatus(orderId, normalizedStatus, label) {
        const button = document.querySelector(`[onclick*="'#${orderId}'"]`);
        const card = button ? button.closest('.order-card') : null;
        if (!card) return;

        const pill = card.querySelector('.order-status-pill');
        if (pill) {
            pill.className = `order-status-pill order-status-${normalizedStatus}`;
            pill.textContent = normalizedStatus === 'delivered' ? 'Delivered' : label;
        }

        const progress = card.querySelector('.order-progress');
        if (progress && normalizedStatus !== 'cancelled') {
            const steps = ['confirmed', 'packed', 'shipped', 'delivered'];
            const index = Math.max(0, steps.indexOf(normalizedStatus));
            const fillWidths = [0, 25, 50, 75];
            const fill = progress.querySelector('.order-progress-fill');
            if (fill) fill.style.width = fillWidths[index] + '%';

            progress.querySelectorAll('.progress-step').forEach((step, stepIndex) => {
                step.classList.toggle('done', stepIndex < index);
                step.classList.toggle('active', stepIndex === index);
                const icon = step.querySelector('i');
                if (icon) icon.className = `bi ${stepIndex <= index ? 'bi-check' : 'bi-circle'}`;
            });
        }
    }

    function renderTracking(data, orderId, awbCode, localOrderId = null) {
        const status = data.current_status || 'In transit';
        const normalizedStatus = normalizeTrackingStatus(status);

        if (localOrderId) {
            updateOrderCardStatus(localOrderId, normalizedStatus, status);
        }

        // ── Banner ──────────────────────────────────────
        if (normalizedStatus === 'delivered') {
            document.getElementById('trackingDeliverySummary').style.display = 'flex';
            document.getElementById('trackingDeliveryPrimary').textContent = 'Package delivered';
            document.getElementById('trackingDeliverySecondary').textContent =
                data.delivered_date ? `Delivered on ${data.delivered_date}` : 'Your order has been delivered';
        } else if (normalizedStatus === 'shipped') {
            document.getElementById('trackingTransitSummary').style.display = 'block';
            document.getElementById('trackingTransitPrimary').textContent = 'On the way';
            document.getElementById('trackingTransitSecondary').textContent =
                data.eta ? `Expected by ${data.eta}` : 'Your order is in transit';
        }

        // ── Progress steps ──────────────────────────────
        const stepOrder = ['confirmed', 'packed', 'shipped', 'delivered'];
        const stepIndex = Math.max(0, stepOrder.indexOf(normalizedStatus));

        // Fill width: track spans 75% of row (left 12.5% → right 12.5%).
        // Each of the 3 gaps = 25% of that 75% span = 25% of container width.
        // stepIndex 0 → 0%, 1 → 25%, 2 → 50%, 3 → 75%
        const fillWidthPct = [0, 25, 50, 75][stepIndex];
        document.getElementById('trackFill').style.width = fillWidthPct + '%';

        stepOrder.forEach((step, i) => {
            const el = document.getElementById('step-' + step);
            if (!el) return;
            if (i <= stepIndex) {
                el.className = 'tracking-step done';
                el.querySelector('.tracking-step-dot').innerHTML = '<i class="bi bi-check"></i>';
            } else {
                el.className = 'tracking-step';
                el.querySelector('.tracking-step-dot').innerHTML = '<i class="bi bi-circle"></i>';
            }
        });

        // ── Info strip (date/ETA only) ──────────────────
        const stripEl = document.getElementById('trackingInfoStrip');
        const stripText = document.getElementById('trackingInfoStripText');
        if (data.delivered_date) {
            stripText.innerHTML = `Delivered on <strong>${esc(data.delivered_date)}</strong>`;
            stripEl.style.display = 'flex';
        } else if (data.eta) {
            stripText.innerHTML = `Estimated delivery: <strong>${esc(data.eta)}</strong>`;
            stripEl.style.display = 'flex';
        }

        // ── Timeline ────────────────────────────────────
        const activities = data.activities ?? [];

        if (activities.length === 0) {
            document.getElementById('trackingTimeline').innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                    <p class="mb-0">No tracking activity yet.</p>
                </div>`;
            return;
        }

        // Group consecutive activities with the same milestone label together.
        // The first (most recent) activity is the active milestone; rest are inactive.
        const milestoneOrder = ['delivered', 'out for delivery', 'in transit', 'picked up', 'shipment booked'];

        function getMilestone(act) {
            const t = activityTitle(act).toLowerCase();
            if (t.includes('deliver') && !t.includes('out')) return 'Delivered';
            if (t.includes('out for')) return 'Out For Delivery';
            if (t.includes('transit') || t.includes('shipped')) return 'In Transit';
            if (t.includes('pickup') || t.includes('picked')) return 'Picked Up';
            if (t.includes('manifest') || t.includes('booked') || t.includes('confirmed')) return 'Order Confirmed';
            return activityTitle(act);
        }

        // Group by milestone label
        const groups = [];
        activities.forEach(act => {
            const label = getMilestone(act);
            const last = groups[groups.length - 1];
            if (last && last.label === label) {
                last.items.push(act);
            } else {
                groups.push({ label, items: [act] });
            }
        });

        const timelineHtml = `<div class="tracking-section-title">Shipment activity</div>` +
            groups.map((group, gi) => {
                const isActive = gi === 0;
                // Use date from first item in group as the milestone date
                const milestoneDate = group.items[0]?.date ?? '';
                // Sub-items: show location + time for each activity in the group
                const subHtml = group.items.map(act => {
                    const loc = act.location ?? '';
                    const dt  = act.date ?? '';
                    return `<div class="tl-sub">${esc(loc)}${loc && dt ? ' <span class="tl-sub-time">· ' + esc(dt) + '</span>' : esc(dt)}</div>`;
                }).join('');

                return `
                <div class="tl-group ${isActive ? '' : 'inactive'}">
                    <div class="tl-spine">
                        <div class="tl-dot ${isActive ? '' : 'inactive'}"></div>
                    </div>
                    <div class="tl-body">
                        <div class="tl-head">
                            <span class="tl-label">${esc(group.label)}</span>
                            <span class="tl-date">${esc(milestoneDate)}</span>
                        </div>
                        ${subHtml}
                    </div>
                </div>`;
            }).join('');

        document.getElementById('trackingTimeline').innerHTML = timelineHtml;
    }

    // Navigation persistence and page-local tab fallback
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.nav-pills .nav-link[data-bs-target]');
        const tabPanes = document.querySelectorAll('.account-tabs .tab-pane');

        function setActiveAccountTab(target) {
            const pane = document.querySelector(target);
            const trigger = document.querySelector(`.nav-pills .nav-link[data-bs-target='${target}']`);

            if (!pane || !trigger) return;

            tabButtons.forEach(function (btn) {
                btn.classList.toggle('active', btn === trigger);
                btn.setAttribute('aria-selected', btn === trigger ? 'true' : 'false');
            });

            tabPanes.forEach(function (tabPane) {
                const isActive = tabPane === pane;
                tabPane.classList.toggle('show', isActive);
                tabPane.classList.toggle('active', isActive);
            });

            localStorage.setItem('dashboard-active-tab', target);
        }

        const savedTab = localStorage.getItem('dashboard-active-tab');
        const initialTab = savedTab && document.querySelector(savedTab) ? savedTab : '#dashboard';
        setActiveAccountTab(initialTab);

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                const target = btn.getAttribute('data-bs-target');
                if (target) {
                    event.preventDefault();
                    setActiveAccountTab(target);
                }
            });
        });
    });

    // Profile edit
    function enableEdit() {
        document.querySelectorAll("#profileForm input, #profileForm select").forEach((el) => {
            el.removeAttribute("readonly");
            el.removeAttribute("disabled");
            el.classList.remove("form-control-plaintext");
            el.classList.add("form-control");
        });
        document.getElementById("actionButtons").classList.remove("d-none");
        document.getElementById("editBtn").classList.add("d-none");
    }

    function cancelEdit() {
        document.querySelectorAll("#profileForm input").forEach((el) => {
            el.setAttribute("readonly", true);
            el.classList.remove("form-control");
            el.classList.add("form-control-plaintext");
        });
        const sel = document.querySelector("#profileForm select");
        if (sel) {
            sel.setAttribute("disabled", true);
            sel.classList.remove("form-control");
            sel.classList.add("form-control-plaintext");
        }
        document.getElementById("actionButtons").classList.add("d-none");
        document.getElementById("editBtn").classList.remove("d-none");
    }

    // Address form
    function showForm() {
        const form = document.getElementById("addressForm");
        const emptyForm = document.getElementById("emptyAddressForm");

        if (!form && emptyForm) {
            emptyForm.scrollIntoView({ behavior: "smooth", block: "start" });
            const firstInput = emptyForm.querySelector("input, textarea, select");
            if (firstInput) firstInput.focus();
            return;
        }

        document.getElementById("addressList").classList.add("d-none");
        if (form) form.classList.remove("d-none");
    }

    function hideForm() {
        const form = document.getElementById("addressForm");
        if (form) form.classList.add("d-none");
        document.getElementById("addressList").classList.remove("d-none");
    }

    function toggleAddressEdit(id) {
        const form = document.getElementById("addressEdit" + id);
        if (form) form.classList.toggle("d-none");
    }

    // Keep user on Addresses tab after any address action submit
    document.querySelectorAll(
        '#addresses form[action*="/account/addresses"], #addressForm form[action*="/account/addresses"], #emptyAddressForm form[action*="/account/addresses"]'
    ).forEach((form) => {
        form.addEventListener('submit', function () {
            localStorage.setItem('dashboard-active-tab', '#addresses');
        });
    });

    const style = document.createElement('style');
    style.textContent = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
</script>
@endpush
