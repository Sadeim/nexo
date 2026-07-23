<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - Nexo Barbers</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #222; background: #f7f7f7; margin: 0; padding: 24px;">
    <div style="max-width: 520px; margin: 0 auto; background: #fff; padding: 28px; border-radius: 8px;">
        <div style="text-align: center; border-bottom: 1px dashed #ccc; padding-bottom: 16px; margin-bottom: 16px;">
            <h1 style="margin: 0; color: #111; font-size: 22px;">NEXO BARBERS</h1>
            <div style="color: #666; font-size: 12px; margin-top: 4px;">Receipt #{{ $order->order_number }}</div>
            <div style="color: #666; font-size: 12px;">{{ $order->created_at->format('D, M j, Y — g:i A') }}</div>
        </div>

        <p style="margin: 0 0 16px 0; font-size: 14px;">
            Thanks for stopping by. Here is your receipt.
        </p>

        @if ($order->employee)
            <div style="font-size: 13px; color: #444; margin-bottom: 12px;">
                Served by: <strong>{{ $order->employee->name }}</strong>
            </div>
        @endif

        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 6px 0; border-bottom: 1px solid #eee;">Item</th>
                    <th style="text-align: center; padding: 6px 0; border-bottom: 1px solid #eee;">Qty</th>
                    <th style="text-align: right; padding: 6px 0; border-bottom: 1px solid #eee;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td style="padding: 6px 0; border-bottom: 1px solid #f2f2f2;">{{ $item->name }}</td>
                    <td style="text-align: center; padding: 6px 0; border-bottom: 1px solid #f2f2f2;">{{ $item->quantity }}</td>
                    <td style="text-align: right; padding: 6px 0; border-bottom: 1px solid #f2f2f2;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 12px;">
            <tr>
                <td style="padding: 4px 0; color: #555;">Subtotal</td>
                <td style="padding: 4px 0; text-align: right;">${{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if ((float) $order->tip > 0)
            <tr>
                <td style="padding: 4px 0; color: #555;">Tip</td>
                <td style="padding: 4px 0; text-align: right;">${{ number_format($order->tip, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 8px 0 0 0; font-weight: bold; border-top: 1px solid #ccc;">Total</td>
                <td style="padding: 8px 0 0 0; font-weight: bold; text-align: right; border-top: 1px solid #ccc;">${{ number_format($order->total, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: #555;">Payment</td>
                <td style="padding: 4px 0; text-align: right; text-transform: uppercase;">{{ $order->payment_method }}</td>
            </tr>
        </table>

        <p style="text-align: center; color: #888; font-size: 12px; margin-top: 24px;">
            Thank you for choosing Nexo Barbers.
        </p>
    </div>
</body>
</html>
