@if($items->isNotEmpty())
    {{-- Loop through and display each item --}}
    @foreach ($items as $item)
        <x-public.item-card :item="$item" />
    @endforeach
@else
    {{-- This is the message that will be displayed when no items are found --}}
    <div class="lg:col-span-3"> {{-- This ensures the message takes up the full width of the grid area --}}
        <div class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('public.no_items_found_filtered') }}</p>
            <a href="{{ route('public.items.index') }}" class="mt-4 inline-block rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">{{ __('public.clear_filters_link') }}</a>
        </div>
    </div>
@endif