<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }
        h2, h4, p {
            margin: 0 0 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background: #f3f3f3;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Tax Invoice</h2>

    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
    <p><strong>Invoice Date:</strong> {{ optional($order->paid_at)->format('d-m-Y H:i') }}</p>
    <p><strong>Payment ID:</strong> {{ $order->razorpay_payment_id }}</p>

    <div class="mt-20">
        <h4>Billing Details</h4>
        <p>{{ $order->name }}</p>
        <p>{{ $order->address_line_1 }}</p>
        <p>{{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}</p>
        <p>{{ $order->phone }}</p>
        <p>{{ $order->email }}</p>
    </div>

    <div class="mt-20">
        <h4>Items</h4>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="mt-20">
        <tr>
            <td><strong>Subtotal</strong></td>
            <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
        </tr>

        @if(($order->coupon_discount ?? 0) > 0)
        <tr>
            <td><strong>Coupon Discount</strong></td>
            <td class="text-right">-{{ number_format($order->coupon_discount, 2) }}</td>
        </tr>
        @endif

        @if(($order->shipping ?? 0) > 0)
        <tr>
            <td><strong>Shipping</strong></td>
            <td class="text-right">{{ number_format($order->shipping, 2) }}</td>
        </tr>
        @endif

        @if(($order->gst_type ?? '') === 'cgst_sgst')
            <tr>
                <td><strong>CGST ({{ number_format($order->cgst_rate, 2) }}%)</strong></td>
                <td class="text-right">{{ number_format($order->cgst_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>SGST ({{ number_format($order->sgst_rate, 2) }}%)</strong></td>
                <td class="text-right">{{ number_format($order->sgst_amount, 2) }}</td>
            </tr>
        @else
            <tr>
                <td><strong>IGST ({{ number_format($order->igst_rate, 2) }}%)</strong></td>
                <td class="text-right">{{ number_format($order->igst_amount, 2) }}</td>
            </tr>
        @endif

        <tr>
            <td><strong>Grand Total</strong></td>
            <td class="text-right"><strong>{{ number_format($order->total, 2) }}</strong></td>
        </tr>
    </table>
</body>
</html>