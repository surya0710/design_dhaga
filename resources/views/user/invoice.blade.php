<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
            background: #fff;
            padding: 30px;
        }

        .wrapper {
            max-width: 860px;
            margin: 0 auto;
        }

        /* TITLE */
        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        /* DIVIDER */
        .divider {
            border: none;
            border-top: 1.5px solid #111;
            margin: 10px 0;
        }
        .divider-thin {
            border: none;
            border-top: 0.5px solid #ccc;
            margin: 10px 0;
        }

        /* META GRID */
        .meta-grid {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }
        .meta-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
        }
        .meta-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            text-align: right;
        }
        .meta-row {
            margin-bottom: 3px;
            font-size: 12px;
            line-height: 1.7;
        }
        .meta-row b {
            font-weight: bold;
        }

        /* ADDRESS SECTION */
        .addr-grid {
            display: table;
            width: 100%;
            margin: 10px 0;
        }
        .addr-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .addr-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }
        .addr-label {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 12px;
        }
        .addr-body {
            font-size: 12px;
            line-height: 1.75;
            color: #111;
        }

        /* GSTIN ROW */
        .gstin-row {
            font-size: 12px;
            font-weight: bold;
            margin: 8px 0 4px 0;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-top: 8px;
        }
        thead tr {
            border-top: 1.5px solid #111;
            border-bottom: 1.5px solid #111;
        }
        th {
            padding: 7px 6px;
            text-align: right;
            font-weight: bold;
            font-size: 11.5px;
            white-space: nowrap;
        }
        th:first-child { text-align: left; padding-left: 0; }
        th:last-child { padding-right: 0; }

        .product-row td {
            padding: 6px 6px 2px 6px;
            vertical-align: top;
        }
        .product-name-cell {
            font-weight: bold;
            font-size: 11.5px;
            padding-left: 0 !important;
        }

        .item-row td {
            padding: 4px 6px;
            text-align: right;
            vertical-align: top;
            font-size: 11.5px;
            border-bottom: 0.5px solid #ddd;
        }
        .item-row td:first-child {
            text-align: left;
            padding-left: 0;
        }
        .item-row td:last-child { padding-right: 0; }

        .total-row td {
            padding: 7px 6px;
            text-align: right;
            font-weight: bold;
            font-size: 12px;
            border-top: 1.5px solid #111;
            border-bottom: 1.5px solid #111;
        }
        .total-row td:first-child {
            text-align: left;
            padding-left: 0;
        }
        .total-row td:last-child { padding-right: 0; }

        /* DECLARATION */
        .declaration {
            margin-top: 14px;
            font-size: 11.5px;
            line-height: 1.7;
        }
        .declaration b {
            font-weight: bold;
        }

        /* REG ADDRESS */
        .reg-address {
            margin-top: 10px;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }
        .reg-address b { font-weight: bold; }

        /* FOOTER */
        .footer {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 0.5px solid #ccc;
            font-size: 11px;
            color: #444;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- TITLE -->
    <div class="title">Tax Invoice</div>

    <div class="divider"></div>

    <!-- META -->
    <div class="meta-grid">
        <div class="meta-left">
            <div class="meta-row"><b>Invoice Number:</b> {{ $order->invoice_number ?? $order->id }}</div>
            <div class="meta-row"><b>Order Number:</b> {{ $order->id }}</div>
            <div class="meta-row"><b>Nature of Transaction:</b> Inter-State</div>
            <div class="meta-row"><b>Place of Supply:</b> {{ strtoupper($order->state) }}</div>
        </div>
        <div class="meta-right">
            <div class="meta-row"><b>PacketID:</b> {{ $order->packet_id ?? '—' }}</div>
            <div class="meta-row"><b>Invoice Date:</b> {{ $order->created_at->format('d M Y') }}</div>
            <div class="meta-row"><b>Order Date:</b> {{ $order->created_at->format('d M Y') }}</div>
            <div class="meta-row"><b>Nature of Supply:</b> Goods</div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- ADDRESSES -->
    <div class="addr-grid">
        <div class="addr-left">
            <div class="addr-label">Bill to / Ship to:</div>
            <div class="addr-body">
                <b>{{ $order->name }}</b><br>
                {{ $order->address_line_1 }}<br>
                City {{ $order->city }} - {{ $order->pincode }} {{ $order->state_code ?? '' }}, {{ $order->country }}<br>
            </div>
        </div>
        <div class="addr-right">
            <div class="addr-grid" style="margin:0;">
                <div class="addr-left" style="padding-right:10px;">
                    <div class="addr-label">Bill From:</div>
                    <div class="addr-body">
                        Design Dhaga<br>
                        {{ $company->address ?? '' }}<br>
                        {{ $company->city ?? '' }}, {{ $company->state ?? '' }}-{{ $company->pincode ?? '' }}
                    </div>
                </div>
                <div class="addr-right" style="padding-left:10px;">
                    <div class="addr-label">Ship From:</div>
                    <div class="addr-body">
                        Design Dhaga<br>
                        {{ $company->address ?? '' }}<br>
                        {{ $company->city ?? '' }}, {{ $company->state ?? '' }}-{{ $company->pincode ?? '' }}
                    </div>
                </div>
            </div>
            <div class="gstin-row">GSTIN Number: {{ $company->gstin ?? '—' }}</div>
        </div>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th style="text-align:left; width:30%;">Qty</th>
                <th style="width:10%;">Gross<br>Amount</th>
                <th style="width:10%;">Discount</th>
                <th style="width:10%;">Other<br>Charges</th>
                <th style="width:12%;">Taxable<br>Amount</th>
                <th style="width:8%;">CGST</th>
                <th style="width:8%;">SGST/<br>UGST</th>
                <th style="width:8%;">IGST</th>
                <th style="width:4%;">Cess</th>
                <th style="width:10%;">Total<br>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            @php
                $taxable = $item->total / 1.18;
                $igst    = $item->total - $taxable;
            @endphp

            <!-- Product name row -->
            <tr class="product-row">
                <td colspan="10" class="product-name-cell">
                    {{ $item->product_name }}
                    @if($item->sku ?? false) ({{ $item->sku }}) @endif<br>
                    <span style="font-weight:normal;">HSN: {{ $item->hsn ?? '—' }}, 5.0% IGST</span>
                </td>
            </tr>

            <!-- Item values row -->
            <tr class="item-row">
                <td>{{ $item->quantity }}</td>
                <td>Rs {{ number_format($item->price, 2) }}</td>
                <td>Rs {{ number_format($item->discount ?? 0, 2) }}</td>
                <td>Rs 0.00</td>
                <td>Rs {{ number_format($taxable, 2) }}</td>
                <td></td>
                <td></td>
                <td>Rs {{ number_format($igst, 2) }}</td>
                <td></td>
                <td>Rs {{ number_format($item->total, 2) }}</td>
            </tr>

            @endforeach

            <!-- TOTAL -->
            <tr class="total-row">
                <td><b>TOTAL</b></td>
                <td>Rs {{ number_format($order->items->sum('price'), 2) }}</td>
                <td>Rs {{ number_format($order->items->sum('discount') ?? 0, 2) }}</td>
                <td>Rs 0.00</td>
                <td>Rs {{ number_format($order->items->sum(fn($i) => $i->total / 1.18), 2) }}</td>
                <td></td>
                <td></td>
                <td>Rs {{ number_format($order->items->sum(fn($i) => $i->total - $i->total / 1.18), 2) }}</td>
                <td></td>
                <td>Rs {{ number_format($order->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- DECLARATION -->
    <div class="declaration">
        <b>DECLARATION</b><br>
        The goods sold are intended for end-user consumption and are not for retail sale.
    </div>

    <div class="divider-thin" style="margin-top:12px;"></div>

    <!-- REG ADDRESS -->
    <div class="reg-address">
        <b>Reg Address:</b> Design Dhaga, {{ $company->reg_address ?? '' }}
    </div>

    <!-- FOOTER -->
    <div class="footer">
        If you have any questions, feel free to contact us.
    </div>

</div>

</body>
</html>