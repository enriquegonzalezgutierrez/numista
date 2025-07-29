@foreach($items as $item)
    <x-public.items.item-card :item="$item" />
@endforeach