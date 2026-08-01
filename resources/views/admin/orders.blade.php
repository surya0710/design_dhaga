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
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Orders</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="index.html">
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
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET" action="{{ route('admin.orders') }}">
                        <fieldset class="name">
                            <input type="text" placeholder="Search orders by name, phone, status..." class="" name="search"
                                tabindex="2" value="{{ request('search') }}">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
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
                                <th class="text-center">Status</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Total Items</th>
                                <th class="text-center">Delivered On</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1; @endphp

                            @foreach ($orders as $order)
                            <tr>
                                <td class="text-center">{{$i++}}</td>
                                <td class="text-center">{{$order->name}}</td>
                                <td class="text-center">{{$order->phone}}</td>
                                <td class="text-center">{{$order->total}}</td>
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
                            @endforeach
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
