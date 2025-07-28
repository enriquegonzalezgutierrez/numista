@extends('layouts.account')

@section('title', __('Dashboard'))

@section('account-content')
<div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
    {{-- THE FIX: Use translation keys --}}
    <h2 class="text-2xl font-semibold mb-2">{{ __('Hello, :name!', ['name' => $user->name]) }}</h2>
    <p class="text-gray-600 dark:text-gray-400">{{ __('Welcome to your dashboard. From here you can manage your orders, addresses, and account details.') }}</p>
</div>
@endsection