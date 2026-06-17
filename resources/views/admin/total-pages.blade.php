@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Total Pages</h3>

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
                    <div class="text-tiny">Total Pages</div>
                </li>
            </ul>
        </div>

        <div class="wg-box mb-20">
            <div class="flex gap20 flex-wrap">
                <div class="body-text">Total: <strong>{{ $totalPagesCount }}</strong></div>
                <div class="body-text">Pages: <strong>{{ $frontendPagesCount }}</strong></div>
                <div class="body-text">Products: <strong>{{ $productsCount }}</strong></div>
                <div class="body-text">Categories: <strong>{{ $categoriesCount }}</strong></div>
                <div class="body-text">Blogs: <strong>{{ $blogsCount }}</strong></div>
            </div>
        </div>

        <div class="wg-box">
            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Frontend URL</th>
                            <th>Status</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['title'] }}</td>
                                <td>
                                    <a href="{{ $item['url'] }}" target="_blank" rel="noopener">
                                        {{ $item['url'] }}
                                    </a>
                                </td>
                                <td>{{ $item['status'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No pages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
