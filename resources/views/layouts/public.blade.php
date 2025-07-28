<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale-1">
        <title>@yield('title', 'Numista Marketplace')</title>

        <link rel="icon" href="{{ asset('storage/favicon.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles handled by Vite -->
        @vite(['resources/css/public.css'])
        
        {{-- Define custom CSS variables for theme colors --}}
        @php
            $primaryColor = \Filament\Support\Colors\Color::Teal;
        @endphp
        <style>
            :root {
                --c-500: {{ $primaryColor[500] }};
                --c-600: {{ $primaryColor[600] }};
                --c-700: {{ $primaryColor[700] }};
            }
        </style>
    </head>
    <body class="antialiased font-sans flex flex-col min-h-screen">
        <div class="flex-grow">
            <x-public.header />

            <main class="flex-grow py-8 sm:py-12">
                    @yield('content')
            </main>
        </div>

        <x-public.footer />

        <!-- Global notification live region, positioned in the bottom-right corner. -->
        <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50">
            <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
                {{-- Session Success Message --}}
                @if (session('success'))
                    <x-public.notification type="success" message="{{ session('success') }}" />
                @endif

                {{-- Session Error Message --}}
                @if (session('error'))
                    <x-public.notification type="error" message="{{ session('error') }}" />
                @endif

                {{-- THE FIX: Display a generic validation error toast --}}
                @if ($errors->any())
                    <x-public.notification type="error" message="{{ __('Whoops! Something went wrong.') }}" />
                @endif
            </div>
        </div>

        @vite(['resources/js/app.js'])
    </body>
</html>