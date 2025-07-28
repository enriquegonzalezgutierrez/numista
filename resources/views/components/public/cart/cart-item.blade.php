@props(['item', 'quantity'])

<li class="flex py-6 px-4 sm:px-6">
    
    <div class="flex-shrink-0">
        <img src="{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : '/images/placeholder.svg' }}" alt="{{ $item->name }}" class="w-24 h-24 sm:w-32 sm:h-32 rounded-md object-cover object-center">
    </div>

    
    <div class="ml-4 flex flex-1 flex-col justify-between sm:ml-6">
        <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
            <div>
                <div class="flex justify-between">
                    <h3 class="text-sm">
                        <a href="{{ route('public.items.show', $item) }}" class="font-medium text-gray-800 hover:text-teal-600 dark:text-gray-200 dark:hover:text-teal-400">{{ $item->name }}</a>
                    </h3>
                </div>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->sale_price, 2, ',', '.') }} €</p>
            </div>

            <div class="mt-4 sm:mt-0 sm:pr-9">
                
                <form action="{{ route('cart.update', $item) }}" method="POST"
                      x-data="{ quantity: {{ $quantity }} }"
                      x-ref="updateForm" class="flex items-center">
                    @csrf
                    @method('PATCH')
                    
                    <label for="quantity-{{$item->id}}" class="sr-only">{{ __('public.quantity') }}, {{ $item->name }}</label>
                    <div class="flex items-center rounded-md border border-gray-300 dark:border-gray-600">
                        <button
                            type="button"
                            @click="if (quantity > 1) { quantity--; $nextTick(() => $refs.updateForm.submit()) }"
                            class="px-2 py-1 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-l-md transition"
                            aria-label="{{ __('public.cart.decrease_quantity') }}"
                        >
                            −
                        </button>

                        <input type="text" name="quantity" id="quantity-{{$item->id}}" :value="quantity" class="w-12 border-x border-y-0 text-center text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        
                        <button
                            type="button"
                            @click="quantity++; $nextTick(() => $refs.updateForm.submit())"
                            class="px-2 py-1 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-r-md transition"
                            aria-label="{{ __('public.cart.increase_quantity') }}"
                        >
                            +
                        </button>
                    </div>
                </form>

                
                <div class="absolute top-0 right-0">
                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="-m-2 inline-flex p-2 text-gray-400 hover:text-gray-500">
                            <span class="sr-only">{{ __('public.remove') }}</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <p class="mt-4 flex space-x-2 text-sm text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 flex-shrink-0 text-green-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.052-.143z" clip-rule="evenodd" /></svg>
            <span>{{ __('public.cart.in_stock') }}</span>
        </p>
    </div>
</li>