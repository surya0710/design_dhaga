@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Stories</h3>

            <a class="tf-button style-1 w208"
                href="{{ route('admin.story.add') }}">

                <i class="icon-plus"></i>Add Story
            </a>
        </div>

        <div class="wg-box">

            @if(Session::has('status'))
                <p class="alert alert-success">
                    {{ Session::get('status') }}
                </p>
            @endif

            <div class="wg-table table-all-user">

                <table class="table table-striped table-bordered">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Year</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($stories as $story)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $story->year }}</td>

                            <td style="max-width:400px">
                                {{ Str::limit($story->description, 120) }}
                            </td>

                            <td>
                                <img src="{{ asset($story->image) }}"
                                    width="80">
                            </td>

                            <td>{{ $story->display_order }}</td>

                            <td>
                                @if($story->status)
                                    <span class="badge bg-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td>

                                <div class="list-icon-function">

                                    <a href="{{ route('admin.story.edit', $story->id) }}">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>

                                    <form action="{{ route('admin.story.delete', $story->id) }}"
                                        method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <div class="item text-danger delete">
                                            <i class="icon-trash-2"></i>
                                        </div>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>
    </div>
</div>

@endsection