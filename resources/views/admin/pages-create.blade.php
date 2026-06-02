@extends('layouts.admin')

@section('content')

<style>
    form textarea{
        height: auto !important;
    }

    .tox-tinymce{
        border-radius: 10px !important;
    }

    .wg-box{
        padding: 30px;
    }

    .form-section-title{
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .field-group{
        margin-bottom: 25px;
    }

    .upload-image .item img{
        border-radius: 12px;
        height: 180px;
        object-fit: cover;
    }

    .text-danger{
        margin-top: 8px;
        font-size: 13px;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">

            <h3>Create Page</h3>

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
                    <a href="{{ route('admin.pages') }}">
                        <div class="text-tiny">Pages</div>
                    </a>
                </li>

                <li>
                    <i class="icon-chevron-right"></i>
                </li>

                <li>
                    <div class="text-tiny">Create Page</div>
                </li>

            </ul>

        </div>

        {{-- Form Box --}}
        <div class="wg-box">

            <form class="form-new-product form-style-1"
                  action="{{ route('admin.pages.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                {{-- Basic Information --}}
                <div class="form-section-title">
                    Basic Information
                </div>

                {{-- Page Title --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Page Title
                        <span class="tf-color-1">*</span>
                    </div>

                    <input type="text"
                           name="title"
                           placeholder="Enter page title"
                           value="{{ old('title') }}"
                           required>

                    @error('title')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Page Url
                        <span class="tf-color-1">*</span>
                    </div>

                    <input type="text" name="url" placeholder="Enter page url" value="{{ old('url') }}" required>

                    @error('url')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Page Heading
                        <span class="tf-color-1">*</span>
                    </div>

                    <input type="text" name="heading" placeholder="Enter page heading (h1)" value="{{ old('heading') }}">

                    @error('heading')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- Content --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Page Content
                    </div>

                    <textarea name="content"
                              class="editor">{{ old('content') }}</textarea>

                    @error('content')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- SEO Section --}}
                <div class="form-section-title mt-40">
                    SEO Meta Details
                </div>

                {{-- Meta Title --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Meta Title
                    </div>

                    <input type="text"
                           name="meta_title"
                           placeholder="Enter meta title"
                           value="{{ old('meta_title') }}">

                    @error('meta_title')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- Meta Description --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Meta Description
                    </div>

                    <textarea name="meta_description"
                              rows="4"
                              placeholder="Enter meta description">{{ old('meta_description') }}</textarea>

                    @error('meta_description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- Meta Keywords --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Meta Keywords
                    </div>

                    <textarea name="meta_keywords"
                              rows="3"
                              placeholder="keyword1, keyword2, keyword3">{{ old('meta_keywords') }}</textarea>

                    @error('meta_keywords')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- Canonical URL --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Canonical URL
                    </div>

                    <input type="text"
                           name="canonical_url"
                           placeholder="https://example.com/page-url"
                           value="{{ old('canonical_url') }}">

                    @error('canonical_url')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- Meta Image --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Meta Image
                    </div>

                    <div class="upload-image flex-grow">

                        <div class="item"
                             id="imgpreview"
                             style="display:none">

                            <img id="previewImg"
                                 src=""
                                 class="effect8">

                        </div>

                        <div id="upload-file"
                             class="item up-load">

                            <label class="uploadfile" for="myFile">

                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>

                                <span class="body-text">
                                    Drop your image here or
                                    <span class="tf-color">
                                        click to browse
                                    </span>
                                </span>

                                <input type="file"
                                       id="myFile"
                                       name="meta_image"
                                       accept="image/*">

                            </label>

                        </div>

                    </div>

                    @error('meta_image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- Status --}}
                <fieldset class="field-group">

                    <div class="body-title mb-10">
                        Status
                    </div>

                    <select name="status">

                        <option value="1"
                            {{ old('status',1) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0"
                            {{ old('status') == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </fieldset>

                {{-- Submit --}}
                <div class="bot">

                    <div></div>

                    <button class="tf-button w208"
                            type="submit">

                        Save Page

                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

@endsection


@push('scripts')

{{-- Image Preview --}}
<script>
    $(function () {

        $('#myFile').on('change', function () {

            const file = this.files[0];

            if (file) {

                $('#previewImg').attr(
                    'src',
                    URL.createObjectURL(file)
                );

                $('#imgpreview').show();
            }

        });

    });
</script>

{{-- TinyMCE --}}
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>

<script>
tinymce.init({

    selector: '.editor',

    height: 350,
    width : "100%",

    menubar: false,

    plugins: 'lists link image code table',

    toolbar:
        'undo redo | ' +
        'bold italic underline | ' +
        'bullist numlist | ' +
        'link image table | ' +
        'code',

    content_style: `
        body {
            font-family: sans-serif;
            font-size: 14px;
            padding: 10px;
        }
    `
});
</script>

@endpush