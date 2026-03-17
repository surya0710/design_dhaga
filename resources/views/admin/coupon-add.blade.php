@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Coupon infomation</h3>
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
                    <a href="#">
                        <div class="text-tiny">Coupons</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Coupon</div>
                </li>
            </ul>
        </div>
        <div class="wg-box">
            <form class="form-new-product form-style-1" method="POST" action="{{route('admin.coupon.store')}}">
                @csrf
                @error('error')
                    <div class="text-danger">{{ $message }}</div>
                    
                @enderror
                <fieldset class="name">
                    <div class="body-title">Coupon Code <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Coupon Code" name="code"
                        tabindex="0" value="{{old('code')}}" aria-required="true" required="">
                    @error('code')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
                <fieldset class="category">
                    <div class="body-title">Coupon Type</div>
                    <div class="select flex-grow">
                        <select class="" name="type">
                            <option disabled selected value="">Select</option>
                            <option {{(old('type') == 'fixed' ? 'selected' : '')}} value="fixed">Fixed</option>
                            <option {{(old('type') == 'percent' ? 'selected' : '')}} value="percent">Percent</option>
                        </select>
                    </div>
                    @error('type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
                <fieldset class="category">
                    <div class="body-title">Is Single Use</div>
                    <div class="select flex-grow">
                        <select class="" name="is_single_use">
                            <option disabled selected value="">Select</option>
                            <option value="1" {{ old('is_single_use') == '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('is_single_use') == '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    @error('is_single_use')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Value <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Coupon Value" name="value"
                        tabindex="0" value="{{old('value')}}" aria-required="true" required="">
                    @error('value')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Min Cart Value <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Cart Value"
                        name="cart_value" tabindex="0" value="{{old('cart_value')}}" aria-required="true"
                        required="">
                    @error('cart_value')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Max discount <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Max Discount"
                        name="max_discount" tabindex="0" value="{{old('max_discount')}}" aria-required="true"
                        required="">
                    @error('max_discount')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">Start Date <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="date" placeholder="Start Date"
                        name="start_date" tabindex="0" value="{{ old('start_date') }}" aria-required="true"
                        required="">
                    @error('start_date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
                <fieldset class="name">
                    <div class="body-title">End Date <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="date" placeholder="End Date"
                        name="end_date" tabindex="0" value="{{ old('end_date') }}" aria-required="true"
                        required="">
                    @error('end_date')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection