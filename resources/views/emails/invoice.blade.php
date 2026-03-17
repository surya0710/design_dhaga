<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Invoice</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0;">

  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #ddd;">
    <tr>
      <td style="padding: 20px; background-color:rgb(255, 255, 255);">
        <table width="100%">
          <tr>
            <td align="left" style="vertical-align: top;">
              <img src="{{ asset('assets/images/logo/ratnabhagya-logo-1.png') }}" alt="RatnaBhagya Logo" style="max-height: 60px;">
            </td>
            <td align="right" style="color:rgb(0, 0, 0); font-size: 13px; line-height: 20px;">
              <strong>RatnaBhagya</strong><br>
              845, Ground Floor, Udyog Vihar Phase V,<br>
              Sector 19, Gurugram, Haryana 122016<br>
              contact@ratnabhagya.com<br>
              +91 9999097979
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td style="padding: 20px;">
        <p><strong>Invoice No:</strong> #{{ $order->id }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
        <p><strong>Name:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Phone:</strong> {{ $order->mobile }}</p>
        <p><strong>Shipping Address:</strong> {{ $order->street }}, {{ $order->city }}, {{ $order->state }}, {{ $order->country }}, {{ $order->pincode }}</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
          <thead>
            <tr style="background-color: #f4f4f4;">
              <th align="left" style="padding: 10px; border: 1px solid #ddd;">Product</th>
              <th align="left" style="padding: 10px; border: 1px solid #ddd;">Sku</th>
              <th align="left" style="padding: 10px; border: 1px solid #ddd;">Qty</th>
              <th align="right" style="padding: 10px; border: 1px solid #ddd;">Price</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->product->name }}<br>
                @if($item->certificate_name)
                <span>{{ $item->certificate_name .','. format_currency($item->certificate_price, session('currency','INR')) }}</span>
                @endif
              </td>
              <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->product->sku }}</td>
              <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->quantity }}</td>
              <td style="padding: 10px; border: 1px solid #ddd;" align="right">₹{{ number_format($item->price, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </td>
    </tr>

    <tr>
      <td style="padding: 20px;">
        <p><strong>Subtotal:</strong> ₹{{ number_format($order->total + $order->coupon_discount - $order->delivery_charge, 2) }}</p>
        @if($order->delivery_charge > 0)
        <p><strong>Delivery Charge:</strong> + ₹{{ number_format($order->delivery_charge, 2) }}</p>
        @endif
        @if($order->coupon_discount > 0)
        <p><strong>Coupon Discount:</strong> - ₹{{ number_format($order->coupon_discount, 2) }}</p>
        @endif
        <p><strong>Grand Total:</strong> ₹{{ number_format($order->total, 2) }}</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 20px; text-align: center; background-color: #f4f4f4; color: #555;">
        Thank you for shopping with us!<br><br>
        <a href="{{ route('home.index') }}" style="color: #dab14d; text-decoration: none;">Visit RatnaBhagya</a>
      </td>
    </tr>
  </table>

</body>
</html>
