<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 0; background: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f5f5; padding: 24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border: 1px solid #e5e5e5;">
                    <tr>
                        <td style="padding: 24px; text-align: center; border-bottom: 1px solid #eee;">
                            <h1 style="margin: 0; font-size: 22px; color: #790101;">Khut Shop</h1>
                            <p style="margin: 8px 0 0; color: #666;">Order Confirmation</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 24px;">
                            <p>Dear {{ $order->first_name }} {{ $order->last_name }},</p>

                            @if($order->payment_method === 'cod')
                                <p>Thank you for your order. Your order has been placed successfully and will be paid on delivery.</p>
                            @else
                                <p>Thank you for your purchase. Your payment was successful and your order has been confirmed.</p>
                            @endif

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; background: #f9f9f9; border: 1px solid #eee;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <p style="margin: 0 0 8px;"><strong>Order ID:</strong> #{{ $order->id }}</p>
                                        @if($order->transaction_id)
                                            <p style="margin: 0 0 8px;"><strong>Transaction ID:</strong> {{ $order->transaction_id }}</p>
                                        @endif
                                        <p style="margin: 0 0 8px;"><strong>Payment Method:</strong>
                                            @if($order->payment_method === 'cod')
                                                Cash on Delivery
                                            @elseif($order->payment_method === 'card')
                                                Card / Mobile Banking
                                            @else
                                                {{ ucfirst($order->payment_method) }}
                                            @endif
                                        </p>
                                        <p style="margin: 0 0 8px;"><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                                        <p style="margin: 0;"><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 24px 0 12px; font-size: 16px;">Order Items</h3>
                            <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; border: 1px solid #ddd; font-size: 14px;">
                                <thead>
                                    <tr style="background: #f2f2f2;">
                                        <th align="left" style="border: 1px solid #ddd;">Product</th>
                                        <th align="left" style="border: 1px solid #ddd;">Size</th>
                                        <th align="left" style="border: 1px solid #ddd;">Color</th>
                                        <th align="center" style="border: 1px solid #ddd;">Qty</th>
                                        <th align="right" style="border: 1px solid #ddd;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td style="border: 1px solid #ddd;">{{ $item->product_name }}</td>
                                            <td style="border: 1px solid #ddd;">{{ $item->size ?? '-' }}</td>
                                            <td style="border: 1px solid #ddd;">{{ $item->color ?? '-' }}</td>
                                            <td align="center" style="border: 1px solid #ddd;">{{ $item->quantity }}</td>
                                            <td align="right" style="border: 1px solid #ddd;">৳{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" align="right" style="border: 1px solid #ddd;"><strong>Subtotal</strong></td>
                                        <td align="right" style="border: 1px solid #ddd;">৳{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="right" style="border: 1px solid #ddd;"><strong>Delivery</strong></td>
                                        <td align="right" style="border: 1px solid #ddd;">৳{{ number_format($order->delivery_charge, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="right" style="border: 1px solid #ddd;"><strong>Total</strong></td>
                                        <td align="right" style="border: 1px solid #ddd;"><strong>৳{{ number_format($order->total, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <h3 style="margin: 24px 0 12px; font-size: 16px;">Shipping Address</h3>
                            <p style="margin: 0;">
                                {{ $order->address }}@if($order->apartment), {{ $order->apartment }}@endif<br>
                                {{ $order->city }}, {{ $order->district }} {{ $order->postcode }}<br>
                                {{ $order->country ?? 'Bangladesh' }}<br>
                                Phone: {{ $order->phone }}
                            </p>

                            @if($order->notes)
                                <p style="margin-top: 16px;"><strong>Order Note:</strong> {{ $order->notes }}</p>
                            @endif

                            <p style="margin-top: 24px;">
                                <a href="{{ route('customer.order.details', $order->id) }}" style="display: inline-block; background: #790101; color: #fff; text-decoration: none; padding: 10px 18px;">
                                    View Order Details
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 24px; background: #fafafa; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #888;">
                            Thank you for shopping with Khut Shop.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
