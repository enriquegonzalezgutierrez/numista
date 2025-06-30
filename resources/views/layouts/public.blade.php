<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Numista Marketplace')</title>

        <link rel="icon" href="{{ asset('storage/favicon.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles & Scripts handled by Vite -->
        @vite(['resources/css/public.css', 'resources/js/app.js'])
        
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
    {{-- This structure ensures the footer is always at the bottom --}}
    <body class="antialiased font-sans flex flex-col min-h-screen">
        <div class="flex-grow">
            <x-public.header />

            {{-- The main content area will grow to fill available space --}}
            <main class="flex-grow py-8 sm:py-12">
                    @yield('content')
            </main>
        </div>

        <x-public.footer />
    </body>
</html>