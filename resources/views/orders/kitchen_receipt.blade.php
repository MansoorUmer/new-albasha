<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen Receipt #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            padding: 10px;
            margin: 0;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .order-info {
            margin-bottom: 15px;
        }
        .order-info p {
            margin: 3px 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }
        td {
            padding: 8px 0;
            font-size: 1.1em;
            font-weight: bold;
            border-bottom: 1px dashed #eee;
        }
        .qty {
            width: 50px;
            text-align: center;
        }
        .item-name {
            padding-left: 10px;
        }
        .instruction {
            margin-top: 20px;
            padding: 10px;
            border: 2px solid #000;
            background: #f9f9f9;
        }
        .instruction strong {
            display: block;
            font-size: 1.2em;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 2px dashed #000;
            padding-top: 10px;
            font-size: 0.9em;
        }
        @media print {
            body { padding: 0; }
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); window.close();">
<div class="receipt">
    <div class="header">
        <h1 style="margin: 0; font-size: 1.4em;">KITCHEN ORDER</h1>
    </div>

    <div class="order-info">
        <p>Order ID: #{{ $order->id }}</p>
        <p>Table: {{ $order->table_no ?? 'N/A' }}</p>
        <p>Time: {{ $order->created_at->format('H:i') }} ({{ $order->created_at->format('M d, Y') }})</p>
        <p>Type: {{ strtoupper($order->type ?? 'N/A') }}</p>
    </div>

    <table>
        <thead>
        <tr>
            <th class="qty">QTY</th>
            <th class="item-name">ITEM</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td class="qty">{{ $item->quantity }}</td>
                <td class="item-name">{{ $item->product->name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if ($order->instruction)
        <div class="instruction">
            <strong>KITCHEN NOTES:</strong>
            {{ $order->instruction }}
        </div>
    @endif

    <div class="footer">
        *** End of Order ***
    </div>
</div>
</body>
</html>
