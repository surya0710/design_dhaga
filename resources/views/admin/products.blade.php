@extends('layouts.admin')
@section('content')
<style>
    .table-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        display: block;
        white-space: nowrap;
    }

    .table-scroll table {
        min-width: 1400px;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Products</h3>
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
                    <div class="text-tiny">All Products</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET" action="{{ route('admin.products') }}">
                        <fieldset class="name">
                            <input type="text" placeholder="Search products by name, SKU, category..." class="" name="search"
                                tabindex="2" value="{{ request('search') }}">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{route('import.products')}}"><i
                        class="icon-plus"></i>Import Products</a>
                <a class="tf-button style-1 w208" href="{{route('admin.products.add')}}"><i class="icon-plus"></i>Add new</a>
            </div>
            @if(Session::has('status'))
                <p class="alert alert-success"> {{Session::get('status')}}</p>
            @endif
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>SalePrice</th>
                            <th width="10%">Category</th>
                            <th width="10%">Featured</th>
                            <th width="5%">Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($product as $products)
                            
                            <tr>
                                <td width="5%">{{ $product->firstItem() + $loop->index }}</td>
                                <td>{{ $products->name }}</td>
                                <td>
                                    @if($products->has_active_variants)
                                        From {{ number_format($products->display_price, 2) }}
                                    @else
                                        {{ $products->regular_price }}
                                    @endif
                                </td>
                                <td>
                                    @if($products->has_active_variants)
                                        Variant pricing
                                    @else
                                        {{ $products->sale_price }}
                                    @endif
                                </td>
                                <td width="10%">{{ $products->category->name ?? '' }}</td>
                                <td width="10%">{{$products->featured == 0 ? "No" : "Yes"}}</td>
                                <td width="5%">{{$products->quantity}}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <form action="{{ route('admin.product.toggleStatus', $products->id) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-link text-primary p-0">
                                                @if($products->status == 1)
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
                                        <a href="{{route('admin.product.edit', $products->id)}}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <!-- <form action="{{route('admin.product.delete',$products->id)}}" method="POST" >
                                            <div class="item text-danger delete">
                                                <a href="{{url('admin/products/delete',$products->id)}}"><i class="icon-trash-2"></i></a>
                                            </div>
                                        </form> -->
                                        <form action="{{ route('admin.product.delete', $products->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0">
                                                <i style="font-size: 17px;" class="icon-trash-2"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No products found.</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $product->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
