@extends('layouts.public')

@section('title', __('public.shopping_cart'))

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('public.your_cart') }}</h1>

    {{-- THE FIX: Add a section to display warnings if the cart was auto-updated. --}}
    @if(!empty($warnings))
        <div class="mt-8 rounded-md bg-yellow-50 dark:bg-yellow-500/10 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">¡Atención! Tu carrito ha sido actualizado.</h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <ul role="list" class="list-disc space-y-1 pl-5">
                            @foreach($warnings as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-8 lg:grid lg:grid-cols-12 lg:items-start lg:gap-x-12">
        <section aria-labelledby="cart-heading" class="lg:col-span-7">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <h2 id="cart-heading" class="sr-only">{{ __('public.cart.sr_heading') }}</h2>
                
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <x-public.cart-item :item="$item" :quantity="$cart[$item->id]['quantity']" />
                    @empty
                        <li class="text-center py-16 px-6">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">{{ __('public.cart_empty') }}</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </section>

        @if(count($cart) > 0)
        <section aria-labelledby="summary-heading" class="mt-16 rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-6 sm:p-6 lg:col-span-5 lg:mt-0 lg:p-8 lg:sticky lg:top-24">
            <h2 id="summary-heading" class="text-lg font-medium text-gray-900 dark:text-white">{{ __('public.order_summary') }}</h2>
            <dl class="mt-6 space-y-4">
                <div class="flex items-center justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('public.subtotal') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($total, 2, ',', '.') }} €</dd>
                </div>
                <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                    <dt class="text-base font-medium text-gray-900 dark:text-white">{{ __('panel.Total') }}</dt>
                    <dd class="text-base font-medium text-gray-900 dark:text-white">{{ number_format($total, 2, ',', '.') }} €</dd>
                </div>
            </dl>
            <div class="mt-6">
                <a href="{{ route('checkout.create') }}" class="w-full flex items-center justify-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700">
                    {{ __('public.proceed_to_checkout') }}
                </a>
            </div>
            <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                <p>
                    {{ __('public.or') }}
                    <a href="{{ route('public.items.index') }}" class="font-medium text-teal-600 hover:text-teal-500">
                        {{ __('public.continue_shopping') }}
                        <span aria-hidden="true"> →</span>
                    </a>
                </p>
            </div>
        </section>
        @endif
    </div>
</div>
@endsection