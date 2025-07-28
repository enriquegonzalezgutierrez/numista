@extends('layouts.account')

@section('title', __('public.confirm_address_delete'))

@section('account-content')
<div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
    <h2 class="text-2xl font-semibold mb-4">{{ __('public.confirm_address_delete') }}</h2>

    <p class="text-gray-600 dark:text-gray-400 mb-6">
        Estás a punto de eliminar permanentemente la siguiente dirección:
    </p>

    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="font-bold text-lg">{{ $address->label }}</h3>
        <address class="mt-2 not-italic text-sm text-gray-600 dark:text-gray-300">
            {{ $address->recipient_name }}<br>
            {{ $address->street_address }}<br>
            {{ $address->postal_code }} {{ $address->city }}, {{ $address->state }}<br>
            {{ $address->country_code }}
        </address>
    </div>

    <form action="{{ route('my-account.addresses.destroy', $address) }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="flex justify-end space-x-4">
            <a href="{{ route('my-account.addresses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">{{ __('public.cancel') }}</a>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">{{ __('public.delete') }}</button>
        </div>
    </form>
</div>
@endsection