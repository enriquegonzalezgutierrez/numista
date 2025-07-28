<x-mail::message>
# ¡Gracias por tu pedido!

Hola {{ $order->customer->name }},

Hemos recibido tu pedido **#{{ $order->order_number }}** y ya lo estamos preparando.

## Resumen del Pedido

<x-mail::table>
| Producto | Cantidad | Precio |
|:---------|:--------:|-------:|
@foreach($order->items as $item)
| {{ $item->item->name }} | {{ $item->quantity }} | {{ number_format($item->price, 2, ',', '.') }} € |
@endforeach
</x-mail::table>

**Total: {{ number_format($order->total_amount, 2, ',', '.') }} €**

Puedes ver los detalles completos de tu pedido en tu cuenta.

<x-mail::button :url="route('orders.show', $order)">
Ver Mi Pedido
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>