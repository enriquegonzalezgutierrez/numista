@props(['collection'])

<div class="group relative">
    <div class="relative h-80 w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:opacity-75 sm:h-64">
        @php
            $imageUrl = $collection->image 
                ? $collection->image->url
                : '/images/collection-placeholder.svg'; // A generic placeholder
        @endphp
        <img src="{{ $imageUrl }}" 
             alt="{{ $collection->name }}" 
             class="h-full w-full object-cover object-center">
    </div>
    <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
        <a href="{{ route('public.items.index', ['collections' => [$collection->id]]) }}">
            <span class="absolute inset-0"></span>
            {{ $collection->name }}
        </a>
    </h3>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $collection->items_count }} {{ trans_choice('public.item', $collection->items_count) }}</p>
</div>