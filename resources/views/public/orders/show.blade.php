@extends('layouts.public')

@section('title', __('public.order_details') . ' #' . $order->order_number)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 md:p-8 border-b border-gray-200 dark:border-gray-700">
                <div class="md:flex md:justify-between md:items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('public.order_details') }}</h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('panel.label_order') }} <span class="font-medium text-gray-700 dark:text-gray-300">#{{ $order->order_number }}</span>
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('public.placed_on') }} {{ $order->created_at->translatedFormat('F j, Y') }}
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0 text-left md:text-right">
                        <span @class([
                            'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                            'bg-yellow-100 text-yellow-800' => $order->status === 'pending',
                            'bg-blue-100 text-blue-800' => $order->status === 'paid',
                            'bg-cyan-100 text-cyan-800' => $order->status === 'shipped',
                            'bg-green-100 text-green-800' => $order->status === 'completed',
                            'bg-red-100 text-red-800' => $order->status === 'cancelled',
                            'bg-gray-100 text-gray-800' => !in_array($order->status, ['pending', 'paid', 'shipped', 'completed', 'cancelled']),
                        ])>
                            {{ __("item.status_{$order->status}") }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="p-6 md:p-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('public.items_ordered') }}</h3>
                <div class="flow-root">
                    <ul role="list" class="-my-6 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($order->items as $orderItem)
                        <li class="flex items-start py-6 space-x-4">
                            {{-- Image Container --}}
                            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-gray-700">
                                <img src="{{ $orderItem->item->images->first() ? route('public.images.show', ['image' => $orderItem->item->images->first()->id]) : '/images/placeholder.svg' }}" alt="{{ $orderItem->item->name }}" class="h-full w-full object-cover object-center">
                            </div>

                            {{-- Text Container --}}
                            <div class="ml-4 flex flex-1 flex-col">
                                <div>
                                    <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                        <h3>
                                            <a href="{{ route('public.items.show', $orderItem->item) }}" class="hover:underline">{{ $orderItem->item->name }}</a>
                                        </h3>
                                        <p class="ml-4">{{ number_format($orderItem->price * $orderItem->quantity, 2, ',', '.') }} €</p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($orderItem->price, 2, ',', '.') }} € x {{ $orderItem->quantity }}
                                    </p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <dl class="space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-base font-medium text-gray-900 dark:text-white">{{ __('panel.Total') }}</dt>
                        <dd class="ml-4 text-base font-medium text-gray-900 dark:text-white">{{ number_format($order->total_amount, 2, ',', '.') }} €</dd>
                    </div>
                </dl>

                <div class="mt-8">
                    <a href="{{ route('my-account.orders') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">
                        ← {{ __('public.back_to_my_orders') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection