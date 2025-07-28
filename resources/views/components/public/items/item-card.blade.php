{{--
    Modern Item Card Component
    - Root element is a `div` with `group` to allow for multiple interactive elements inside.
    - The main image is the primary link to the item's detail page.
    - An "Add to Cart" button appears on hover for a quick action.
    - The layout uses flexbox for better alignment of text and price.
--}}
@props(['item'])

<div class="group relative block">
    {{-- Main clickable area for image --}}
    <a href="{{ route('public.items.show', $item) }}">
        <div class="relative h-72 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700 group-hover:opacity-75 transition-opacity">
            <img src="{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : '/images/placeholder.svg' }}"
                 alt="{{ $item->images->first()?->alt_text ?? $item->name }}"
                 class="h-full w-full object-cover object-center transition-transform duration-300 ease-in-out group-hover:scale-105"
                 loading="lazy"> {{-- Added lazy loading for performance --}}
        </div>
    </a>

    {{-- Quick action button, appears on hover --}}
    <div class="absolute inset-x-0 bottom-24 flex justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <form action="{{ route('cart.add', $item) }}" method="POST">
            @csrf
            <button type="submit" class="rounded-md bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-900 dark:text-white shadow-md hover:bg-gray-50 dark:hover:bg-gray-700">
                {{ __('Add to Cart') }}
            </button>
        </form>
    </div>

    {{-- Item details section --}}
    <div class="relative mt-4">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-sm text-gray-700 dark:text-gray-300">
                    <a href="{{ route('public.items.show', $item) }}">
                        {{-- This span makes the entire text block clickable, which is a common accessible pattern --}}
                        <span aria-hidden="true" class="absolute inset-0"></span>
                        {{ $item->name }}
                    </a>
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->tenant->name }}</p>
            </div>
            @if($item->sale_price)
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->sale_price, 2, ',', '.') }} €</p>
            @endif
        </div>
    </div>
</div>