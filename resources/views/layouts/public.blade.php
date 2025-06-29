<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Numista Marketplace')</title>

        {{-- Add this line for the favicon --}}
        <link rel="icon" href="{{ asset('storage/favicon.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles & Scripts for Public Site -->
        @vite(['resources/css/public.css', 'resources/js/app.js'])

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="antialiased font-sans">
        <div class="min-h-screen">
            <header class="bg-white dark:bg-gray-800/50 backdrop-blur-sm shadow-sm sticky top-0 z-10">
                <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('public.items.index') }}" class="flex items-center space-x-3">
                             <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="h-10 w-auto">
                             <span class="text-xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">{{ config('app.name') }}</span>
                        </a>
                        <nav class="space-x-4">
                            @auth
                                <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                    {{ __('filament-panels::layout.actions.dashboard.label') }}
                                </a>
                            @else
                                <a href="{{ route('filament.admin.auth.login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                    {{ __('filament-panels::pages/auth/login.title') }}
                                </a>
                            @endauth
                        </nav>
                    </div>
                </div>
            </header>

            <main class="py-8 sm:py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>