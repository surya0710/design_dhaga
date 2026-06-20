@extends('layouts.admin')

@section('content')
<style>
    form textarea{
        height: auto !important;
    }
    .cke {
        width: 100% !important;
    }

    .cke_inner {
        width: 100% !important;
    }

    .cke_contents {
        width: 100% !important;
    }
</style>

@php
    $isEdit = isset($slider);
@endphp

<div class="main-content-inner">
    <div class="main-content-wrap">
        
        <!-- Header -->
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>{{ $isEdit ? 'Edit Slider' : 'Add Slider' }}</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="#"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="#"><div class="text-tiny">Sliders</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">{{ $isEdit ? 'Edit Slider' : 'Add Slider' }}</div></li>
            </ul>
        </div>

        <!-- Form -->
        <div class="wg-box">
            <form class="form-new-product form-style-1"
                  method="POST"
                  action="{{ $isEdit ? route('admin.sliders.update', $slider->id) : route('admin.sliders.store') }}"
                  enctype="multipart/form-data">

                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                @error('error')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Image Upload -->
                <fieldset>
                    <div class="body-title">Slider Image <span class="tf-color-1">*</span></div>

                    <div class="upload-image flex-grow">

                        <!-- Existing Image -->
                        @if($isEdit && $slider->image)
                            <div class="item" id="imgpreview" style="display:block">
                                <img id="previewImg" src="{{ asset('storage/'.$slider->image) }}" class="effect8">
                            </div>
                        @else
                            <div class="item" id="imgpreview" style="display:none">
                                <img id="previewImg" src="">
                            </div>
                        @endif

                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon"><i class="icon-upload-cloud"></i></span>
                                <span class="body-text">
                                    Drop your image or 
                                    <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>

                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>

                <!-- Image Alt -->
                <fieldset>
                    <div class="body-title">Image Alt Tag Content</div>
                    <input type="text" name="image_alt"
                           placeholder="Describe this slider image for SEO and accessibility"
                           value="{{ old('image_alt', $slider->image_alt ?? '') }}">
                </fieldset>
                @error('image_alt')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                <!-- Heading -->
                <fieldset>
                    <div class="body-title">Heading <span class="tf-color-1">*</span></div>
                    <textarea name="heading" id="heading-editor" required>
                        {{ old('heading', $slider->heading ?? '') }}
                    </textarea>
                </fieldset>
                @error('heading')
                        <div class="text-danger">{{ $message }}</div>
                @enderror
                <!-- Description -->
                <fieldset>
                    <div class="body-title">Description</div>
                    <textarea name="description" id="description-editor">
                        {{ old('description', $slider->description ?? '') }}
                    </textarea>
                </fieldset>
                @error('description')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Text Location -->
                <fieldset>
                    <div class="body-title">Text Location</div>
                    <select name="text_location" required>
                        <option value="left" {{ old('text_location', $slider->text_location ?? '') == 'left' ? 'selected' : '' }}>Left</option>
                        <option value="center" {{ old('text_location', $slider->text_location ?? '') == 'center' ? 'selected' : '' }}>Center</option>
                        <option value="right" {{ old('text_location', $slider->text_location ?? '') == 'right' ? 'selected' : '' }}>Right</option>
                    </select>
                </fieldset>
                @error('text_location')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <fieldset>
                    <div class="body-title">Text Color</div>
                    <select name="text_color" required>
                        <option value="white" {{ old('text_color', $slider->text_color ?? '') == 'white' ? 'selected' : '' }}>White</option>
                        <option value="dark" {{ old('text_color', $slider->text_color ?? '') == 'dark' ? 'selected' : '' }}>Black</option>
                    </select>
                </fieldset>
                @error('text_color')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Button Text -->
                <fieldset>
                    <div class="body-title">Button Text</div>
                    <input type="text" name="button_text"
                           value="{{ old('button_text', $slider->button_text ?? '') }}">
                </fieldset>
                @error('button_text')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Button Link -->
                <fieldset>
                    <div class="body-title">Button Link</div>
                    <input type="text" name="button_link"
                           value="{{ old('button_link', $slider->button_link ?? '') }}">
                </fieldset>
                @error('button_link')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Target -->
                <fieldset>
                    <div class="body-title">Link Target</div>
                    <select name="target">
                        <option value="_self" {{ old('target', $slider->target ?? '') == '_self' ? 'selected' : '' }}>Same Tab</option>
                        <option value="_blank" {{ old('target', $slider->target ?? '') == '_blank' ? 'selected' : '' }}>New Tab</option>
                    </select>
                </fieldset>
                @error('target')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Order -->
                <fieldset>
                    <div class="body-title">Display Order</div>
                    <input type="number" name="order"
                           value="{{ old('order', $slider->order ?? 1) }}">
                </fieldset>
                @error('order')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Status -->
                <fieldset>
                    <div class="body-title">Status</div>
                    <select name="active_status">
                        <option value="1" {{ old('active_status', $slider->active_status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('active_status', $slider->active_status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </fieldset>
                @error('active_status')
                        <div class="text-danger">{{ $message }}</div>
                @enderror

                <!-- Submit -->
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">
                        {{ $isEdit ? 'Update Slider' : 'Save Slider' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(function () {

        $('#myFile').on('change', function () {

            const file = this.files[0];

            if (file) {
                $('#previewImg').attr('src', URL.createObjectURL(file));
                $('#imgpreview').show();
            }

        });

    });
</script>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<script>
    const commonConfig = {
        height: 120,
        enterMode: CKEDITOR.ENTER_BR,
        shiftEnterMode: CKEDITOR.ENTER_BR,
        toolbar: [
            ['Bold', 'Italic'],
            ['NumberedList', 'BulletedList'],
            ['Link'],
            ['Undo', 'Redo'],
            ['RemoveFormat'],
            ['Source']
        ],
        removeButtons: 'Image,Table,HorizontalRule,SpecialChar,Styles,Format,Font,FontSize',
    };

    // Heading Editor - Always H2
    CKEDITOR.replace('heading-editor', {
        ...commonConfig,
        height: 120,

        on: {
            instanceReady: function (ev) {
                const editor = ev.editor;

                editor.dataProcessor.writer.setRules('h2', {
                    indent: false,
                    breakBeforeOpen: false,
                    breakAfterOpen: false,
                    breakBeforeClose: false,
                    breakAfterClose: false
                });

                // Force content into h2
                editor.on('change', function () {
                    let data = editor.getData().trim();

                    // Remove existing h2 wrapper if already exists
                    data = data.replace(/^<h2>|<\/h2>$/g, '');

                    editor.removeListener('change');

                    editor.setData(`<h2>${data}</h2>`, function () {
                        editor.on('change', arguments.callee);
                    });
                });
            }
        }
    });

    // Description Editor - Always P
    CKEDITOR.replace('description-editor', {
        ...commonConfig,
        height: 150,

        on: {
            instanceReady: function (ev) {
                const editor = ev.editor;

                editor.dataProcessor.writer.setRules('p', {
                    indent: false,
                    breakBeforeOpen: false,
                    breakAfterOpen: false,
                    breakBeforeClose: false,
                    breakAfterClose: false
                });

                // Force content into p
                editor.on('change', function () {
                    let data = editor.getData().trim();

                    // Remove existing p wrapper if already exists
                    data = data.replace(/^<p>|<\/p>$/g, '');

                    editor.removeListener('change');

                    editor.setData(`<p>${data}</p>`, function () {
                        editor.on('change', arguments.callee);
                    });
                });
            }
        }
    });
</script>
@endpush