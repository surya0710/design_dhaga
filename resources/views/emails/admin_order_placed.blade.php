<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Order Received</title>
</head>
<body>
    <h2>New Order Received</h2>

    <p>A new order has been placed successfully.</p>

    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
    <p><strong>Customer:</strong> {{ $order->name }}</p>
    <p><strong>Email:</strong> {{ $order->email }}</p>
    <p><strong>Phone:</strong> {{ $order->phone }}</p>
    <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
    <p><strong>Order Status:</strong> {{ ucfirst($order->order_status) }}</p>
    <p><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>

    <h3>Ordered Products</h3>
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th align="left">Product</th>
                <th align="left">Variant</th>
                <th align="right">Price</th>
                <th align="right">Qty</th>
                <th align="right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>
                        @php
                            $parts = [];
                            if (!empty($item->fabric_type)) $parts[] = 'Fabric: ' . $item->fabric_type;
                            if (!empty($item->size)) $parts[] = 'Size: ' . $item->size;
                            if (!empty($item->sku)) $parts[] = 'SKU: ' . $item->sku;
                        @endphp
                        {{ !empty($parts) ? implode(' | ', $parts) : 'N/A' }}
                    </td>
                    <td align="right">₹{{ number_format($item->price, 2) }}</td>
                    <td align="right">{{ $item->quantity }}</td>
                    <td align="right">₹{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 16px;"><strong>Shipping Address:</strong> {{ $order->full_address }}</p>
</body>
</html>
