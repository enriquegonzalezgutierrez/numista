@extends('layouts.public')

@section('title', __('Checkout'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">{{ __('Checkout') }}</h1>
    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
            <section class="lg:col-span-7 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Shipping Information') }}</h2>
                <div class="mt-4">
                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Shipping Address') }}</label>
                    
                    {{-- THE FIX: Added padding (px-3 py-2) and dark mode styles --}}
                    <textarea 
                        id="shipping_address" 
                        name="shipping_address" 
                        rows="4" 
                        required 
                        class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                    >{{ old('shipping_address', $user->customer?->shipping_address) }}</textarea>
                </div>
            </section>

            {{-- Order Summary --}}
            <section class="lg:col-span-5 mt-8 lg:mt-0 rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm h-fit sticky top-8">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Order Summary') }}</h2>
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                    @foreach($items as $item)
                        <li class="flex py-4 space-x-4">
                            <img src="{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : '/images/placeholder.svg' }}" alt="{{ $item->name }}" class="h-16 w-16 rounded-md object-cover">
                            <div class="flex-auto text-sm">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</h3>
                                <p class="text-gray-500 dark:text-gray-400">{{ $cart[$item->id]['quantity'] }} x {{ number_format($item->sale_price, 2, ',', '.') }} €</p>
                            </div>
                            <p class="flex-none text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->sale_price * $cart[$item->id]['quantity'], 2, ',', '.') }} €</p>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-6 space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-base font-medium text-gray-900 dark:text-white">{{ __('Total') }}</dt>
                        <dd class="text-base font-medium text-gray-900 dark:text-white">{{ number_format($total, 2, ',', '.') }} €</dd>
                    </div>
                </dl>
                <div class="mt-6">
                    <button type="submit" class="w-full flex items-center justify-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700">{{ __('Place Order') }}</button>
                </div>
            </section>
        </div>
    </form>
</div>
@endsection