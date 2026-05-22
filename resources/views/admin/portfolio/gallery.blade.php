@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Portfolio Gallery</h3>
            <div class="flex items-center gap10">
                <a class="tf-button style-1" href="{{ route('admin.portfolio.categories.index') }}">
                    <i class="icon-layers"></i>Categories
                </a>
                <a class="tf-button style-1 w208" href="{{ route('admin.portfolio.gallery.create') }}">
                    <i class="icon-plus"></i>Add Image
                </a>
            </div>
        </div>

        <div class="wg-box">
            @if(Session::has('status'))
                <p class="alert alert-success">{{ Session::get('status') }}</p>
            @endif

            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Subcategory</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($galleries as $gallery)
                            <tr>
                                <td>{{ $galleries->firstItem() + $loop->index }}</td>
                                <td>
                                    <img src="{{ asset($gallery->image) }}" alt="{{ $gallery->alt_text ?: $gallery->title }}" style="width:90px;height:70px;object-fit:cover;border-radius:6px;">
                                </td>
                                <td>{{ $gallery->title ?: '-' }}</td>
                                <td>{{ $gallery->category->name ?? '-' }}</td>
                                <td>{{ $gallery->subcategory->name ?? '-' }}</td>
                                <td>
                                    @if($gallery->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{ route('admin.portfolio.gallery.edit', $gallery) }}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <form action="{{ route('admin.portfolio.gallery.destroy', $gallery) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No portfolio images uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $galleries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('.delete').on('click', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        swal({
            title: 'Are you sure?',
            text: 'This portfolio image will be deleted.',
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
