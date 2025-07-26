@props(['item', 'quantity'])

<li class="flex py-6 px-6">
    <div class="flex-shrink-0">
        <img src="{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : '/images/placeholder.svg' }}" alt="{{ $item->name }}" class="w-24 h-24 sm:w-32 sm:h-32 rounded-md object-cover">
    </div>

    <div class="ml-4 flex flex-1 flex-col sm:ml-6">
        <div class="flex justify-between">
            <div class="pr-6">
                <h3 class="text-base font-semibold">
                    <a href="{{ route('public.items.show', $item) }}" class="text-gray-800 hover:text-teal-600 dark:text-gray-200 dark:hover:text-teal-400">{{ $item->name }}</a>
                </h3>
                <p class="mt-1 text-sm text-green-600">En stock</p>
            </div>
            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ number_format($item->sale_price * $quantity, 2, ',', '.') }} €</p>
        </div>

        <div class="mt-4 flex items-center space-x-4 text-sm">
            {{-- THE FIX: Custom Quantity Stepper Component --}}
            <form action="{{ route('cart.update', $item) }}" method="POST"
                  x-data="{ quantity: {{ $quantity }} }"
                  x-ref="updateForm">
                @csrf
                @method('PATCH')
                
                <label for="quantity-{{$item->id}}" class="sr-only">Quantity</label>
                <div class="flex items-center rounded-md border border-gray-300">
                    <button
                        type="button"
                        @click="if (quantity > 1) { quantity--; $nextTick(() => $refs.updateForm.submit()) }"
                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-l-md"
                    >
                        -
                    </button>

                    <input type="text" name="quantity" :value="quantity" class="w-12 border-x border-y-0 text-center text-sm" readonly>
                    
                    <button
                        type="button"
                        @click="quantity++; $nextTick(() => $refs.updateForm.submit())"
                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-r-md"
                    >
                        +
                    </button>
                </div>
            </form>
            
            <div class="border-l border-gray-300 dark:border-gray-600 h-6"></div>

            <form action="{{ route('cart.remove', $item) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="font-medium text-red-600 hover:text-red-500 dark:text-red-500 dark:hover:text-red-400">{{ __('Remove') }}</button>
            </form>
        </div>
    </div>
</li>