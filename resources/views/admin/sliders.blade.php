@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Sliders</h3>
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
                    <div class="text-tiny">Sliders</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">

                {{-- Search --}}
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET">
                        <fieldset class="name">
                            <input type="text" placeholder="Search by heading..." name="search"
                                value="{{ request('search') }}">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>

                {{-- Add Button --}}
                <a href="{{ route('admin.sliders.create') }}" class="tf-button style-1 w208">
                    + Add Slider
                </a>

            </div>

            <div class="wg-table table-all-user">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Heading</th>
                            <th>Button</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($sliders as $key => $slider)
                        <tr>
                            <td>{{ $key + 1 }}</td>

                            {{-- Image --}}
                            <td>
                                <img src="{{ asset('storage/' . $slider->image) }}" 
                                     alt="{{ $slider->image_alt }}" 
                                     width="80" height="50" style="object-fit:cover;">
                            </td>

                            {{-- Heading --}}
                            <td>{{ $slider->heading }}</td>

                            {{-- Button --}}
                            <td>
                                @if($slider->button_text)
                                    <span class="badge bg-info">
                                        {{ $slider->button_text }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Order --}}
                            <td>{{ $slider->order }}</td>

                            {{-- Status --}}
                            <td>
                                @if($slider->active_status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            {{-- Created --}}
                            <td>{{ $slider->created_at->format('d M Y') }}</td>

                            {{-- Actions --}}
                            <td>
                                <div class="d-flex gap-2">

                                    <a href="{{ route('admin.sliders.edit', $slider->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.sliders.destroy', $slider->id) }}" 
                                          method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger delete">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No sliders found</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="divider"></div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $sliders->links() }}
            </div>

        </div>
    </div>
</div>
@endsection