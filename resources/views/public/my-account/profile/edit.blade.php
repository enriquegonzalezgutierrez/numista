@extends('layouts.account')

@section('title', __('public.account_details'))

@section('account-content')
<div class="divide-y divide-gray-200 dark:divide-gray-700">
    <div class="p-6 md:p-8">
        <h2 class="text-xl font-semibold">{{ __('public.profile_info') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("public.profile_info_desc") }}
        </p>
        <form method="post" action="{{ route('my-account.profile.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('patch')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.name') }}</label>
                <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                @error('name')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                 @error('email')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-4">
                <button type="submit" class="rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">{{ __('public.save') }}</button>
                @if (session('status') === 'profile-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 dark:text-gray-400">{{ __('public.saved') }}</p>
                @endif
            </div>
        </form>
    </div>
    <div class="p-6 md:p-8">
        <h2 class="text-xl font-semibold">{{ __('public.update_password') }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('public.update_password_desc') }}
        </p>
        <form method="post" action="{{ route('my-account.password.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('put')
             <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.current_password') }}</label>
                <input id="current_password" name="current_password" type="password" class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500" autocomplete="current-password" />
                @error('current_password', 'updatePassword')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.new_password') }}</label>
                <input id="password" name="password" type="password" class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500" autocomplete="new-password" />
                @error('password', 'updatePassword')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500" autocomplete="new-password" />
                 @error('password_confirmation', 'updatePassword')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-4">
                <button type="submit" class="rounded-md bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">{{ __('public.save') }}</button>
                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 dark:text-gray-400">{{ __('public.saved') }}</p>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection