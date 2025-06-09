<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        body {
            font-family: monospace;
            width: 300px;
            margin: 0 auto;
        }
        h2, h4 {
            text-align: center;
            margin: 5px 0;
        }
        table {
            width: 100%;
            font-size: 14px;
        }
        th, td {
            text-align: left;
            padding: 2px 0;
        }
        .total {
            font-weight: bold;
            border-top: 1px dashed #000;
        }
        .text-right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
        .print-btn {
            margin-top: 20px;
            text-align: center;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<h2>Your Restaurant Name</h2>
<h4>Receipt #{{ $order->id }}</h4>
<p>Date: {{ $order->created_at->format('d M Y h:i A') }}</p>

<table>
    <thead>
    <tr>
        <th>Item</th>
        <th class="text-right">Qty</th>
        <th class="text-right">Price</th>
        <th class="text-right">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($order->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">{{ number_format($item->price, 2) }}</td>
            <td class="text-right">{{ number_format($item->price * $item->quantity, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p class="total text-right">Total: {{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 2) }}</p>

<div class="center">Thank you!</div>

<div class="print-btn">
    <button onclick="window.print()">Print</button>
</div>

</body>
</html>
