@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Product Reviews</h3>
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
                    <div class="text-tiny">Product Reviews</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET" action="{{ route('admin.reviews') }}">
                        <fieldset class="name">
                            <input type="text" placeholder="Search reviews by customer, product, rating..." name="search"
                                value="{{ request('search') }}">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            @if(Session::has('status'))
                <p class="alert alert-success">{{ Session::get('status') }}</p>
            @endif

            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 30px">#</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Image</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $reviews->firstItem() + $loop->index }}</td>
                            <td>
                                <div>{{ $review->user->name ?? '—' }}</div>
                                <small class="text-muted">{{ $review->user->email ?? '' }}</small>
                            </td>
                            <td>{{ $review->product->name ?? '—' }}</td>
                            <td>{{ $review->rating }} / 5</td>
                            <td style="max-width: 280px;">{{ Str::limit($review->review, 120) }}</td>
                            <td>
                                @if($review->image)
                                    <img src="{{ asset('storage/' . $review->image) }}" alt="Review image" width="50" height="50" style="object-fit: cover;">
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $review->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="list-icon-function">
                                    <form action="{{ route('admin.review.delete', ['id' => $review->id]) }}" method="POST">
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
                            <td colspan="8" class="text-center">No reviews found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.delete').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Are you sure?",
                text: "You want to delete this review?",
                type: "warning",
                buttons: ["No", "Yes"],
                confirmButtonColor: '#dc3545'
            }).then(function (result) {
                if (result) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
