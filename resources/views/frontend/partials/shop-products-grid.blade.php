@foreach($products as $product)
    @include('frontend.partials.shop-product-item', ['product' => $product])
@endforeach
