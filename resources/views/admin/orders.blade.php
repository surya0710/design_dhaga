@extends('layouts.admin')
@section('content')
<style>
    .table-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        display: block;
        white-space: nowrap;
    }

    .table-scroll table {
        min-width: 1400px;
    }

    .orders-filters {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        width: 100%;
        margin-bottom: 8px;
    }

    .orders-filters .filter-field {
        min-width: 160px;
    }

    .orders-filters .filter-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 6px;
    }

    .orders-filters .filter-field select,
    .orders-filters .filter-field input {
        width: 100%;
        min-height: 40px;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
    }

    .orders-filters .filter-search {
        flex: 1;
        min-width: 220px;
    }

    .orders-filters .filter-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Orders</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Orders</div>
                </li>
            </ul>
        </div>
        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap" style="margin-bottom:16px;">
                <form class="orders-filters" method="GET" action="{{ route('admin.orders') }}">
                    <div class="filter-field">
                        <label>Order status</label>
                        <select name="order_status">
                            <option value="confirmed" {{ ($orderStatus ?? 'confirmed') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="pending" {{ ($orderStatus ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="packed" {{ ($orderStatus ?? '') === 'packed' ? 'selected' : '' }}>Packed</option>
                            <option value="shipped" {{ ($orderStatus ?? '') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ ($orderStatus ?? '') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ ($orderStatus ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="all" {{ ($orderStatus ?? '') === 'all' ? 'selected' : '' }}>All statuses</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label>Payment status</label>
                        <select name="payment_status">
                            <option value="" {{ ($paymentStatus ?? '') === '' ? 'selected' : '' }}>All</option>
                            <option value="paid" {{ ($paymentStatus ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ ($paymentStatus ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label>Payment method</label>
                        <select name="payment_method">
                            <option value="" {{ ($paymentMethod ?? '') === '' ? 'selected' : '' }}>All</option>
                            <option value="razorpay" {{ ($paymentMethod ?? '') === 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                            <option value="offline" {{ ($paymentMethod ?? '') === 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="cod" {{ ($paymentMethod ?? '') === 'cod' ? 'selected' : '' }}>COD</option>
                            <option value="bank_transfer" {{ ($paymentMethod ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank transfer</option>
                        </select>
                    </div>

                    <div class="filter-field filter-search">
                        <label>Search</label>
                        <input type="text" name="search" placeholder="Name, phone, email, order id..." value="{{ request('search') }}">
                    </div>

                    <div class="filter-actions">
                        <button class="tf-button style-1" type="submit">Filter</button>
                        <a class="tf-button style-2" href="{{ route('admin.orders') }}">Reset</a>
                    </div>
                </form>

                <a class="tf-button style-1 w208" href="{{ route('admin.order.add') }}">Create Order</a>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-scroll">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width:70px">OrderNo</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Payment</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Total Items</th>
                                <th class="text-center">Delivered On</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($orders as $index => $order)
                            <tr>
                                <td class="text-center">{{ $orders->firstItem() + $index }}</td>
                                <td class="text-center">{{$order->name}}</td>
                                <td class="text-center">{{$order->phone}}</td>
                                <td class="text-center">{{$order->total}}</td>
                                <td class="text-center">{{ $order->payment_status }} / {{ $order->payment_method }}</td>
                                <td class="text-center">{{$order->order_status}}</td>
                                <td class="text-center">{{$order->created_at->toDayDateTimeString()}}</td>
                                <td class="text-center">{{$order->items()->count()}}</td>

                                <td>
                                    @if ($order->delivered_at)
                                        {{ \Carbon\Carbon::parse($order->delivered_at)->toDayDateTimeString() }}
                                    @else
                                        Not delivered yet
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="list-icon-function view-icon" style="justify-content:center;gap:8px;">
                                        <a href="{{ route('admin.order.detail', $order->id) }}" title="View order">
                                            <div class="item eye">
                                                <i class="icon-eye"></i>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.order.invoice', $order->id) }}" target="_blank" title="View invoice">
                                            <div class="item">
                                                <i class="icon-file-text"></i>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.order.invoice.download', $order->id) }}" title="Download invoice">
                                            <div class="item">
                                                <i class="icon-download"></i>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No orders found for the selected filters.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
