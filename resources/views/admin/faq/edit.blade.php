@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">

            <h3>Edit FAQ</h3>

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
                    <div class="text-tiny">Edit FAQ</div>
                </li>

            </ul>

        </div>

        {{-- Form Box --}}
        <div class="wg-box">

            <form class="form-new-product form-style-1"
                  action="{{ route('admin.faqs.update', $faq->id) }}"
                  method="POST">

                @csrf

                {{-- Page Slug --}}
                <fieldset class="name">

                    <div class="body-title">
                        Page Slug <span class="tf-color-1">*</span>
                    </div>

                    <input type="text"
                           name="page_slug"
                           placeholder="Enter page slug"
                           value="{{ old('page_slug', $faq->page_slug ?? 'shop') }}">

                    @error('page_slug')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                {{-- Question --}}
                <fieldset class="name mt-4">

                    <div class="body-title">
                        Question <span class="tf-color-1">*</span>
                    </div>

                    <input type="text"
                           name="question"
                           placeholder="Enter FAQ question"
                           value="{{ old('question', $faq->question) }}">

                    @error('question')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </fieldset>

                {{-- Answer --}}
                <fieldset class="description mt-4">

                    <div class="body-title">
                        Answer <span class="tf-color-1">*</span>
                    </div>

                    <textarea name="answer"
                              rows="6"
                              placeholder="Enter FAQ answer">{{ old('answer', $faq->answer) }}</textarea>

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
                               value="{{ old('sort_order', $faq->sort_order) }}">

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
                                {{ old('status', $faq->status) == '1' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('status', $faq->status) == '0' ? 'selected' : '' }}>
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

                        Update FAQ

                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

@endsection