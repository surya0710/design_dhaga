@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Testimonial infomation</h3>
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
                        <div class="text-tiny">Testimonials</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit Testimonial</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.testimonial.update')}}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{$testimonial->id}}">
                <fieldset class="name">
                    <div class="body-title">Name <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" value="{{$testimonial->name}}" type="text" placeholder="User name" name="name"
                        tabindex="0"  aria-required="true" required="">
                </fieldset>
                @error('name')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Testimonial <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="flex-grow" rows="5" placeholder="Enter Testimonial" name="testimonial" tabindex="0" aria-required="true" required="">{{$testimonial->testimonial}}</textarea>
                </fieldset>
                @error('testimonial')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Product Id <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" value="{{$testimonial->product_id}}" type="number" placeholder="Product id" name="productid"
                        tabindex="0"  aria-required="true" required="">
                </fieldset>
                @error('productid')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    
@endpush