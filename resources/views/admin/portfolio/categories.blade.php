@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Portfolio Categories</h3>
            <a class="tf-button style-1 w208" href="{{ route('admin.portfolio.gallery.index') }}">
                <i class="icon-image"></i>Gallery
            </a>
        </div>

        <div class="wg-box mb-20">
            @if(Session::has('status'))
                <p class="alert alert-success">{{ Session::get('status') }}</p>
            @endif

            <form class="form-new-product form-style-1" action="{{ route('admin.portfolio.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset class="name">
                    <div class="body-title">Category Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow slug-source" type="text" name="name" value="{{ old('name') }}" placeholder="Example: Wedding Collections" required>
                </fieldset>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title">Slug</div>
                    <input class="flex-grow slug-target" type="text" name="slug" value="{{ old('slug') }}" placeholder="Auto generated if empty">
                </fieldset>
                @error('slug') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset>
                    <div class="body-title">Category Image</div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="categoryImgPreview" style="display:none">
                            <img src="" class="effect8" alt="">
                        </div>
                        <div class="item up-load">
                            <label class="uploadfile" for="categoryImage">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your image here or select <span class="tf-color">click to browse</span></span>
                                <input type="file" id="categoryImage" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset>
                    <div class="body-title">Sort Order</div>
                    <input class="flex-grow" type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}">
                </fieldset>

                <fieldset>
                    <div class="body-title">Status</div>
                    <select class="flex-grow" name="status">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </fieldset>

                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Add Category</button>
                </div>
            </form>
        </div>

        <div class="wg-box">
            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($category->image)
                                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" style="width:70px;height:58px;object-fit:cover;border-radius:6px;margin-bottom:8px;">
                                    @else
                                        <span>-</span>
                                    @endif
                                    <input form="category-update-{{ $category->id }}" type="file" name="image" accept="image/*" style="max-width:160px;">
                                </td>
                                <td>
                                    <form id="category-update-{{ $category->id }}" action="{{ route('admin.portfolio.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                    <input form="category-update-{{ $category->id }}" type="text" name="name" value="{{ $category->name }}" required>
                                </td>
                                    <td><input form="category-update-{{ $category->id }}" type="text" name="slug" value="{{ $category->slug }}" required></td>
                                    <td><input form="category-update-{{ $category->id }}" type="number" min="0" name="sort_order" value="{{ $category->sort_order }}" style="max-width:90px"></td>
                                    <td>
                                        <select form="category-update-{{ $category->id }}" name="status">
                                            <option value="1" @selected($category->status)>Active</option>
                                            <option value="0" @selected(! $category->status)>Inactive</option>
                                        </select>
                                    </td>
                                    <td>{{ $category->subcategories_count }} subcategories / {{ $category->galleries_count }} images</td>
                                    <td>
                                        <div class="list-icon-function">
                                            <button form="category-update-{{ $category->id }}" class="item edit" type="submit" title="Save">
                                                <i class="icon-check"></i>
                                            </button>
                                            <form action="{{ route('admin.portfolio.categories.destroy', $category) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="item text-danger delete" type="submit" title="Delete">
                                                    <i class="icon-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No portfolio categories added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function portfolioSlug(value) {
        return value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
    }

    $('.slug-source').on('change', function () {
        const target = $(this).closest('form').find('.slug-target');
        if (!target.val()) {
            target.val(portfolioSlug($(this).val()));
        }
    });

    $('#categoryImage').on('change', function () {
        const [file] = this.files;
        if (file) {
            $('#categoryImgPreview img').attr('src', URL.createObjectURL(file));
            $('#categoryImgPreview').show();
        }
    });

    $('.delete').on('click', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        swal({
            title: 'Are you sure?',
            text: 'This will remove this portfolio category and its gallery items.',
            type: 'warning',
            buttons: ['No', 'Yes'],
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (result) {
                form.submit();
            }
        });
    });
</script>
@endpush
