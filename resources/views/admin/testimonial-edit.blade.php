@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Testimonial</h3>

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
                    <div class="text-tiny">Edit Testimonial</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">

            <form class="form-new-product form-style-1" action="{{ route('admin.testimonial.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- Name --}}
                <fieldset class="name">
                    <div class="body-title">
                        Name <span class="tf-color-1">*</span>
                    </div>

                    <input
                        class="flex-grow"
                        type="text"
                        name="name"
                        value="{{ old('name', $testimonial->name) }}"
                        placeholder="Enter user name"
                        required>
                </fieldset>

                @error('name')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror


                {{-- Testimonial --}}
                <fieldset class="name">
                    <div class="body-title">
                        Testimonial <span class="tf-color-1">*</span>
                    </div>

                    <textarea
                        class="flex-grow"
                        name="testimonial"
                        rows="5"
                        placeholder="Enter testimonial"
                        required>{{ old('testimonial', $testimonial->testimonial) }}</textarea>
                </fieldset>

                @error('testimonial')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror


                {{-- Stars --}}
                <fieldset class="name">
                    <div class="body-title">
                        Stars <span class="tf-color-1">*</span>
                    </div>

                    <select class="flex-grow" name="stars" required>
                        <option value="">Select Rating</option>

                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}"
                                {{ old('stars', $testimonial->stars) == $i ? 'selected' : '' }}>
                                {{ $i }} Star
                            </option>
                        @endfor
                    </select>
                </fieldset>

                @error('stars')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror


                {{-- Image Upload --}}
                <fieldset>
                    <div class="body-title">
                        Image
                    </div>

                    <div class="upload-image flex-grow">

                        {{-- Existing Image --}}
                        @if($testimonial->image)
                            <div class="item" id="imgpreview" style="display:block">
                                <img id="previewImg"
                                    src="{{ asset($testimonial->image) }}"
                                    class="effect8">
                            </div>
                        @else
                            <div class="item" id="imgpreview" style="display:none">
                                <img id="previewImg" src="" class="effect8">
                            </div>
                        @endif

                        {{-- Upload Box --}}
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">

                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>

                                <span class="body-text">
                                    Drop your image or
                                    <span class="tf-color">click to browse</span>
                                </span>

                                <input
                                    type="file"
                                    id="myFile"
                                    name="image"
                                    accept="image/*">
                            </label>
                        </div>

                    </div>

                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>


                {{-- Status --}}
                <fieldset class="name">
                    <div class="body-title">
                        Status
                    </div>

                    <select class="flex-grow" name="status">
                        <option value="1"
                            {{ old('status', $testimonial->status) == '1' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0"
                            {{ old('status', $testimonial->status) == '0' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </fieldset>

                @error('status')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror


                <div class="bot">
                    <div></div>

                    <button class="tf-button w208" type="submit">
                        Update Testimonial
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

        $("#myFile").on('change', function () {

            const [file] = this.files;

            if (file) {

                $("#previewImg").attr('src', URL.createObjectURL(file));

                $("#imgpreview").show();
            }

        });

    });
</script>
@endpush