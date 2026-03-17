@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Category infomation</h3>
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
                        <div class="text-tiny">Categories</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Category</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('admin.category.store')}}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <fieldset class="parent_category">
                    <div class="body-title">Parent Category <span class="tf-color-1">*</span>
                    </div>
                    <select class="flex-grow" name="parent_id" tabindex="0" aria-required="true">
                        <option value="0">No Parent Category</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}" {{ old('parent_category_id') == $category->id ? 'selected' : '' }} >{{$category->name}}</option>
                        @endforeach
                    </select>
                </fieldset>
                @error('parent_category_id')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Category Name <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" value="{{old('name')}}" type="text" placeholder="Category name" name="name"
                        tabindex="0"  aria-required="true" required="">
                </fieldset>
                @error('name')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Category Slug <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" type="text" placeholder="Category Slug" name="slug"
                        tabindex="0" value="{{old('slug')}}" aria-required="true" required="">
                </fieldset>
                @error('slug')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Meta Title <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" type="text" placeholder="Category meta title" name="meta_title"
                        tabindex="0" value="{{old('meta_title')}}" aria-required="true" >
                </fieldset>
                @error('meta_title')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Meta Keywords <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow" type="text" placeholder="Category meta keywords" name="meta_keywords"
                        tabindex="0" value="{{old('meta_keywords')}}" aria-required="true" >
                </fieldset>
                @error('meta_keywords')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                <fieldset class="name">
                    <div class="body-title">Meta description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="flex-grow" type="text" placeholder="Category meta_description" name="meta_description"
                        tabindex="0" aria-required="true">{{old('meta_description')}}</textarea>
                </fieldset>
                @error('meta_description')
                    <span class="invalid-feedback">{{$message}}</span>
                @enderror
                
                <fieldset>
                    <div class="body-title">Upload images </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="upload-1.html" class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click
                                        to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')
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