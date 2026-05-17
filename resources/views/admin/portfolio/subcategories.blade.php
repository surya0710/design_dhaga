@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Portfolio Subcategories</h3>
            <a class="tf-button style-1 w208" href="{{ route('admin.portfolio.categories.index') }}">
                <i class="icon-layers"></i>Categories
            </a>
        </div>

        <div class="wg-box mb-20">
            @if(Session::has('status'))
                <p class="alert alert-success">{{ Session::get('status') }}</p>
            @endif

            <form class="form-new-product form-style-1" action="{{ route('admin.portfolio.subcategories.store') }}" method="POST">
                @csrf
                <fieldset>
                    <div class="body-title">Category <span class="tf-color-1">*</span></div>
                    <select class="flex-grow" name="portfolio_category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('portfolio_category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </fieldset>
                @error('portfolio_category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title">Subcategory Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow slug-source" type="text" name="name" value="{{ old('name') }}" placeholder="Example: Bridal Lehengas" required>
                </fieldset>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror

                <fieldset class="name">
                    <div class="body-title">Slug</div>
                    <input class="flex-grow slug-target" type="text" name="slug" value="{{ old('slug') }}" placeholder="Auto generated if empty">
                </fieldset>
                @error('slug') <span class="invalid-feedback">{{ $message }}</span> @enderror

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
                    <button class="tf-button w208" type="submit">Add Subcategory</button>
                </div>
            </form>
        </div>

        <div class="wg-box">
            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcategories as $subcategory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <form id="subcategory-update-{{ $subcategory->id }}" action="{{ route('admin.portfolio.subcategories.update', $subcategory) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                        <select form="subcategory-update-{{ $subcategory->id }}" name="portfolio_category_id" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" @selected($subcategory->portfolio_category_id == $category->id)>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input form="subcategory-update-{{ $subcategory->id }}" type="text" name="name" value="{{ $subcategory->name }}" required></td>
                                    <td><input form="subcategory-update-{{ $subcategory->id }}" type="text" name="slug" value="{{ $subcategory->slug }}" required></td>
                                    <td><input form="subcategory-update-{{ $subcategory->id }}" type="number" min="0" name="sort_order" value="{{ $subcategory->sort_order }}" style="max-width:90px"></td>
                                    <td>
                                        <select form="subcategory-update-{{ $subcategory->id }}" name="status">
                                            <option value="1" @selected($subcategory->status)>Active</option>
                                            <option value="0" @selected(! $subcategory->status)>Inactive</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="list-icon-function">
                                            <button form="subcategory-update-{{ $subcategory->id }}" class="item edit" type="submit" title="Save">
                                                <i class="icon-check"></i>
                                            </button>
                                            <form action="{{ route('admin.portfolio.subcategories.destroy', $subcategory) }}" method="POST">
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
                                <td colspan="7" class="text-center">No portfolio subcategories added yet.</td>
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

    $('.delete').on('click', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        swal({
            title: 'Are you sure?',
            text: 'This will remove this portfolio subcategory from related gallery items.',
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
