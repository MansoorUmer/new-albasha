<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 14px;
            padding: 20px;
        }
        h2, h4 {
            text-align: center;
            margin: 0;
        }
        .receipt {
            max-width: 600px;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border: 1px solid #ddd;
        }
        tfoot td {
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body onload="window.print(); window.close();">
<div class="receipt">
    <h2>New Albasha POS</h2>
    <h4>Order Receipt</h4>
    <p><strong>Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Table:</strong> {{ $order->table_no ?? 'N/A' }}</p>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ number_format($item->product->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->product->price * $item->quantity, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4" class="text-right">Subtotal</td>
            <td>{{ config('settings.currency_symbol') }} {{ number_format($order->total(), 2) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-right">Paid</td>
            <td>{{ config('settings.currency_symbol') }} {{ number_format($order->receivedAmount(), 2) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-right">Change</td>
            <td>{{ config('settings.currency_symbol') }} {{ number_format($order->receivedAmount() - $order->total(), 2) }}</td>
        </tr>
        </tfoot>
    </table>

    @if ($order->instruction)
        <p><strong>Instruction:</strong> {{ $order->instruction }}</p>
    @endif

    <div class="footer">
        <p>Thank you for dining with us!</p>
    </div>
</div>
</body>
</html>
