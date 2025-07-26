@props(['item'])

<a href="{{ route('public.items.show', $item) }}" class="group block">
    <div class="relative">
        <div class="relative h-72 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
            {{-- This checks if the relationship exists and has items before trying to access the first one --}}
            @if($item->images && $item->images->isNotEmpty())
                <img src="{{ route('public.images.show', ['image' => $item->images->first()->id]) }}" 
                     alt="{{ $item->images->first()->alt_text ?? $item->name }}" 
                     class="h-full w-full object-cover object-center transition-transform duration-300 ease-in-out group-hover:scale-105">
            @else
                {{-- This now points to a reliable public path --}}
                <img src="{{ asset('images/placeholder.svg') }}" 
                     alt="No image available" 
                     class="h-full w-full object-cover object-center">
            @endif
            <div class="absolute inset-x-0 bottom-0 h-36 bg-gradient-to-t from-black opacity-50" aria-hidden="true"></div>
            @if($item->sale_price)
                <p class="absolute bottom-4 right-4 text-lg font-semibold text-white">{{ number_format($item->sale_price, 2, ',', '.') }} €</p>
            @endif
        </div>
        <div class="relative mt-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $item->name }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->tenant->name }}</p>
        </div>
    </div>
</a>