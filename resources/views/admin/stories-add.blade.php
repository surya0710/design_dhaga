@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">

            <h3>Add Story</h3>

        </div>

        <div class="wg-box">

            <form class="form-new-product form-style-1"
                action="{{ route('admin.story.store') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf

                {{-- Year --}}
                <fieldset>
                    <div class="body-title">
                        Year <span class="tf-color-1">*</span>
                    </div>

                    <input type="number"
                        name="year"
                        value="{{ old('year') }}"
                        required>
                </fieldset>

                {{-- Description --}}
                <fieldset>
                    <div class="body-title">
                        Description <span class="tf-color-1">*</span>
                    </div>

                    <textarea name="description"
                        rows="6"
                        required>{{ old('description') }}</textarea>
                </fieldset>

                {{-- Image --}}
                <fieldset>

                    <div class="body-title">
                        Image <span class="tf-color-1">*</span>
                    </div>

                    <div class="upload-image flex-grow">

                        <div class="item"
                            id="imgpreview"
                            style="display:none">

                            <img id="previewImg"
                                src=""
                                class="effect8">
                        </div>

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
                                    accept="image/*"
                                    required>

                            </label>

                        </div>

                    </div>

                </fieldset>

                {{-- Order --}}
                <fieldset>
                    <div class="body-title">
                        Display Order
                    </div>

                    <input type="number"
                        name="display_order"
                        value="{{ old('display_order', 1) }}">
                </fieldset>

                {{-- Status --}}
                <fieldset>

                    <div class="body-title">
                        Status
                    </div>

                    <select name="status">

                        <option value="1">
                            Active
                        </option>

                        <option value="0">
                            Inactive
                        </option>

                    </select>

                </fieldset>

                <div class="bot">

                    <div></div>

                    <button class="tf-button w208"
                        type="submit">

                        Save Story

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