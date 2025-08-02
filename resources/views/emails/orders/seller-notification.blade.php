<x-mail::message>
# {{ __('mail.seller_notification_title') }}

{{ __('mail.seller_notification_intro', ['appName' => config('app.name')]) }}

---

### {{ __('mail.seller_notification_order_details') }}

<x-mail::panel>
**{{ __('mail.seller_notification_order_number') }}:** {{ $order->order_number }}<br>
**{{ __('mail.seller_notification_date') }}:** {{ $order->created_at->format('d/m/Y H:i') }}<br>
**{{ __('mail.seller_notification_total_order') }}:** {{ number_format($order->total_amount, 2, ',', '.') }} €
</x-mail::panel>

### {{ __('mail.seller_notification_items_sold') }}

<x-mail::table>
| {{ __('mail.product') }} | {{ __('mail.quantity') }} | {{ __('mail.price') }} |
|:-------------------------|:----------------:|--------------:|
@foreach($order->items as $item)
| {{ $item->item->name }} | {{ $item->quantity }} | {{ number_format($item->price, 2, ',', '.') }} € |
@endforeach
</x-mail::table>

### {{ __('mail.seller_notification_customer') }}

<x-mail::panel>
**{{ __('panel.field_name') }}:** {{ $order->customer->name }}<br>
**{{ __('panel.field_email') }}:** {{ $order->customer->email }}
</x-mail::panel>

### {{ __('mail.seller_notification_shipping_address') }}

<x-mail::panel>
{{-- La dirección de envío es un dato dinámico, por lo que no necesita traducción --}}
{!! nl2br(e($order->shipping_address)) !!}
</x-mail::panel>

{{ __('mail.seller_notification_manage_order') }}

{{-- Este botón ahora enlaza directamente a la vista del pedido en el panel de Filament --}}
<x-mail::button :url="route('filament.admin.resources.orders.view', ['record' => $order, 'tenant' => $order->tenant])">
{{ __('mail.seller_notification_cta') }}
</x-mail::button>

{{ __('mail.regards') }},<br>
{{ __('mail.seller_notification_team', ['appName' => config('app.name')]) }}
</x-mail::message>