@extends('layouts.admin')
@push('styles')
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />
@endpush
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Product</h3>
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
                    <div class="text-tiny">Edit blog</div>
                </li>
            </ul>
        </div>
        <!-- form-add-product -->
        <form class=" form-add-product" method="POST" enctype="multipart/form-data"
            action="{{route('admin.blog.update', $blog->id)}}">
            @csrf
            @method('PUT')
            @if(Session::has('status'))
            <p class="alert alert-success"> {{Session::get('status')}}</p>
            @endif
            @if(Session::has('error'))
            <p class="alert alert-danger"> {{Session::get('error')}}</p>
            @endif
            <!-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif -->

            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Blog title <span class="tf-color-1">*</span>
                    </div>
                    <input class="mb-10" type="text" placeholder="Enter blog title" name="title" tabindex="0"
                        value="{{$blog->title}}" aria-required="true" required="">
                    <div class="text-tiny">Do not exceed 100 characters when entering the
                        blog title.</div>
                    <!-- print error message -->
                    <div class="text-danger">
                        {{ $errors->first('title') }}
                    </div>
                </fieldset>

                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product slug" name="slug" tabindex="0"
                        value="{{$blog->slug}}" aria-required="true" required="">
                    <div class="text-danger">
                        {{ $errors->first('slug') }}
                    </div>
                </fieldset>
                <fieldset class="name">
                    <div class="body-title mb-10">Tags <span class="tf-color-1">*</span></div>
                    <input type="text" name="tags" class="form-control w-100" data-role="tagsinput"
                        value="{{ isset($blog) ? $blog->tags->pluck('name')->implode(',') : '' }}">
                    <div class="text-danger">
                        {{ $errors->first('tags') }}
                    </div>
                </fieldset>

                <fieldset class="description">
                    <div class="body-title mb-10">Content <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="content" placeholder="Content" id="editor1" tabindex="0"
                        aria-required="true" required="">{{ old('content', $blog->content ?? '') }}</textarea>
                    <div class="text-danger">
                        {{ $errors->first('content') }}
                    </div>
                </fieldset>
                <fieldset class="name">
                    <div class="body-title mb-10">Meta title <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product Meta title" name="meta_title"
                        tabindex="0" value="{{$blog->meta_title}}" aria-required="true">
                    <div class="text-danger">
                        {{ $errors->first('meta_title') }}
                    </div>
                </fieldset>
                <fieldset class="name">
                    <div class="body-title mb-10">Meta keywords <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product Meta keywords" name="meta_keywords"
                        tabindex="0" value="{{$blog->meta_keywords}}" aria-required="true">
                    <div class="text-danger">
                        {{ $errors->first('meta_keywords') }}
                    </div>
                </fieldset>
                <fieldset class="name">
                    <div class="body-title mb-10">Meta description <span class="tf-color-1">*</span></div>
                    <textarea class="mb-10" placeholder="Enter product Meta description" name="meta_description"
                        tabindex="0" aria-required="true">{{$blog->meta_description}}</textarea>
                    <div class="text-danger">
                        {{ $errors->first('meta_description') }}
                    </div>
                </fieldset>
                <fieldset>
                    <div class="body-title mb-10">Upload image <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview">
                            @if($blog->image)
                            @php
                            $images = explode(',', $blog->image);
                            $firstimg = $images[0];
                            @endphp
                            <img src="{{ asset('uploads/blogs/'.$firstimg) }}" class="effect8" alt="">
                            @endif
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span class="tf-color">click to
                                        browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>

                    </div>
                    <div class="text-danger">
                        {{ $errors->first('image') }}
                    </div>

                </fieldset>
                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Update Blog</button>
                </div>
            </div>
        </form>
        <!-- /form-add-product -->
    </div>
    <!-- /main-content-wrap -->
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $("#myFile").on('change', function(e) {
            const photoinp = $("#myFile");
            const [file] = this.files;
            if (file) {
                $("#imgpreview img").attr('src', URL.createObjectURL(file));
                $("#imgpreview").show();
            }
        });
        $("#gFile").on('change', function(e) {
            const photoinp = $("#gFile");
            const $gphotos = this.files;
            $.each($gphotos, function(index, file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $("#galUpload").append('<div class="item"><img src="' + e.target.result +
                        '" alt=""></div>');
                }
                reader.readAsDataURL(file);
            });
        });

        $("input[name='title']").on("change", function() {
            $("input[name='slug']").val(StringToSlug($(this).val()));

        })
    });

    function StringToSlug(Text) {
        return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>


<script>
    CKEDITOR.replace('editor1');
</script>

@endpush