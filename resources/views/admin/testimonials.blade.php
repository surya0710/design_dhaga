@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Testimonials</h3>
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
                    <div class="text-tiny">Testimonials</div>
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
                <a class="tf-button style-1 w208" href="{{route('admin.testimonial.add')}}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            <div class="wg-table table-all-user">
            @if(Session::has('status'))
                <p class="alert alert-success"> {{Session::get('status')}}</p>
            @endif
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>testimonial</th>
                            <th>Product id</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($testimonials as $testimonial)
                        <tr>
                            <td>{{$testimonial->id}}</td>
                            <td class="pname">
                                <div class="name">
                                    <a href="{{$testimonial->name}}" class="body-title-2">{{$testimonial->name}}</a>
                                </div>
                            </td>
                            <td>{{$testimonial->testimonial}}</td>
                            <td><a href="javascript:void(0);" target="_blank">{{$testimonial->product_id}}</a></td>
                            <td>
                                <div class="list-icon-function">
                                    <a href="{{route('admin.testimonial.edit',['id'=>$testimonial->id])}}">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>
                                    <form action="{{route('admin.testimonial.delete',['id'=>$testimonial->id])}}" method="POST">
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
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(function(){
            $('.delete').on('click',function(e){
                e.preventDefault();
                var form = $(this).closest('form');
                swal({
                    title: "Are you sure?",
                    text: "You want to delete this record?",
                    type: "warning",
                    buttons: ["No","Yes"],
                    confirmButtonColor: '#dc3545'
                }).then(function(result){
                    if(result){
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush