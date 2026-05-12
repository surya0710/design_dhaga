@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center justify-between flex-wrap gap20 mb-27">

            <h3>About Section</h3>

        </div>

        <div class="wg-box">

            @if(Session::has('status'))

                <div class="alert alert-success">
                    {{ Session::get('status') }}
                </div>

            @endif

            <form class="form-new-product form-style-1" action="{{ route('admin.about.section.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Heading --}}
                <fieldset>
                    <div class="body-title">Heading</div>
                    <input type="text" name="heading" value="{{ old('heading', $section->heading ?? '') }}" required>
                </fieldset>

                {{-- Description --}}
                <fieldset>
                    <div class="body-title">Description</div>
                    <textarea name="description" id="description-editor" required>{{ old('description', $section->description ?? '') }}</textarea>
                </fieldset>
                {{-- Signature --}}
                <fieldset>
                    <div class="body-title">Signature</div>
                    <input type="text" name="signature" value="{{ old('signature', $section->signature ?? '') }}">
                </fieldset>

                {{-- Image --}}
                <fieldset>
                    <div class="body-title">Image</div>
                    <div class="upload-image flex-grow">

                        @if(isset($section) && $section->image)

                            <div class="item" id="imgpreview" style="display:block"> 
                                <img id="previewImg" src="{{ asset($section->image) }}" class="effect8">
                            </div>
                        @else

                            <div class="item" id="imgpreview" style="display:none"> 
                                <img id="previewImg" src="" class="effect8">
                            </div>

                        @endif

                        <div class="item up-load">

                            <label class="uploadfile" for="myFile">

                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>

                                <span class="body-text">
                                    Drop your image or
                                    <span class="tf-color">
                                        click to browse
                                    </span>
                                </span>

                                <input type="file"
                                    id="myFile"
                                    name="image"
                                    accept="image/*">

                            </label>

                        </div>

                    </div>

                </fieldset>

                <div class="bot">

                    <div></div>

                    <button class="tf-button w208"
                        type="submit">

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<script>

    CKEDITOR.replace('description-editor', {
        width: '100%',
        height: 250,
        allowedContent: true,
        extraAllowedContent: 'p(*) div(*) span(*) strong em;',
        toolbar: [
            ['Bold', 'Italic', 'Underline'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            ['NumberedList', 'BulletedList'],
            ['Link'],
            ['Undo', 'Redo'],
            ['RemoveFormat'],
            ['Source']
        ],

        removeButtons: 'Image,Table,HorizontalRule,SpecialChar,Styles,Format,Font,FontSize'

    });

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

@endpush