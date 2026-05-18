@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Pages</h3>

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
                    <div class="text-tiny">Pages</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">

            <div class="flex items-center justify-between gap10 flex-wrap">

                {{-- Search --}}
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET" action="{{ route('admin.pages') }}">

                        <fieldset class="name">
                            <input type="text"
                                   placeholder="Search pages by title, slug, meta..."
                                   name="search"
                                   value="{{ request('search') }}">
                        </fieldset>

                        <div class="button-submit">
                            <button type="submit">
                                <i class="icon-search"></i>
                            </button>
                        </div>

                    </form>
                </div>

                {{-- Add Button --}}
                <a href="{{ route('admin.pages.create') }}"
                   class="tf-button style-1 w208">

                    + Add Page

                </a>

            </div>

            <div class="wg-table table-all-user">

                <table class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Meta Title</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($pages as $key => $page)

                        <tr>

                            <td>{{ $pages->firstItem() + $key }}</td>

                            {{-- Title --}}
                            <td>
                                {{ $page->title }}
                            </td>

                            {{-- Slug --}}
                            <td>
                                {{ $page->slug }}
                            </td>

                            {{-- Meta Title --}}
                            <td>
                                {{ $page->meta_title ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td>

                                @if($page->status)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            {{-- Created --}}
                            <td>
                                {{ $page->created_at->format('d M Y') }}
                            </td>

                            {{-- Actions --}}
                            <td>

                                <div class="list-icon-function">

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.pages.edit', $page->id) }}">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.pages.delete', $page->id) }}"
                                          method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" style="border:none;background:none;">

                                            <div class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
                                            </div>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="7" class="text-center">
                                No pages found
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="divider"></div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                {{ $pages->links('pagination::bootstrap-5') }}

            </div>

        </div>

    </div>
</div>
@endsection
