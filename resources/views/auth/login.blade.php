@extends('layouts.public')

@section('content')
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        {{-- Card container for the form --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8">
            <h2 class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900 dark:text-white mb-6">{{ __('Login') }}</h2>
        
            {{-- General error message area (for auth failures) --}}
            @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative" role="alert">
                    <strong class="font-bold">{{ __('Whoops! Something went wrong.') }}</strong>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-white">{{ __('Email') }}</label>
                    <div class="mt-2">
                        {{-- Unified input field styling --}}
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                               class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500" 
                               value="{{ old('email') }}">
                        @error('email')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-white">{{ __('Password') }}</label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
                        @error('password')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-teal-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600">{{ __('Login') }}</button>
                </div>
            </form>
        </div>

        <p class="mt-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('Not a member?') }}
            <a href="{{ route('register') }}" class="font-semibold leading-6 text-teal-600 hover:text-teal-500">{{ __('Register') }}</a>
        </p>
    </div>
</div>
@endsection