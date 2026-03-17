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
                    <div class="text-tiny">New Testimonial</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.testimonial.store')}}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Name <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" value="{{old('name')}}" type="text" placeholder="User name" name="name"
                        tabindex="0"  aria-required="true" required="">
                </fieldset>
                @error('name')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Testimonial <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="flex-grow" rows="5" placeholder="Enter Testimonial" name="testimonial" tabindex="0" aria-required="true" required="">{{old('testimonial')}}</textarea>
                </fieldset>
                @error('testimonial')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Product Id <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" value="{{old('productid')}}" type="number" placeholder="Product id" name="productid"
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
    <script>
        $(function(){
            $("#myFile").on('change',function(e){
                const photoinp = $("#myFile");
                const [file] = this.files;
                if(file){
                    $("#imgpreview img").attr('src', URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });

            $("input[name='name']").on("change", function(){
                $("input[name='slug']").val(StringToSlug($(this).val()));

            })
        });

        function StringToSlug(Text){
            return Text.toLowerCase()
            .replace(/[^\w ]+/g,"")
            .replace(/ +/g,"-");    
        }
    </script>
@endpush