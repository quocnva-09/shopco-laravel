<x-mail::message>
# Verify Your Order

Hello {{ $order->guest_name ?? 'Guest' }},

Thank you for your order! To complete your checkout process, please use the following One-Time Password (OTP) to verify your order:

<x-mail::panel>
**{{ $otp }}**
</x-mail::panel>

This OTP is valid for exactly 5 minutes.

**Order Details:**
- Order ID: #{{ $order->id }}
- Total Amount: ${{ number_format($order->totalAmount, 2) }}

If you did not make this order, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
