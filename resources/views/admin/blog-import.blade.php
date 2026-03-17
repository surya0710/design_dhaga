@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Import Blogs</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('admin.blogs')}}">
                        <div class="text-tiny">Blogs</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Import Blogs</div>
                </li>
            </ul>
        </div>
        <!-- form-add-product -->
        @if(Session::has('status'))
            <p class="alert alert-success"> {{Session::get('status')}}</p>
        @endif
        <form class="form-new-product form-style-1" action="{{ route('import.blogs.add') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <fieldset class="file">
                    <div class="body-title">Upload CSV File <span class="tf-color-1">*</span>
                    </div>
                    <input type="file" class="flex-grow" value="" type="text" placeholder="Category name" name="file"
                        tabindex="0"  aria-required="true" required="">
                </fieldset>
                @error('file')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
        </form>
        <!-- /form-add-product -->
    </div>
    <!-- /main-content-wrap -->
</div>
@endsection
