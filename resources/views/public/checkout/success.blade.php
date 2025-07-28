@extends('layouts.public')

@section('title', __('public.thank_you'))

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <h1 class="text-3xl font-bold text-green-600">{{ __('public.thank_you') }}</h1>
    <p class="mt-4 text-lg text-gray-700">{{ __('public.order_placed_successfully') }}</p>
    <p class="mt-2 text-gray-500">{{ __('public.your_order_number_is') }} <span class="font-semibold text-gray-800">{{ $order->order_number }}</span></p>
    <div class="mt-8">
        
        <a href="{{ route('my-account.orders') }}" class="inline-block bg-teal-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-teal-700">{{ __('public.view_your_orders') }}</a>
    </div>
</div>
@endsection