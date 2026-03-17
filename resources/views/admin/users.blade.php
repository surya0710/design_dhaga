@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Users</h3>
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
                    <div class="text-tiny">All Users</div>
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
                <!-- <a class="tf-button style-1 w208" href="{{route('admin.products.add')}}"><i
                        class="icon-plus"></i>Add new</a> -->
            </div>
            @if(Session::has('status'))
                <p class="alert alert-success"> {{Session::get('status')}}</p>
            @endif
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Email Verified At</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            
                            <tr>
                                <td>{{$user->id}}</td>
                                <td class="pname">
                                    <div class="name">
                                        <a href="#" class="body-title-2">{{ $user->name }}</a>
                                    </div>
                                </td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->mobile}}</td>
                                <td>{{$user->email_verified_at}}</td>
                                <td>{{ $user->created_at }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection