@component('mail::message')
# Order #{{ $order->id }} Confirmation

Thank you for your purchase! Here are the items you ordered:

@component('mail::table')
| # | Product name | Quantity | Unit price | Subtotal |
|:-:|:-------------|:--------:|:----------:|---------:|
@foreach($order->orderItems as $item)
    | {{ $loop->iteration }} | {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
    ${{ number_format($item->totalMoney, 2) }} |
@endforeach
@endcomponent

**Order status:** {{ $order->status }}

**Total amount:** ${{ number_format($order->totalAmount, 2) }}

Thanks for shopping with us!

Regards,<br>
{{ config('app.name') }}
@endcomponent