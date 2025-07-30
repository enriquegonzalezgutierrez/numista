@foreach($items as $item)
    {{-- THE FIX: Update component tag --}}
    <x-public.item-card :item="$item" />
@endforeach