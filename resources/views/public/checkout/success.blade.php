@extends('layouts.public')

@section('title', __('Thank You!'))

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <h1 class="text-3xl font-bold text-green-600">{{ __('Thank You!') }}</h1>
    <p class="mt-4 text-lg text-gray-700">{{ __('Your order has been placed successfully.') }}</p>
    <p class="mt-2 text-gray-500">{{ __('Your order number is:') }} <span class="font-semibold text-gray-800">{{ $order->order_number }}</span></p>
    <div class="mt-8">
        {{-- THE FIX: Changed route from 'home' to 'my-account' --}}
        <a href="{{ route('my-account') }}" class="inline-block bg-teal-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-teal-700">{{ __('View Your Orders') }}</a>
    </div>
</div>
@endsection