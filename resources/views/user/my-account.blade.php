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
        --accent-color: #4f46e5;
        --bg-color: #f8f9fa;
        --card-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    body {
        font-family: "Poppins", sans-serif;
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

    /* ── Tracking Modal ── */
    .tracking-modal .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }

    .tracking-header {
        background: linear-gradient(135deg, #212529 0%, #495057 100%);
        color: white;
        padding: 24px;
    }

    .tracking-header h5 {
        font-weight: 700;
        margin: 0;
    }

    .tracking-header small {
        opacity: 0.75;
        font-size: 12px;
    }

    .tracking-status-badge {
        display: inline-block;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        margin-top: 8px;
    }

    .tracking-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 20px;
        background: #f8f9fa;
    }

    .tracking-info-item {
        background: #fff;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .tracking-info-item .label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 600;
        color: #9ca3af;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .tracking-info-item .value {
        font-size: 14px;
        font-weight: 600;
        color: #212529;
    }

    /* Timeline */
    .tracking-timeline {
        padding: 20px;
        max-height: 320px;
        overflow-y: auto;
    }

    .tracking-timeline::-webkit-scrollbar {
        width: 4px;
    }

    .tracking-timeline::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .tracking-timeline::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .timeline-item {
        display: flex;
        gap: 16px;
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 32px;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #212529;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        z-index: 1;
    }

    .timeline-dot.inactive {
        background: #e5e7eb;
    }

    .timeline-dot i {
        font-size: 13px;
        color: white;
    }

    .timeline-dot.inactive i {
        color: #9ca3af;
    }

    .timeline-content {
        flex: 1;
        padding-top: 4px;
    }

    .timeline-content .activity {
        font-size: 13px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
    }

    .timeline-content .location {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 2px;
    }

    .timeline-content .time {
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
            <div class="tab-content">

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
                        <div class="row g-4">
                            @foreach($addresses as $address)
                            <div class="col-md-6">
                                <div class="card p-4 h-100">
                                    <div class="d-flex justify-content-between">
                                        @if($address->is_default == 1)
                                        <span class="badge bg-dark">Default</span>
                                        @endif
                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
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
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Orders Tab --}}
                <div class="tab-pane fade" id="orders">
                    <h4 class="fw-bold mb-4">Your Orders</h4>
                    <div id="ordersList">
                        <div class="row g-3">
                            @forelse($orders as $order)
                                @foreach($order->items as $item)
                                <div class="col-12">
                                    <div class="card p-4 border-1">
                                        <div class="row g-4 align-items-start">

                                            {{-- Product Image --}}
                                            <div class="col-md-2">
                                                <img src="{{ asset('storage/' . $item->product_image) }}" class="img-fluid rounded" alt="Product">
                                            </div>

                                            {{-- Product Info --}}
                                            <div class="col-md-5">
                                                <h4 class="fw-bold mb-2 mt-0">{{ $item->product_name }}</h4>
                                                <p class="text-muted d-block mb-2">Order ID: #{{ $order->id }}</p>
                                                <p class="text-muted d-block mb-2">Ordered on: {{ $order->created_at->format('M d, Y') }}</p>

                                                @php
                                                    $statusColors = [
                                                        'pending'   => 'secondary',
                                                        'confirmed' => 'info',
                                                        'packed'    => 'warning',
                                                        'shipped'   => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                    ];
                                                @endphp

                                                <span class="badge bg-{{ $statusColors[$order->order_status] ?? 'secondary' }}">
                                                    {{ ucfirst($order->order_status) }}
                                                </span>

                                                @if($order->delivered_at)
                                                    <small class="text-muted d-block mt-1">
                                                        Delivered on {{ $order->delivered_at->format('M d, Y') }}
                                                    </small>
                                                @endif

                                                {{-- Courier + AWB info --}}
                                                @if($order->awb_code)
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="bi bi-truck me-1"></i>
                                                        {{ $order->courier_name ?? 'Courier' }} &bull; AWB: {{ $order->awb_code }}
                                                    </small>
                                                @endif
                                            </div>

                                            {{-- Price --}}
                                            <div class="col-md-2">
                                                <p class="fw-bold">₹ {{ number_format($item->total, 2) }}</p>
                                                <p class="text-muted">Qty: {{ $item->quantity }}</p>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="col-md-3 text-end">
                                                <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-sm btn-outline-dark w-100 mb-2">
                                                    View Bill
                                                </a>

                                                {{-- Track Order Button — only if AWB exists --}}
                                                @if($order->awb_code)
                                                    <button
                                                        class="btn btn-sm btn-dark w-100"
                                                        onclick="openTracking('{{ $order->awb_code }}', '#{{ $order->id }}')"
                                                    >
                                                        <i class="bi bi-geo-alt me-1"></i> Track Order
                                                    </button>
                                                @endif
                                            </div>

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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            {{-- Header --}}
            <div class="tracking-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5><i class="bi bi-geo-alt-fill me-2"></i> Order Tracking</h5>
                        <small id="trackingOrderId">Loading...</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div id="trackingCurrentStatus">
                    <div class="skeleton mt-3" style="height: 28px; width: 160px; border-radius: 20px;"></div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="tracking-info-grid" id="trackingInfoGrid">
                <div class="tracking-info-item">
                    <div class="label">AWB Code</div>
                    <div class="value skeleton" style="height:18px; width:80%;">&nbsp;</div>
                </div>
                <div class="tracking-info-item">
                    <div class="label">Courier</div>
                    <div class="value skeleton" style="height:18px; width:70%;">&nbsp;</div>
                </div>
                <div class="tracking-info-item">
                    <div class="label">Origin</div>
                    <div class="value skeleton" style="height:18px; width:60%;">&nbsp;</div>
                </div>
                <div class="tracking-info-item">
                    <div class="label">Destination</div>
                    <div class="value skeleton" style="height:18px; width:60%;">&nbsp;</div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="tracking-timeline" id="trackingTimeline">
                <p class="text-muted text-center py-3">
                    <span class="skeleton d-inline-block" style="height:14px; width:200px;">&nbsp;</span>
                </p>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Tracking Modal ──────────────────────────────
    const trackUrl = "{{ url('order/track') }}";

    function openTracking(awbCode, orderId) {
        // Reset to skeleton state
        document.getElementById('trackingOrderId').textContent = 'Order ' + orderId;
        document.getElementById('trackingCurrentStatus').innerHTML = `
            <div class="skeleton mt-3" style="height:28px; width:160px; border-radius:20px;"></div>`;
        document.getElementById('trackingInfoGrid').innerHTML = `
            ${infoSkeleton('AWB Code')}${infoSkeleton('Courier')}
            ${infoSkeleton('Origin')}${infoSkeleton('Destination')}`;
        document.getElementById('trackingTimeline').innerHTML = `
            <p class="text-center py-4 text-muted">
                <i class="bi bi-arrow-repeat" style="animation:spin 1s linear infinite; display:inline-block;"></i>
                &nbsp;Fetching tracking details...
            </p>`;

        const modal = new bootstrap.Modal(document.getElementById('trackingModal'));
        modal.show();

        fetch(`${trackUrl}/${awbCode}`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) throw new Error(res.message || 'Failed to fetch tracking');
                renderTracking(res.data, orderId, awbCode);
            })
            .catch(err => {
                document.getElementById('trackingTimeline').innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                        <p class="mb-0">${err.message}</p>
                    </div>`;
                document.getElementById('trackingInfoGrid').innerHTML = '';
                document.getElementById('trackingCurrentStatus').innerHTML = `
                    <span class="tracking-status-badge">Unavailable</span>`;
            });
    }

    function infoSkeleton(label) {
        return `<div class="tracking-info-item">
            <div class="label">${label}</div>
            <div class="value skeleton" style="height:18px; width:70%;">&nbsp;</div>
        </div>`;
    }

    function renderTracking(data, orderId, awbCode) {
        // Status badge
        document.getElementById('trackingCurrentStatus').innerHTML =
            `<span class="tracking-status-badge">
                <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>
                ${data.current_status}
             </span>`;

        // Info grid
        document.getElementById('trackingInfoGrid').innerHTML = `
            <div class="tracking-info-item">
                <div class="label">AWB Code</div>
                <div class="value">${data.awb_code ?? awbCode}</div>
            </div>
            <div class="tracking-info-item">
                <div class="label">Courier</div>
                <div class="value">${data.courier_name ?? '—'}</div>
            </div>
            <div class="tracking-info-item">
                <div class="label">Origin</div>
                <div class="value">${data.origin ?? '—'}</div>
            </div>
            <div class="tracking-info-item">
                <div class="label">Destination</div>
                <div class="value">${data.destination ?? '—'}</div>
            </div>
            ${data.eta ? `
            <div class="tracking-info-item">
                <div class="label">Estimated Delivery</div>
                <div class="value">${data.eta}</div>
            </div>` : ''}
            ${data.delivered_date ? `
            <div class="tracking-info-item">
                <div class="label">Delivered On</div>
                <div class="value">${data.delivered_date}</div>
            </div>` : ''}
        `;

        // Timeline
        const activities = data.activities ?? [];

        if (activities.length === 0) {
            document.getElementById('trackingTimeline').innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                    <p class="mb-0">No tracking activity yet.</p>
                </div>`;
            return;
        }

        const icons = {
            'delivered'  : 'bi-check-circle-fill',
            'picked up'  : 'bi-box-seam',
            'in transit' : 'bi-truck',
            'out for'    : 'bi-bicycle',
            'rto'        : 'bi-arrow-return-left',
            'default'    : 'bi-geo-alt-fill',
        };

        function getIcon(activity) {
            const lower = (activity ?? '').toLowerCase();
            for (const [key, cls] of Object.entries(icons)) {
                if (lower.includes(key)) return cls;
            }
            return icons.default;
        }

        const timelineHtml = activities.map((act, index) => `
            <div class="timeline-item">
                <div class="timeline-dot ${index > 0 ? 'inactive' : ''}">
                    <i class="bi ${getIcon(act['sr-status'] ?? act.activity ?? '')}"></i>
                </div>
                <div class="timeline-content">
                    <div class="activity">${act['sr-status'] ?? act.activity ?? 'Update'}</div>
                    <div class="location"><i class="bi bi-geo me-1"></i>${act.location ?? '—'}</div>
                    <div class="time"><i class="bi bi-clock me-1"></i>${act.date ?? ''}</div>
                </div>
            </div>
        `).join('');

        document.getElementById('trackingTimeline').innerHTML = timelineHtml;
    }

    // ── Navigation Persistence ──────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const lastTab = localStorage.getItem('dashboard-active-tab');
        if (lastTab) {
            const trigger = document.querySelector(`[data-bs-target='${lastTab}']`);
            if (trigger) trigger.click();
        }

        document.querySelectorAll('.nav-pills .nav-link').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const target = btn.getAttribute('data-bs-target');
                if (target) localStorage.setItem('dashboard-active-tab', target);
            });
        });
    });

    // ── Profile Edit ────────────────────────────────
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

    // ── Address Form ────────────────────────────────
    function showForm() {
        document.getElementById("addressList").classList.add("d-none");
        document.getElementById("addressForm").classList.remove("d-none");
    }

    function hideForm() {
        document.getElementById("addressForm").classList.add("d-none");
        document.getElementById("addressList").classList.remove("d-none");
    }

    // Spinner for loading state
    const style = document.createElement('style');
    style.textContent = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
</script>
@endpush