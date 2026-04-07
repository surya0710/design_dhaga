<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body>
    <h2>Thank you for your order</h2>

    <p>Your order has been confirmed and payment was received successfully.</p>

    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
    <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
    <p><strong>Order Status:</strong> {{ ucfirst($order->order_status) }}</p>
    <p><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>

    <p>Your invoice PDF is attached with this email.</p>
</body>
</html>