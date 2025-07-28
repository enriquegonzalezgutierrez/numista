@extends('layouts.public')

@section('title', __('Shopping Cart'))

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('Your Cart') }}</h1>

    <div class="mt-12 lg:grid lg:grid-cols-12 lg:items-start lg:gap-x-12">
        
        <section aria-labelledby="cart-heading" class="lg:col-span-7">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <h2 id="cart-heading" class="sr-only">{{ __('public.cart.sr_heading') }}</h2>
                
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <x-public.cart.cart-item :item="$item" :quantity="$cart[$item->id]['quantity']" />
                    @empty
                        <li class="text-center py-16 px-6">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">{{ __('Your cart is empty.') }}</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </section>

        {{-- Order summary --}}
        @if(count($cart) > 0)
        <section aria-labelledby="summary-heading" class="mt-16 rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-6 sm:p-6 lg:col-span-5 lg:mt-0 lg:p-8 lg:sticky lg:top-24">
            <h2 id="summary-heading" class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Order summary') }}</h2>

            <dl class="mt-6 space-y-4">
                <div class="flex items-center justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('Subtotal') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($total, 2, ',', '.') }} €</dd>
                </div>
                <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                    <dt class="text-base font-medium text-gray-900 dark:text-white">{{ __('Total') }}</dt>
                    <dd class="text-base font-medium text-gray-900 dark:text-white">{{ number_format($total, 2, ',', '.') }} €</dd>
                </div>
            </dl>

            <div class="mt-6">
                <a href="{{ route('checkout.create') }}" class="w-full flex items-center justify-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700">
                    {{ __('Proceed to Checkout') }}
                </a>
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