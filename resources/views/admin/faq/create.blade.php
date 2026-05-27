@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">

            <h3>Add FAQ</h3>

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
                    <a href="{{ route('admin.faqs') }}">
                        <div class="text-tiny">FAQs</div>
                    </a>
                </li>

                <li>
                    <i class="icon-chevron-right"></i>
                </li>

                <li>
                    <div class="text-tiny">Add FAQ</div>
                </li>

            </ul>

        </div>

        {{-- Form Box --}}
        <div class="wg-box">

            <form class="form-new-product form-style-1"
                  action="{{ route('admin.faqs.store') }}"
                  method="POST">

                @csrf

                <fieldset class="name">
                    <div class="body-title">
                        Page Slug <span class="tf-color-1">*</span>
                    </div>

                    <input type="text" name="page_slug" placeholder="Enter page slug" value="{{ old('page_slug', 'shop') }}">

                    @error('page_slug')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </fieldset>

                <fieldset class="name mt-4">

                    <div class="body-title">
                        Question <span class="tf-color-1">*</span>
                    </div>

                    <input type="text"
                           name="question"
                           placeholder="Enter FAQ question"
                           value="{{ old('question') }}">

                    @error('question')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                <fieldset class="description mt-4">

                    <div class="body-title">
                        Answer <span class="tf-color-1">*</span>
                    </div>

                    <textarea name="answer" rows="6" class="editor" placeholder="Enter FAQ answer">{{ old('answer') }}</textarea>

                    @error('answer')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                <div class="cols gap22 mt-4">

                    {{-- Sort Order --}}
                    <fieldset class="name">

                        <div class="body-title">
                            Sort Order
                        </div>

                        <input type="number"
                               name="sort_order"
                               placeholder="Enter sort order"
                               value="{{ old('sort_order', 0) }}">

                        @error('sort_order')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </fieldset>

                    {{-- Status --}}
                    <fieldset class="name">

                        <div class="body-title">
                            Status
                        </div>

                        <select name="status">

                            <option value="1"
                                {{ old('status') == '1' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('status') == '0' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                        @error('status')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </fieldset>

                </div>

                {{-- Submit --}}
                <div class="bot mt-4">

                    <button class="tf-button w208"
                            type="submit">

                        Save FAQ

                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

@endsection

@push('scripts')
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