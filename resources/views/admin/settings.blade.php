@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Website Settings</h3>

            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>

                <li>
                    <i class="icon-chevron-right"></i>
                </li>

                <li>
                    <div class="text-tiny">Settings</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">

            @if(Session::has('success'))
            <div class="alert alert-success mb-3">
                {{ Session::get('success') }}
            </div>
            @endif

            <form class="form-new-product form-style-1"
                action="{{ route('admin.settings.update') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf

                {{-- Store Name --}}
                <fieldset class="name">
                    <div class="body-title">Store Name</div>

                    <input class="flex-grow"
                        type="text"
                        name="store_name"
                        value="{{ old('store_name', $settings->store_name ?? '') }}"
                        placeholder="Store Name">
                </fieldset>

                {{-- Regular Logo --}}
                <fieldset>
                    <div class="body-title">Regular Logo</div>

                    <div class="upload-image flex-grow">

                        @if(!empty($settings->logo))
                        <div class="item" id="logoPreview">
                            <img src="{{ asset('uploads/settings/'.$settings->logo) }}" class="effect8">
                        </div>
                        @else
                        <div class="item" id="logoPreview" style="display:none">
                            <img src="" class="effect8">
                        </div>
                        @endif

                        <div class="item up-load">
                            <label class="uploadfile" for="logo">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>

                                <span class="body-text">
                                    Upload Regular Logo
                                </span>

                                <input type="file" id="logo" name="logo" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>

                {{-- Dark Logo --}}
                <fieldset>
                    <div class="body-title">White Logo</div>

                    <div class="upload-image flex-grow">

                        @if(!empty($settings->dark_logo))
                        <div class="item bg-dark" id="darkLogoPreview">
                            <img src="{{ asset('uploads/settings/'.$settings->dark_logo) }}" class="effect8">
                        </div>
                        @else
                        <div class="item" id="darkLogoPreview" style="display:none">
                            <img src="" class="effect8">
                        </div>
                        @endif

                        <div class="item up-load">
                            <label class="uploadfile" for="dark_logo">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>

                                <span class="body-text">
                                    Upload Dark Logo
                                </span>

                                <input type="file"
                                    id="dark_logo"
                                    name="dark_logo"
                                    accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>

                {{-- Office Address --}}
                <fieldset class="name">
                    <div class="body-title">Office Address</div>

                    <textarea name="office_address"
                        class="flex-grow"
                        rows="4">{{ old('office_address', $settings->office_address ?? '') }}</textarea>
                </fieldset>

                {{-- Store Address --}}
                <fieldset class="name">
                    <div class="body-title">Store Address</div>

                    <textarea name="store_address"
                        class="flex-grow"
                        rows="4">{{ old('store_address', $settings->store_address ?? '') }}</textarea>
                </fieldset>

                {{-- Email --}}
                <fieldset class="name">
                    <div class="body-title">Support Email</div>

                    <input class="flex-grow"
                        type="email"
                        name="support_email"
                        value="{{ old('support_email', $settings->support_email ?? '') }}"
                        placeholder="Support Email">
                </fieldset>

                {{-- Contact Number --}}
                <fieldset class="name">
                    <div class="body-title">Contact Number</div>

                    <input class="flex-grow"
                        type="text"
                        name="contact_number"
                        value="{{ old('contact_number', $settings->contact_number ?? '') }}"
                        placeholder="Contact Number">
                </fieldset>

                {{-- Whatsapp --}}
                <fieldset class="name">
                    <div class="body-title">Whatsapp Number</div>

                    <input class="flex-grow"
                        type="text"
                        name="whatsapp_number"
                        value="{{ old('whatsapp_number', $settings->whatsapp_number ?? '') }}"
                        placeholder="Whatsapp Number">
                </fieldset>

                {{-- Timings --}}
                <fieldset class="name">
                    <div class="body-title">Working Days</div>

                    <input class="flex-grow"
                        type="text"
                        name="working_days"
                        value="{{ old('working_days', $settings->working_days ?? '') }}"
                        placeholder="Mon - Sat">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Opening Time</div>

                    <input class="flex-grow"
                        type="time"
                        name="opening_time"
                        value="{{ old('opening_time', $settings->opening_time ?? '') }}">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Closing Time</div>

                    <input class="flex-grow"
                        type="time"
                        name="closing_time"
                        value="{{ old('closing_time', $settings->closing_time ?? '') }}">
                </fieldset>

                {{-- Social Links --}}
                <fieldset class="name">
                    <div class="body-title">Facebook Link</div>

                    <input class="flex-grow"
                        type="text"
                        name="facebook"
                        value="{{ old('facebook', $settings->facebook ?? '') }}">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Instagram Link</div>

                    <input class="flex-grow"
                        type="text"
                        name="instagram"
                        value="{{ old('instagram', $settings->instagram ?? '') }}">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Twitter Link</div>

                    <input class="flex-grow"
                        type="text"
                        name="twitter"
                        value="{{ old('twitter', $settings->twitter ?? '') }}">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Pinterest Link</div>

                    <input class="flex-grow"
                        type="text"
                        name="linkedin"
                        value="{{ old('linkedin', $settings->linkedin ?? '') }}">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Youtube Link</div>

                    <input class="flex-grow"
                        type="text"
                        name="youtube"
                        value="{{ old('youtube', $settings->youtube ?? '') }}">
                </fieldset>

                {{-- Meta --}}
                <fieldset class="name">
                    <div class="body-title">Meta Title</div>

                    <input class="flex-grow"
                        type="text"
                        name="meta_title"
                        value="{{ old('meta_title', $settings->meta_title ?? '') }}">
                </fieldset>

                <fieldset class="name">
                    <div class="body-title">Meta Description</div>

                    <textarea name="meta_description"
                        rows="4"
                        class="flex-grow">{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
                </fieldset>

                <div class="bot">
                    <div></div>

                    <button class="tf-button w208" type="submit">
                        Update Settings
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

    $('#logo').on('change', function () {

        const [file] = this.files;

        if (file) {
            $('#logoPreview img').attr('src', URL.createObjectURL(file));
            $('#logoPreview').show();
        }
    });

    $('#dark_logo').on('change', function () {

        const [file] = this.files;

        if (file) {
            $('#darkLogoPreview img').attr('src', URL.createObjectURL(file));
            $('#darkLogoPreview').show();
        }
    });

});

</script>

@endpush