@if($product->has_active_variants)
    <span class="text-black text-bold">From ₹{{ number_format($product->display_price, 0) }}</span>
@elseif ($product->sale_price)
    <span class="text-black text-bold">₹{{ number_format($product->sale_price, 0) }}</span>
    <span class="text-decoration-line-through text-muted small text-light">
        ₹{{ number_format($product->regular_price, 0) }}
    </span>
    @if((float) $product->regular_price > 0)
        <span class="text-maroon small">
            Save {{ number_format((1 - ($product->sale_price / $product->regular_price)) * 100, 0) }}%
        </span>
    @endif
@else
    <span class="text-black">₹{{ number_format($product->regular_price, 0) }}</span>
@endif
