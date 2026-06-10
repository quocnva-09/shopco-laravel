@component('mail::message')
# Order #{{ $order->id }} — Status Update

Hi there! We wanted to let you know that the status of your order has been updated.

**New status:** {{ ucfirst($order->status instanceof \App\Enums\OrderStatus ? $order->status->value : $order->status) }}

@component('mail::table')
| # | Product Name | Qty | Unit Price | Subtotal |
|:-:|:-------------|:---:|----------:|---------:|
@foreach($order->orderItems as $item)
| {{ $loop->iteration }} | {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} | ${{ number_format($item->totalMoney, 2) }} |
@endforeach
@endcomponent

**Total amount:** ${{ number_format($order->totalAmount, 2) }}

If you have any questions about your order, feel free to contact our support team.

Thanks for shopping with us!

Regards,<br>
{{ config('app.name') }}
@endcomponent
