@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Coupons</h3>
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
                    <div class="text-tiny">Coupons</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name"
                                tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{route('admin.coupon.add')}}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    @session('success')
                    <p class="alert alert-success">{{ session('success') }}</p>
                    @endsession
                    @if(Session::has('error'))
                    <p class="alert alert-danger">{{ Session::get('error') }}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Cart Value</th>
                                <th>Max discount</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Is Single Use</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupon as $coupons)
                            <tr>
                                <td>{{$loop->index + 1}}</td>
                                <td>{{$coupons-> code}}</td>
                                <td>{{$coupons-> type}}</td>
                                <td>{{$coupons-> value}}</td>
                                <td>{{$coupons->min_cart_value}}</td>
                                <td>{{$coupons->max_discount}}</td>
                                <td>{{ \Carbon\Carbon::parse($coupons->start_date)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($coupons->end_date)->format('d-m-Y') }}</td>
                                <td>{{$coupons->is_single_use}}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{route('admin.coupon.edit', $coupons->id)}}" class="item">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <a href="{{route('admin.coupon.delete', $coupons->id)}}">
                                            <div class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
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

            </div>
        </div>
    </div>
</div>
@endsection