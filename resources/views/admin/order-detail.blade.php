@extends('layouts.admin')
@section('content')
    <style>
        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;
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
                        <div class="text-tiny">Order Items</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Items</h5>
                    </div>
                    <a class="tf-button style-1 w208" href="{{route('admin.orders')}}">Back</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders->items as $item)
                            @php
                                $images = explode(',',$item->product->images);
                                $imageone = $images[0];
                            @endphp
                            <tr>
                                <td class="pname">
                                    <div class="image">
                                        <img src="{{asset('uploads/products/thumbnails')}}/{{$imageone}}" alt="" class="image">
                                    </div>
                                    <div class="name">
                                        <a href="#" target="_blank" class="body-title-2">{{$item->product->name}}</a>
                                        <div class="text-tiny text-secondary">
                                            <span class="text-uppercase">{{$item->certificate_name}}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{$item->price}}</td>
                                <td class="text-center">{{$item->quantity}}</td>
                                <td class="text-center">{{$item->product_sku}}</td>
                                <td class="text-center">{{$item->product_category}}</td>
                                <td class="text-center">
                                    <div class="list-icon-function view-icon">
                                        <div class="item eye">
                                            <i class="icon-eye"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                </div>
            </div>

            <div class="wg-box mt-5">
                <h5>Shipping Address</h5>
                <div class="my-account__address-item col-md-6">
                    <div class="my-account__address-item__detail">
                        <p>{{$orders->name}}</p>
                        <p>{{$orders->street}}</p>
                        <p>{{$orders->city}},{{$orders->state}},{{$orders->country}}</p>
                        <p>{{$orders->pincode}}</p>
                        <p>Landmark : {{$orders->landmark}}</p>
                        <br>
                        <p>Mobile : {{$orders->mobile}}</p>
                        <br>
                        <p>Note : {{$orders->notes}}</p>
                    </div>
                </div>
            </div>

            <div class="wg-box mt-5">
                <h5>Transactions</h5>
                <table class="table table-striped table-bordered table-transaction">
                    <tbody>
                        <tr>
                            <th>Subtotal</th>
                            <td></td>
                            <th>Tax</th>
                            <td></td>
                            <th>Discount</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td>{{$orders->total}}</td>
                            <th>Payment Mode</th>
                            <td>{{$orders->payment_method}}</td>
                            <th>Status</th>
                            <td>{{$orders->status}}</td>
                        </tr>
                        <tr>
                            <th>Order Date</th>
                            <td>{{$orders->created_at->toDayDateTimeString()}}</td>
                            <th>Delivered Date</th>

                            <td>@if ($orders->delivered_at)
                                    {{ \Carbon\Carbon::parse($orders->delivered_at)->toDayDateTimeString() }}
                                @else
                                    Not delivered yet
                                @endif
                            </td>
                            
                            <th>Canceled Date</th>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="wg-box mt-5">
                <h5>Update Order Status</h5>
                <div class="my-account__address-item">
                    <div class="my-account__address-item__detail">
                        <form action="{{ route('orders.updateStatus', $orders->id) }}" method="POST">
                            @csrf
                            <label for="status" class="form-label">Select Order Status:</label>
                            <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                                <select name="status" >
                                    <option value="pending" {{ $orders->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $orders->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $orders->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $orders->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $orders->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <!-- <option value="returned" {{ $orders->status == 'returned' ? 'selected' : '' }}>Returned</option> -->
                                    <!-- <option value="refunded" {{ $orders->status == 'refunded' ? 'selected' : '' }}>Refunded</option> -->
                                    <option value="failed" {{ $orders->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="on_hold" {{ $orders->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="completed" {{ $orders->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection