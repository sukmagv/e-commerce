<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice {{ $order->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f8f9fa;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }

        h1, h2, h3 {
            color: #2c3e50;
        }

        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background: #3498db;
            color: #fff;
            padding: 10px;
            text-align: left;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .totals {
            margin-top: 15px;
            display: table;
            width: 100%;
        }

        .total-row {
            display: table-row;
        }

        .total-row .label,
        .total-row .value {
            display: table-cell;
            padding: 5px 0;
        }

        .total-row .label {
            text-align: left;
        }

        .total-row .value {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85em;
            color: #777;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="invoice-header">
        <h1>INVOICE</h1>
        <p>Order Code: {{ $order->code }}</p>
    </div>

    <p><strong>Name:</strong> {{ $order->user->name }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Discount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->orderItem->product->name }}</td>
                <td>{{ $order->orderItem->qty }}</td>
                <td>{{ number_format($order->orderItem->normal_price) }}</td>
                <td>{{ number_format($order->orderItem->total_price) }}</td>
                <td>{{ $order->orderItem->discount ? number_format($order->orderItem->discount_price) : 0 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="label">Sub Total:</span>
            <span class="value">{{ number_format($order->sub_total) }}</span>
        </div>
        <div class="total-row">
            <span class="label">Tax:</span>
            <span class="value">{{ number_format($order->tax_amount) }}</span>
        </div>
        <div class="total-row">
            <span class="label">Grand Total:</span>
            <span class="value">{{ number_format($order->grand_total) }}</span>
        </div>
    </div>

    <div class="footer">
        Thank you for your business!
    </div>
</div>
</body>
</html>
