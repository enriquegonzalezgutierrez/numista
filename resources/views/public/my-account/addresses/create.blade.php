@extends('layouts.account')

@section('title', __('public.add_new_address'))

@section('account-content')
<div class="p-6 text-gray-900 dark:text-gray-100">
    <h2 class="text-2xl font-semibold mb-6">{{ __('public.add_new_address') }}</h2>

    <form action="{{ route('my-account.addresses.store') }}" method="POST">
        @csrf
        @include('public.my-account.addresses.partials.form-fields', ['address' => new \Numista\Collection\Domain\Models\Address(), 'countries' => $countries])
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('my-account.addresses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">{{ __('public.cancel') }}</a>
            <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">{{ __('public.save_address') }}</button>
        </div>
    </form>
</div>
@endsection