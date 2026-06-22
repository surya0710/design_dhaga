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
                                <img id="previewImg" src="{{ asset($section->image) }}" class="effect8" alt="{{ old('alt_tag', $section->alt_tag ?? 'About section image') }}">
                            </div>
                        @else

                            <div class="item" id="imgpreview" style="display:none"> 
                                <img id="previewImg" src="" class="effect8" alt="{{ old('alt_tag', $section->alt_tag ?? 'About section image') }}">
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

                <fieldset>
                    <div class="body-title">Image Alt Tag Content</div>
                    <input type="text" id="aboutAltTag" name="alt_tag" value="{{ old('alt_tag', $section->alt_tag ?? '') }}">
                </fieldset>

                <div class="body-title" style="margin-top: 25px;">Values Section</div>

                <div class="about-values-editor">
                    @foreach($valueItems as $index => $item)
                        @php
                            $valueAltText = old('value_alts.'.$index, $item['alt'] ?? '') ?: ($item['title'] ?? 'About value');
                        @endphp
                        <fieldset class="about-value-card" style="max-width: 100%;">
                            <div class="body-title">Value {{ $index + 1 }}</div>

                            <div class="value-icon-stack">
                                <div class="item value-icon-preview" style="{{ !empty($item['icon']) ? 'display:flex' : 'display:none' }}">
                                    <img src="{{ !empty($item['icon']) ? asset($item['icon']) : '' }}" class="effect8 value-preview-img" alt="{{ $valueAltText }}">
                                </div>

                                <div class="item up-load value-icon-upload">
                                    <label class="uploadfile" for="valueIcon{{ $index }}">
                                        <span class="icon">
                                            <i class="icon-upload-cloud"></i>
                                        </span>

                                        <span class="body-text">
                                            Drop icon or
                                            <span class="tf-color">
                                                click to browse
                                            </span>
                                        </span>

                                        <input type="file"
                                            id="valueIcon{{ $index }}"
                                            name="value_icons[{{ $index }}]"
                                            class="value-icon-input"
                                            accept="image/*,.svg">
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="existing_value_icons[{{ $index }}]" value="{{ $item['icon'] ?? '' }}">

                            <div class="value-field">
                                <div class="body-title">Title</div>
                                <input type="text" name="value_titles[{{ $index }}]" class="value-title-input" value="{{ old('value_titles.'.$index, $item['title'] ?? '') }}" required>
                            </div>

                            <div class="value-field">
                                <div class="body-title">Icon Alt Tag Content</div>
                                <input type="text" name="value_alts[{{ $index }}]" class="value-alt-input" value="{{ old('value_alts.'.$index, $item['alt'] ?? '') }}" placeholder="Defaults to the value title">
                            </div>

                            <div class="value-field">
                                <div class="body-title">Description</div>
                                <textarea name="value_descriptions[{{ $index }}]" rows="7" required>{{ old('value_descriptions.'.$index, $item['description'] ?? '') }}</textarea>
                            </div>
                        </fieldset>
                    @endforeach
                </div>

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

@push('styles')
<style>
    .about-values-editor {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .about-value-card {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 20px;
        min-width: 0;
    }

    .value-icon-stack {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 16px;
    }

    .value-icon-preview,
    .value-icon-upload .uploadfile {
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 150px;
    }

    .value-icon-upload {
        width: 100%;
        border: 1px dashed var(--Main);
        border-radius: 12px;
        min-height: 130px;
    }

    .value-icon-upload .uploadfile {
        display: flex;
        flex-direction: column;
        gap: 10px;
        height: 100%;
        cursor: pointer;
        text-align: center;
    }

    .value-icon-upload .uploadfile .icon {
        color: var(--Main);
        font-size: 40px;
        line-height: 1;
    }

    .value-icon-upload .uploadfile input {
        position: absolute;
        opacity: 0;
        visibility: hidden;
        width: 0;
        height: 0;
    }

    .value-icon-preview img {
        width: auto;
        max-width: 120px;
        max-height: 120px;
        object-fit: contain;
    }

    .value-field {
        margin-bottom: 15px;
    }

    .value-field:last-child {
        margin-bottom: 0;
    }

    .value-field textarea {
        min-height: 150px;
        resize: vertical;
    }

    @media (max-width: 1199px) {
        .about-values-editor {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .about-values-editor {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush


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
                $('#previewImg').attr('alt', $('#aboutAltTag').val() || 'About section image');

                $('#imgpreview').show();

            }

        });

        $('#aboutAltTag').on('input', function () {

            $('#previewImg').attr('alt', this.value || 'About section image');

        });

        $('.value-icon-input').on('change', function () {

            const file = this.files[0];
            const card = $(this).closest('fieldset');
            const preview = card.find('.value-icon-preview');
            const image = preview.find('.value-preview-img');

            if (file) {

                image.attr('src', URL.createObjectURL(file));
                image.attr('alt', card.find('.value-alt-input').val() || card.find('.value-title-input').val() || 'About value');
                preview.show();

            }

        });

        $('.value-alt-input, .value-title-input').on('input', function () {

            const card = $(this).closest('fieldset');
            const altText = card.find('.value-alt-input').val() || card.find('.value-title-input').val() || 'About value';

            card.find('.value-preview-img').attr('alt', altText);

        });

    });

</script>

@endpush
