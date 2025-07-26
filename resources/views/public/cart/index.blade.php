@extends('layouts.public')

@section('title', __('Shopping Cart'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
        
        {{-- Main Content: Cart Items (takes 8 of 12 columns on large screens) --}}
        <section class="lg:col-span-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="flex justify-between items-baseline border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Your Cart') }}</h1>
                    <span class="text-sm font-medium text-gray-500">{{ __('Price') }}</span>
                </div>

                @if(session('success'))
                    <div class="px-6 pt-4">
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <x-public.cart.cart-item :item="$item" :quantity="$cart[$item->id]['quantity']" />
                    @empty
                        <li class="text-center py-16 px-6">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">{{ __('Your cart is empty.') }}</p>
                        </li>
                    @endforelse
                </ul>

                @if(count($cart) > 0)
                    <div class="border-t border-gray-200 dark:border-gray-700 p-6 text-right">
                         <p class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('Subtotal') }} ({{ count($cart) }} {{ trans_choice('item', count($cart)) }}): <span class="font-bold">{{ number_format($total, 2, ',', '.') }} €</span>
                        </p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Order Summary Sidebar (takes 4 of 12 columns on large screens) --}}
        @if(count($cart) > 0)
        <section class="lg:col-span-4 mt-8 lg:mt-0 rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm h-fit sticky top-8">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Order summary') }}</h2>
            <dl class="mt-6 space-y-4">
                <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                    <dt class="text-base font-medium text-gray-900 dark:text-white">{{ __('Total') }}</dt>
                    <dd class="text-base font-medium text-gray-900 dark:text-white">{{ number_format($total, 2, ',', '.') }} €</dd>
                </div>
            </dl>
            <div class="mt-6">
                <a href="#" class="w-full flex items-center justify-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700">{{ __('Proceed to Checkout') }}</a>
            </div>
            <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                <p>
                    {{ __('or') }}
                    <a href="{{ route('public.items.index') }}" class="font-medium text-teal-600 hover:text-teal-500">
                        {{ __('Continue Shopping') }}
                        <span aria-hidden="true"> →</span>
                    </a>
                </p>
            </div>
        </section>
        @endif
    </div>
</div>
@endsection