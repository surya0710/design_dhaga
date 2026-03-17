@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Asked Questions</h3>
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
                    <div class="text-tiny">Asked Questions</div>
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
                
            </div>
            <div class="wg-table table-all-user">
            <!-- @if(Session::has('success'))
                <p class="alert alert-success"> {{Session::get('success')}}</p>
            @endif
            @if(Session::has('error'))
                <p class="alert alert-danger"> {{Session::get('error')}}</p>
            @endif -->
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Product Name</th>
                            <th>User id & Name</th>
                            <th>Question</th>
                            <th>Asked On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($askedQuestions as $askedQuestion)
                        <tr>
                            <td>{{$askedQuestion->id}}</td>
                            <td class="pname">
                                <div class="name">
                                    {{$askedQuestion->name}}
                                </div>
                            </td>
                            <td><a href="mailto:{{$askedQuestion->email }}"> {{$askedQuestion->email}}</a></td>
                            <td><a href="tel:{{$askedQuestion->mobile}}" target="_blank">{{$askedQuestion->mobile}}</a></td>
                            <td>{{$askedQuestion->product_name}}</td>
                            <td>{{$askedQuestion->user->id .', '. $askedQuestion->user->name}}</a></td>
                            <td>{{$askedQuestion->message}}</a></td>
                            <td>{{$askedQuestion->created_at}}</a></td>
                            <td>
                                <div class="list-icon-function">
                                    <form action="{{route('admin.question.delete',['id'=>$askedQuestion->id])}}" method="POST">
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