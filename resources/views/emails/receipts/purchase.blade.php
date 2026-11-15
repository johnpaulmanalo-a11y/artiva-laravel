<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artiva purchase receipt</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f4f3ef;color:#242321;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:28px 32px;background:#202321;color:#fff;">
                <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#b8d5d0;">Artiva</div>
                <h1 style="margin:10px 0 0;font-size:26px;line-height:1.25;">Your purchase receipt</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:28px 32px;">
                <p style="margin:0 0 8px;">Hello {{ $receipt->order->user->name }},</p>
                <p style="margin:0 0 22px;color:#5e625f;line-height:1.6;">Thank you for supporting Artiva artists. Your payment has been verified and this is your official receipt.</p>

                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:14px;">
                    <tr><td style="padding:8px 0;color:#666;">Receipt number</td><td style="padding:8px 0;text-align:right;font-weight:bold;">{{ $receipt->receipt_number }}</td></tr>
                    <tr><td style="padding:8px 0;color:#666;">Order ID</td><td style="padding:8px 0;text-align:right;">{{ $receipt->order->order_number }}</td></tr>
                    <tr><td style="padding:8px 0;color:#666;">Transaction reference</td><td style="padding:8px 0;text-align:right;">{{ $receipt->payment->provider_reference }}</td></tr>
                    <tr><td style="padding:8px 0;color:#666;">Customer email</td><td style="padding:8px 0;text-align:right;">{{ $receipt->order->user->email }}</td></tr>
                    <tr><td style="padding:8px 0;color:#666;">Payment method</td><td style="padding:8px 0;text-align:right;">{{ $receipt->payment->method === 'card' ? 'Card' : ucfirst($receipt->payment->method) }}</td></tr>
                    <tr><td style="padding:8px 0;color:#666;">Verified on</td><td style="padding:8px 0;text-align:right;">{{ $receipt->payment->verified_at->format('j M Y, g:i a') }}</td></tr>
                </table>

                <h2 style="margin:26px 0 10px;font-size:16px;">Transaction summary</h2>
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:14px;">
                    @foreach ($receipt->order->items as $item)
                        <tr>
                            <td style="padding:10px 0;border-bottom:1px solid #ecebe7;">{{ $item->title }} <span style="color:#777;">&times; {{ $item->quantity }}</span></td>
                            <td style="padding:10px 0;border-bottom:1px solid #ecebe7;text-align:right;">{{ money($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                    <tr><td style="padding:10px 0;color:#666;">Subtotal</td><td style="padding:10px 0;text-align:right;">{{ money($receipt->order->subtotal) }}</td></tr>
                    <tr><td style="padding:10px 0;color:#666;">Tax</td><td style="padding:10px 0;text-align:right;">{{ money($receipt->order->tax) }}</td></tr>
                    <tr><td style="padding:14px 0;border-top:1px solid #d8d6cf;font-weight:bold;">Total paid · PAID / VERIFIED</td><td style="padding:14px 0;border-top:1px solid #d8d6cf;text-align:right;font-weight:bold;">{{ money($receipt->order->total) }}</td></tr>
                </table>

                <p style="margin:24px 0 0;color:#5e625f;line-height:1.6;">We’re grateful you chose Artiva. Keep this email for your records.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:18px 32px;background:#f8f7f4;color:#777;font-size:12px;">Artiva · Art that Speaks to You</td>
        </tr>
    </table>
</body>
</html>
