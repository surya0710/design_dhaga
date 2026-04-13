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
        /* Dark/Black */
        --accent-color: #4f46e5;
        /* Modern Indigo for highlights */
        --bg-color: #f8f9fa;
        --card-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    body {
        font-family: "Poppins", sans-serif;
        background-color: var(--bg-color);
        color: #4a5568;
    }

    /* Card Styling */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    /* Sidebar Navigation */
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

    /* Mobile 2x2 grid navigation */
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

    /* Buttons */
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

    /* Form Controls */
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

    /* Stat Cards */
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
</style>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card text-center p-4 mb-4">
                <div class="position-relative mx-auto mb-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="80"
                        class="rounded-circle border border-3 border-white shadow-sm" />
                    <span
                        class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2"></span>
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
                                <h6 class="text-muted small text-uppercase fw-bold">
                                    Spend
                                </h6>
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
                                <button class="btn btn-outline-dark w-100 mt-2">
                                    View Order History
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card p-4 h-100">
                                <h5 class="fw-bold mb-1">Shipping</h5>
                                <p class="text-muted small mb-3">
                                    Manage your delivery locations
                                </p>
                                <button class="btn btn-dark w-100 mt-auto" onclick="document.querySelector('[data-bs-target=\'#addresses\']').click()">
                                    Manage Addresses
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <input type="text" class="form-control-plaintext" value="{{ $name[1] }}" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Email Address</label>
                                    <input type="email" class="form-control-plaintext" value="{{ Auth::user()->email }}" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Mobile</label>
                                    <input type="text" class="form-control-plaintext" value="{{ isset(Auth::user()->mobile) ? Auth::user()->mobile : '' }}" readonly />
                                </div>
                                <!-- <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Birth Date</label>
                                    <input type="date" class="form-control-plaintext" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Gender</label>
                                    <select class="form-control-plaintext" disabled>
                                        <option>Select Gender</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div> -->
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4 d-none" id="actionButtons">
                                <button type="button" class="btn btn-light" onclick="cancelEdit()">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-dark">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade" id="addresses">
                    <!-- <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Saved Addresses</h4>
                        <button class="btn btn-dark btn-sm" onclick="showForm()">
                            <i class="bi bi-plus-lg"></i> Add New
                        </button>
                    </div> -->

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
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold">{{ $address->full_name }}</h5>
                                        <span>{{ ucwords(str_replace('_', ' ', $address->address_type)) }}</span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        {{ $address->address_line_1 }}, {{ $address->address_line_2 }}, {{ $address->landmark }}, {{ $address->city }}, {{ $address->state }}, {{ $address->country }}, {{ $address->pincode }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="addressForm" class="d-none">
                        <div class="card p-4">
                            <h5 class="fw-bold mb-4">Add New Destination</h5>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small text-muted">First Name</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Last Name</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Address 1</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Address 2
                                            <span class="text-muted">(Optional)</span></label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Phone</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Company
                                            <span class="text-muted">(Optional)</span></label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Country</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">State</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">City</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Zip Code</label>
                                        <input type="text" class="form-control" />
                                    </div>
                                    <div class="col-12 d-flex mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="defaultAddressCheck" />
                                            <label class="form-check-label" for="defaultAddressCheck">
                                                Make as Default Address
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-dark">
                                            Save Address
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary ms-2"
                                            onclick="hideForm()">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="orders">
                    <h4 class="fw-bold mb-4">Your Orders</h4>
                    <div id="ordersEmpty" class="card p-5 text-center d-none">
                        <div class="mb-3 text-muted">
                            <i class="bi bi-bag-x fs-1"></i>
                        </div>
                        <h4>No Orders Yet</h4>
                        <p class="text-muted">
                            Start shopping to see your orders here.
                        </p>
                        <button class="btn btn-dark mt-2">Start Shopping</button>
                    </div>
                    <div id="ordersList">
                        <div class="row g-3">

                            @forelse($orders as $order)

                                @foreach($order->items as $item)

                                <div class="col-12">
                                    <div class="card p-4 border-1">
                                        <div class="row g-4 align-items-start">

                                            <!-- Product Image -->
                                            <div class="col-md-2">
                                                <img src="{{ asset($item->product_image) }}"
                                                    class="img-fluid rounded" alt="Product">
                                            </div>

                                            <!-- Product Info -->
                                            <div class="col-md-5">
                                                <h4 class="fw-bold mb-2">{{ $item->product_name }}</h4>

                                                <p class="text-muted d-block mb-2">Order ID: #{{ $order->id }}</p>

                                                <p class="text-muted d-block mb-2">Ordered on: {{ $order->created_at->format('M d, Y') }}</p>

                                                <!-- Status Badge -->
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'secondary',
                                                        'confirmed' => 'info',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                    ];
                                                @endphp

                                                <span class="badge bg-{{ $statusColors[$order->order_status] ?? 'secondary' }}">
                                                    {{ ucfirst($order->order_status) }}
                                                </span>

                                                <!-- Delivery Date -->
                                                @if($order->delivered_at)
                                                    <small class="text-muted d-block mt-1">
                                                        Delivered on {{ $order->delivered_at->format('M d, Y') }}
                                                    </small>
                                                @endif
                                            </div>

                                            <!-- Price -->
                                            <div class="col-md-2">
                                                <h6 class="fw-bold">
                                                    ₹ {{ number_format($item->total, 2) }}
                                                </h6>
                                                <small class="text-muted">
                                                    Qty: {{ $item->quantity }}
                                                </small>
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-md-3 text-end">

                                                <!-- View Bill -->
                                                <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-sm btn-outline-dark w-100 mb-2">
                                                    View Bill
                                                </a>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                                @endforeach

                            @empty

                                <!-- Empty State -->
                                <div class="col-12">
                                    <div class="card p-5 text-center">
                                        <div class="mb-3 text-muted">
                                            <i class="bi bi-bag-x fs-1"></i>
                                        </div>
                                        <h4>No Orders Yet</h4>
                                        <p class="text-muted">
                                            Start shopping to see your orders here.
                                        </p>
                                        <a href="{{ route('shop') }}" class="btn btn-dark mt-2">
                                            Start Shopping
                                        </a>
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
@endsection

@push('scripts')
<script>
    // Persist Navigation State
    document.addEventListener('DOMContentLoaded', function() {
        // Restore active tab from localStorage
        const lastTab = localStorage.getItem('dashboard-active-tab');
        if (lastTab) {
            const trigger = document.querySelector(`[data-bs-target='${lastTab}']`);
            if (trigger) trigger.click();
        }

        // Save tab on click
        document.querySelectorAll('.nav-pills .nav-link').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const target = btn.getAttribute('data-bs-target');
                if (target) localStorage.setItem('dashboard-active-tab', target);
            });
        });
    });

    // Profile Edit Logic
    function enableEdit() {
        document
            .querySelectorAll("#profileForm input, #profileForm select")
            .forEach((el) => {
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
        sel.setAttribute("disabled", true);
        sel.classList.remove("form-control");
        sel.classList.add("form-control-plaintext");

        document.getElementById("actionButtons").classList.add("d-none");
        document.getElementById("editBtn").classList.remove("d-none");
    }

    // Address Form Logic
    function showForm() {
        document.getElementById("addressList").classList.add("d-none");
        document.getElementById("addressForm").classList.remove("d-none");
        document.getElementById("addressForm").classList.add("fade-in");
    }

    function hideForm() {
        document.getElementById("addressForm").classList.add("d-none");
        document.getElementById("addressList").classList.remove("d-none");
    }
</script>
@endpush