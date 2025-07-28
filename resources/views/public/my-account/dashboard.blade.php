@extends('layouts.account')

@section('title', __('public.dashboard'))

@section('account-content')
<div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
    
    <h2 class="text-2xl font-semibold mb-2">{{ __('public.hello_user', ['name' => $user->name]) }}</h2>
    <p class="text-gray-600 dark:text-gray-400">{{ __('public.dashboard_welcome') }}</p>
</div>
@endsection