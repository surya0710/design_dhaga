@extends('layouts.admin')

@section('content')
    <style>
        .table-transaction > tbody > tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
        }

        .ordered-product {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ordered-product img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .ordered-product .pname-text {
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            background: #f3f3f3;
        }
    </style>

    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Order Details</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="#">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Order Details</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Items</h5>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders->items as $item)
                                <tr>
                                    <td>
                                        <div class="ordered-product">
                                            <img
                                                src="{{ $item->product_image ? asset('storage/' . $item->product_image) : asset('backend/images/no-image.png') }}"
                                                alt="{{ $item->product_name }}"
                                            >
                                            <div class="pname-text">
                                                {{ $item->product_name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No items found for this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination"></div>
            </div>

            <div class="wg-box mt-5">
                <h5>Shipping Address</h5>
                <div class="my-account__address-item col-md-6">
                    <div class="my-account__address-item__detail">
                        <p><strong>Name:</strong> {{ $orders->name }}</p>
                        <p><strong>Email:</strong> {{ $orders->email }}</p>
                        <p><strong>Phone:</strong> {{ $orders->phone }}</p>
                        <p><strong>Address:</strong> {{ $orders->address_line_1 }}</p>

                        @if($orders->address_line_2)
                            <p><strong>Address Line 2:</strong> {{ $orders->address_line_2 }}</p>
                        @endif

                        @if($orders->landmark)
                            <p><strong>Landmark:</strong> {{ $orders->landmark }}</p>
                        @endif

                        <p><strong>City:</strong> {{ $orders->city }}</p>
                        <p><strong>State:</strong> {{ $orders->state }}</p>
                        <p><strong>Country:</strong> {{ $orders->country }}</p>
                        <p><strong>Pincode:</strong> {{ $orders->pincode }}</p>
                        <p><strong>Address Type:</strong> {{ ucfirst($orders->address_type) }}</p>
                    </div>
                </div>
            </div>

            <div class="wg-box mt-5">
                <h5>Transactions</h5>
                <table class="table table-striped table-bordered table-transaction">
                    <tbody>
                        <tr>
                            <th>Subtotal</th>
                            <td>₹{{ number_format($orders->subtotal, 2) }}</td>

                            <th>Shipping</th>
                            <td>{{$orders->delivery_type}} <br> ₹{{ number_format($orders->shipping, 2) }}</td>

                            <th>Discount</th>
                            <td>₹{{ number_format($orders->coupon_discount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td>₹{{ number_format($orders->total, 2) }}</td>

                            <th>Payment Mode</th>
                            <td>{{ ucfirst($orders->payment_method) }}</td>

                            <th>Payment Status</th>
                            <td>
                                <span class="status-badge">{{ $orders->payment_status }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Order Status</th>
                            <td>
                                <span class="status-badge">{{ $orders->order_status }}</span>
                            </td>

                            <th>Order Date</th>
                            <td>{{ $orders->created_at->toDayDateTimeString() }}</td>

                            <th>Paid Date</th>
                            <td>
                                @if ($orders->paid_at)
                                    {{ \Carbon\Carbon::parse($orders->paid_at)->toDayDateTimeString() }}
                                @else
                                    Not paid yet
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Delivered Date</th>
                            <td>
                                @if ($orders->delivered_at)
                                    {{ \Carbon\Carbon::parse($orders->delivered_at)->toDayDateTimeString() }}
                                @else
                                    Not delivered yet
                                @endif
                            </td>

                            <th>Cancelled Date</th>
                            <td>
                                @if ($orders->cancelled_at)
                                    {{ \Carbon\Carbon::parse($orders->cancelled_at)->toDayDateTimeString() }}
                                @else
                                    Not cancelled
                                @endif
                            </td>

                            <th>Razorpay Payment ID</th>
                            <td>{{ $orders->razorpay_payment_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Razorpay Order ID</th>
                            <td>{{ $orders->razorpay_order_id ?? 'N/A' }}</td>

                            <th>Coupon Code</th>
                            <td>{{ $orders->coupon_code ?? 'N/A' }}</td>

                            <th></th>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="wg-box mt-5">
                <h5>Update Order Status</h5>
                <div class="my-account__address-item">
                    <div class="my-account__address-item__detail">
                        <form method="POST" action="{{ route('orders.updateStatus', $orders->id) }}">
                            @csrf

                            <fieldset class="status">
                                <div class="body-title">Status</div>
                                <select name="order_status" id="order_status" class="flex-grow">
                                    <option value="pending" {{ $orders->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="packed" {{ $orders->order_status == 'packed' ? 'selected' : '' }}>Packed</option>
                                    <option value="shipped" {{ $orders->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $orders->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                            </fieldset>

                            <!-- Package Fields -->
                            <div id="package_fields" style="display:none; border:1px solid #eee; padding:15px; border-radius:8px; margin-top: 15px;">
                                <h6>Package Details</h6>

                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" name="length" placeholder="Length (cm)" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" name="breadth" placeholder="Breadth (cm)" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" name="height" placeholder="Height (cm)" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" name="weight" placeholder="Weight (kg)" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <button class="tf-button w208" type="submit">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const statusEl = document.getElementById('order_status');
        const packageBox = document.getElementById('package_fields');

        // Show on load (edit case)
        if (statusEl.value === 'packed') {
            packageBox.style.display = 'block';
        }

        statusEl.addEventListener('change', function () {
            packageBox.style.display = this.value === 'packed' ? 'block' : 'none';
        });

        document.querySelector('form').addEventListener('submit', function (e) {
            const status = statusEl.value;

            if (status === 'packed') {
                const fields = ['length', 'breadth', 'height', 'weight'];

                for (let field of fields) {
                    const val = document.querySelector(`[name="${field}"]`).value;
                    if (!val || val <= 0) {
                        e.preventDefault();
                        alert('Please fill all valid package details');
                        return;
                    }
                }

                if (!confirm('Create Shiprocket order with these package details?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endsection