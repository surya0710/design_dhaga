@extends('layouts.admin')

@section('content')
@php
    $isEdit = isset($gallery) && $gallery;
    $selectedCategory = old('portfolio_category_id', $gallery->portfolio_category_id ?? '');
    $selectedSubcategory = old('portfolio_subcategory_id', $gallery->portfolio_subcategory_id ?? '');
@endphp

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>{{ $isEdit ? 'Edit Portfolio Image' : 'Upload Portfolio Image' }}</h3>
            <a class="tf-button style-1 w208" href="{{ route('admin.portfolio.gallery.index') }}">
                <i class="icon-list"></i>Gallery
            </a>
        </div>

        <div class="wg-box">
            <form class="form-new-product form-style-1"
                action="{{ $isEdit ? route('admin.portfolio.gallery.update', $gallery) : route('admin.portfolio.gallery.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <fieldset>
                    <div class="body-title">Category <span class="tf-color-1">*</span></div>
                    <select class="flex-grow" id="portfolio_category_id" name="portfolio_category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </fieldset>
                @error('portfolio_category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset>
                    <div class="body-title">Subcategory</div>
                    <select class="flex-grow" id="portfolio_subcategory_id" name="portfolio_subcategory_id">
                        <option value="">No Subcategory</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" data-category="{{ $subcategory->portfolio_category_id }}" @selected($selectedSubcategory == $subcategory->id)>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </fieldset>
                @error('portfolio_subcategory_id') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title">Title</div>
                    <input class="flex-grow" type="text" name="title" value="{{ old('title', $gallery->title ?? '') }}" placeholder="Portfolio title">
                </fieldset>
                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title">Alt Text</div>
                    <input class="flex-grow" type="text" name="alt_text" value="{{ old('alt_text', $gallery->alt_text ?? '') }}" placeholder="Image description for SEO">
                </fieldset>
                @error('alt_text') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset>
                    <div class="body-title">Sort Order</div>
                    <input class="flex-grow" type="number" min="0" name="sort_order" value="{{ old('sort_order', $gallery->sort_order ?? 0) }}">
                </fieldset>
                @error('sort_order') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset>
                    <div class="body-title">Status</div>
                    <select class="flex-grow" name="status">
                        <option value="1" @selected(old('status', $gallery->status ?? 1) == 1)>Active</option>
                        <option value="0" @selected(old('status', $gallery->status ?? 1) == 0)>Inactive</option>
                    </select>
                </fieldset>

                <fieldset>
                    <div class="body-title">Upload Image @if(! $isEdit)<span class="tf-color-1">*</span>@endif</div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="{{ $isEdit ? '' : 'display:none' }}">
                            <img src="{{ $isEdit ? asset($gallery->image) : '' }}" class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="portfolioImage">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your image here or select <span class="tf-color">click to browse</span></span>
                                <input type="file" id="portfolioImage" name="image" accept="image/*" {{ ! $isEdit ? 'required' : '' }}>
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">{{ $isEdit ? 'Update Image' : 'Upload Image' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterPortfolioSubcategories() {
        const categoryId = $('#portfolio_category_id').val();
        const subcategory = $('#portfolio_subcategory_id');

        subcategory.find('option').each(function () {
            const optionCategory = $(this).data('category');
            const isPlaceholder = !$(this).val();
            $(this).toggle(isPlaceholder || String(optionCategory) === String(categoryId));
        });

        const selectedOption = subcategory.find('option:selected');
        if (selectedOption.val() && String(selectedOption.data('category')) !== String(categoryId)) {
            subcategory.val('');
        }
    }

    $('#portfolio_category_id').on('change', filterPortfolioSubcategories);
    filterPortfolioSubcategories();

    $('#portfolioImage').on('change', function () {
        const [file] = this.files;
        if (file) {
            $('#imgpreview img').attr('src', URL.createObjectURL(file));
            $('#imgpreview').show();
        }
    });
</script>
@endpush
