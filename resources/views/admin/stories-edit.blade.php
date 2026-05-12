@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">

            <h3>Edit Story</h3>

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
                        <div class="text-tiny">Stories</div>
                    </a>
                </li>

                <li>
                    <i class="icon-chevron-right"></i>
                </li>

                <li>
                    <div class="text-tiny">Edit Story</div>
                </li>

            </ul>

        </div>

        <div class="wg-box">

            <form class="form-new-product form-style-1"
                action="{{ route('admin.story.update', $story->id) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- Year --}}
                <fieldset>

                    <div class="body-title">
                        Year <span class="tf-color-1">*</span>
                    </div>

                    <input type="number"
                        name="year"
                        value="{{ old('year', $story->year) }}"
                        required>

                    @error('year')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                {{-- Description --}}
                <fieldset>

                    <div class="body-title">
                        Description <span class="tf-color-1">*</span>
                    </div>

                    <textarea name="description"
                        rows="6"
                        required>{{ old('description', $story->description) }}</textarea>

                    @error('description')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                {{-- Image Upload --}}
                <fieldset>

                    <div class="body-title">
                        Image
                    </div>

                    <div class="upload-image flex-grow">

                        {{-- Existing Image --}}
                        @if($story->image)

                            <div class="item"
                                id="imgpreview"
                                style="display:block">

                                <img id="previewImg"
                                    src="{{ asset($story->image) }}"
                                    class="effect8">

                            </div>

                        @else

                            <div class="item"
                                id="imgpreview"
                                style="display:none">

                                <img id="previewImg"
                                    src=""
                                    class="effect8">

                            </div>

                        @endif

                        {{-- Upload Box --}}
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

                    @error('image')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                {{-- Display Order --}}
                <fieldset>

                    <div class="body-title">
                        Display Order
                    </div>

                    <input type="number"
                        name="display_order"
                        value="{{ old('display_order', $story->display_order) }}">

                    @error('display_order')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                {{-- Status --}}
                <fieldset>

                    <div class="body-title">
                        Status
                    </div>

                    <select name="status">

                        <option value="1"
                            {{ old('status', $story->status) == 1 ? 'selected' : '' }}>

                            Active

                        </option>

                        <option value="0"
                            {{ old('status', $story->status) == 0 ? 'selected' : '' }}>

                            Inactive

                        </option>

                    </select>

                    @error('status')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                <div class="bot">

                    <div></div>

                    <button class="tf-button w208"
                        type="submit">

                        Update Story

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