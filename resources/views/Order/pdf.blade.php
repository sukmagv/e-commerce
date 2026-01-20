<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>INVOICE</title>
</head>
<body>
    <p>Order {{ $order->code }}</p>
    <p>Name: {{ $order->user->name }}</p>
    <p>Status: {{ $order->status }}</p>

    <table border="1" cellpadding="5">
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
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ number_format($item->normal_price) }}</td>
                <td>{{ number_format($item->total_price) }}</td>
                <td>{{ $item->discount ? $item->discount_price : 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p>Sub Total: {{ number_format($order->sub_total) }}</p>
    <p>Tax : {{ number_format($order->tax_amount) }}</p>
    <p>Grand Total: {{ number_format($order->grand_total) }}</p>

</body>
</html>
