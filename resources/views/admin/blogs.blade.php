@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Blogs</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="index.html">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">All Blogs</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name"
                                tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{route('import.blogs')}}"><i
                        class="icon-plus"></i>Import Products</a>
                <a class="tf-button style-1 w208" href="{{route('admin.blog.add')}}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            @if(Session::has('status'))
                <p class="alert alert-success"> {{Session::get('status')}}</p>
            @endif
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blogs as $blog)
                            <tr>
                                <td>{{$blog->id}}</td>
                                <td>
                                    
                                    <div class="name">
                                        <a href="#" class="body-title-2">{{ $blog->title }}</a>
                                        <div class="text-tiny mt-3">{{$blog->slug}}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="image">
                                        <img src="{{asset('uploads/blogs/thumbnails/')}}/{{$blog->image}}" alt="{{$blog->name}}" class="image">
                                    </div>
                                </td>
                                <td>{{ Str::limit(strip_tags($blog->content), 150) }}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <form action="{{ route('admin.blog.toggleStatus', $blog->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-link text-primary p-0">
                                                @if($blog->status)
                                                    <i style="font-size: 17px;" class="icon-check"></i>
                                                @else
                                                    <i style="font-size: 17px;" class="icon-x text-danger "></i>
                                                @endif
                                            </button>
                                        </form>
                                        <!-- <a href="" target="_blank">
                                            <div class="item eye">
                                                <i class="icon-eye"></i>
                                            </div>
                                        </a> -->
                                        <a href="{{route('admin.blog.edit', $blog->id)}}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <form action="{{ route('admin.blog.delete', $blog->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Blog?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0">
                                                <i style="font-size: 17px;" class="icon-trash-2"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $blogs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection