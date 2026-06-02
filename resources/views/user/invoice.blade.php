<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11.5px;
            color: #161616;
            background: #fff;
            padding: 26px;
        }
        .invoice {
            max-width: 860px;
            margin: 0 auto;
            border: 1px solid #202020;
        }
        .topbar {
            background: #111;
            color: #fff;
            padding: 18px 20px;
            display: table;
            width: 100%;
        }
        .brand, .invoice-title {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .brand-name {
            font-size: 21px;
            font-weight: bold;
            letter-spacing: .5px;
            margin-bottom: 5px;
        }
        .brand-meta {
            color: #e8e8e8;
            line-height: 1.5;
            font-size: 10.5px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            font-size: 24px;
            line-height: 1;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .invoice-title p {
            color: #e8e8e8;
            line-height: 1.5;
        }
        .section {
            padding: 16px 20px;
            border-bottom: 1px solid #d6d6d6;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .col + .col { padding-left: 24px; }
        .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: .7px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .value {
            line-height: 1.65;
        }
        .value strong { font-size: 12.5px; }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            border: none;
            line-height: 1.45;
        }
        .meta-table td:first-child {
            color: #666;
            width: 44%;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items th {
            background: #f1f1f1;
            color: #111;
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
            padding: 8px 5px;
            text-align: right;
            font-size: 10px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .items th:first-child,
        .items td:first-child {
            text-align: left;
        }
        .items td {
            padding: 8px 5px;
            border-bottom: 1px solid #e1e1e1;
            text-align: right;
            vertical-align: top;
            line-height: 1.45;
        }
        .money {
            white-space: nowrap;
            word-break: keep-all;
        }
        .product-name {
            font-weight: bold;
            color: #111;
        }
        .product-meta {
            color: #666;
            font-size: 10.5px;
            margin-top: 2px;
        }
        .totals {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            border: none;
            padding: 4px 0;
        }
        .totals td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .grand td {
            border-top: 1px solid #111;
            padding-top: 8px;
            font-size: 14px;
        }
        .note {
            line-height: 1.6;
            color: #444;
            font-size: 10.5px;
        }
        .footer {
            background: #f7f7f7;
            padding: 12px 20px;
            color: #555;
            font-size: 10.5px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
@php
    $subtotal = (float) ($order->subtotal ?? $order->items->sum('total'));
    $couponDiscount = (float) ($order->coupon_discount ?? 0);
    $shipping = (float) (($order->shipping ?? 0) ?: ($order->delivery_charge ?? 0));
    $taxableTotal = max($subtotal - $couponDiscount, 0);
    $gstType = $order->gst_type ?: 'igst';
    $cgstRate = (float) ($order->cgst_rate ?? 0);
    $sgstRate = (float) ($order->sgst_rate ?? 0);
    $igstRate = (float) ($order->igst_rate ?? 5);
    $gstLabel = $gstType === 'cgst_sgst'
        ? number_format($cgstRate, 1) . '% CGST + ' . number_format($sgstRate, 1) . '% SGST'
        : number_format($igstRate, 1) . '% IGST';
    $distributedDiscount = 0;
    $itemCount = $order->items->count();
@endphp

<div class="invoice">
    <div class="topbar">
        <div class="brand">
            <div class="brand-name">Design Dhaga</div>
            <div class="brand-meta">
                GSTIN: 06BBOPK8637H1Z7<br>
                {{ $settings->office_address ?? '' }}
            </div>
        </div>
        <div class="invoice-title">
            <h1>Tax Invoice</h1>
            <p>Invoice #{{ $order->invoice_number ?? $order->id }}<br>
            Date: {{ optional($order->paid_at ?? $order->created_at)->format('d M Y') }}</p>
        </div>
    </div>

    <div class="section">
        <div class="grid">
            <div class="col">
                <div class="label">Bill To / Ship To</div>
                <div class="value">
                    <strong>{{ $order->name }}</strong><br>
                    {{ $order->address_line_1 }}<br>
                    {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}<br>
                    {{ $order->country ?? 'India' }}<br>
                    Phone: {{ $order->phone }}<br>
                    Email: {{ $order->email }}
                </div>
            </div>
            <div class="col">
                <div class="label">Order Details</div>
                <table class="meta-table">
                    <tr><td>Order Number</td><td>#{{ $order->id }}</td></tr>
                    <tr><td>Order Date</td><td>{{ optional($order->created_at)->format('d M Y') }}</td></tr>
                    <tr><td>Payment ID</td><td>{{ $order->razorpay_payment_id ?? 'Pending' }}</td></tr>
                    <tr><td>Payment Method</td><td>{{ strtoupper($order->payment_method ?? 'Razorpay') }}</td></tr>
                    <tr><td>Place of Supply</td><td>{{ strtoupper($order->state ?? '') }}</td></tr>
                    <tr><td>Nature of Supply</td><td>Goods</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="label">Fabric Items - Prices Exclusive of GST</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:26%;">Item</th>
                    <th style="width:7%;">Qty</th>
                    <th style="width:12%;">Gross</th>
                    <th style="width:10%;">Discount</th>
                    <th style="width:12%;">Taxable</th>
                    <th style="width:8%;">CGST</th>
                    <th style="width:8%;">SGST</th>
                    <th style="width:8%;">IGST</th>
                    <th style="width:9%;">Line<br>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    @php
                        $grossAmount = (float) ($item->total ?? ((float) $item->price * (int) $item->quantity));
                        $lineDiscount = 0;

                        if ($couponDiscount > 0 && $subtotal > 0) {
                            $lineDiscount = $index === $itemCount - 1
                                ? round($couponDiscount - $distributedDiscount, 2)
                                : round(($couponDiscount * $grossAmount) / $subtotal, 2);
                            $distributedDiscount += $lineDiscount;
                        }

                        $taxable = max($grossAmount - $lineDiscount, 0);
                        $cgst = $gstType === 'cgst_sgst' ? round(($taxable * $cgstRate) / 100, 2) : 0;
                        $sgst = $gstType === 'cgst_sgst' ? round(($taxable * $sgstRate) / 100, 2) : 0;
                        $igst = $gstType === 'igst' ? round(($taxable * $igstRate) / 100, 2) : 0;
                        $lineTotal = $taxable + $cgst + $sgst + $igst;
                    @endphp
                    <tr>
                        <td>
                            <div class="product-name">{{ $item->product_name }}</div>
                            @if($item->size || $item->fabric_type || $item->sku)
                                <div class="product-meta">
                                    @if($item->fabric_type) Fabric: {{ $item->fabric_type }} @endif
                                    @if($item->size) {{ $item->fabric_type ? ' | ' : '' }}Size: {{ $item->size }} @endif
                                    @if($item->sku) {{ ($item->fabric_type || $item->size) ? ' | ' : '' }}SKU: {{ $item->sku }} @endif
                                </div>
                            @endif
                            <div class="product-meta">HSN: {{ $item->hsn ?? 'N/A' }} | {{ $gstLabel }}</div>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td class="money">₹{{ number_format($grossAmount, 2) }}</td>
                        <td class="money">₹{{ number_format($lineDiscount, 2) }}</td>
                        <td class="money">₹{{ number_format($taxable, 2) }}</td>
                        <td class="money">{{ $cgst > 0 ? '₹'.number_format($cgst, 2) : '-' }}</td>
                        <td class="money">{{ $sgst > 0 ? '₹'.number_format($sgst, 2) : '-' }}</td>
                        <td class="money">{{ $igst > 0 ? '₹'.number_format($igst, 2) : '-' }}</td>
                        <td class="money">₹{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="grid">
            <div class="col">
                <div class="label">Declaration</div>
                <div class="note">
                    The goods sold are intended for end-user consumption and are not for retail sale.
                    GST is calculated on the exclusive product value after discount.
                </div>
            </div>
            <div class="col">
                <table class="totals">
                    <tr><td>Subtotal</td><td class="money">₹{{ number_format($subtotal, 2) }}</td></tr>
                    @if($couponDiscount > 0)
                        <tr><td>Coupon Discount</td><td class="money">- ₹{{ number_format($couponDiscount, 2) }}</td></tr>
                    @endif
                    <tr><td>Taxable Amount</td><td class="money">₹{{ number_format($taxableTotal, 2) }}</td></tr>
                    @if($gstType === 'cgst_sgst')
                        <tr><td>CGST ({{ number_format($cgstRate, 2) }}%)</td><td class="money">₹{{ number_format((float) $order->cgst_amount, 2) }}</td></tr>
                        <tr><td>SGST ({{ number_format($sgstRate, 2) }}%)</td><td class="money">₹{{ number_format((float) $order->sgst_amount, 2) }}</td></tr>
                    @else
                        <tr><td>IGST ({{ number_format($igstRate, 2) }}%)</td><td class="money">₹{{ number_format((float) $order->igst_amount, 2) }}</td></tr>
                    @endif
                    <tr><td>Shipping</td><td class="money">₹{{ number_format($shipping, 2) }}</td></tr>
                    <tr class="grand"><td>Grand Total</td><td class="money">₹{{ number_format((float) $order->total, 2) }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        Registered Address: Design Dhaga, {{ $company->reg_address ?? ($settings->store_address ?? '') }}<br>
        For support, contact {{ $settings->support_email ?? 'support@designdhaga.com' }}.
    </div>
</div>
</body>
</html>
