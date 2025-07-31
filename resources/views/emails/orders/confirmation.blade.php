<x-mail::message>
# {{ __('mail.order_confirmation_title') }}

{{ __('mail.hello') }} {{ $order->customer->name }},

{{ __('mail.order_confirmation_intro', ['orderNumber' => $order->order_number]) }}

## {{ __('mail.order_summary') }}

<x-mail::table>
| {{ __('mail.product') }} | {{ __('mail.quantity') }} | {{ __('mail.price') }} |
|:---------|:--------:|-------:|
@foreach($order->items as $item)
| {{ $item->item->name }} | {{ $item->quantity }} | {{ number_format($item->price, 2, ',', '.') }} € |
@endforeach
</x-mail::table>

**{{ __('mail.total') }}: {{ number_format($order->total_amount, 2, ',', '.') }} €**

{{ __('mail.order_confirmation_cta') }}

<x-mail::button :url="route('orders.show', $order)">
{{ __('mail.view_order') }}
</x-mail::button>

{{ __('mail.thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>