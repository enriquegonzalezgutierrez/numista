<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Numista Marketplace')</title>

        <link rel="icon" href="{{ asset('storage/favicon.png') }}" type-="image/png">

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
        <div 
            x-data="{
                notifications: [],
                add(notification) {
                    notification.id = Date.now()
                    this.notifications.push(notification)
                    this.remove(notification.id)
                },
                remove(id) {
                    setTimeout(() => {
                        let i = this.notifications.findIndex(n => n.id === id)
                        if (i !== -1) {
                            this.notifications.splice(i, 1)
                        }
                    }, 5000)
                }
            }"
            @notify.window="add($event.detail)"
            aria-live="assertive" 
            class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50"
        >
            <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
                {{-- Session-based notifications (for page reloads) --}}
                @if (session('success'))
                    <x-public.notification type="success" message="{{ session('success') }}" />
                @endif
                @if (session('error'))
                    <x-public.notification type="error" message="{{ session('error') }}" />
                @endif
                @if ($errors->any())
                    <x-public.notification type="error" message="{{ __('Whoops! Something went wrong.') }}" />
                @endif

                {{-- THE FIX: Template for async Alpine.js notifications --}}
                <template x-for="notification in notifications" :key="notification.id">
                    <div
                        x-data="{ show: false }"
                        x-init="$nextTick(() => show = true)"
                        x-show="show"
                        x-transition:enter="transform ease-out duration-300 transition"
                        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        :class="{
                            'bg-green-100 dark:bg-green-800/20': notification.type === 'success',
                            'bg-red-100 dark:bg-red-800/20': notification.type === 'error',
                        }"
                        class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5"
                    >
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <template x-if="notification.type === 'success'">
                                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </template>
                                    <template x-if="notification.type === 'error'">
                                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                    </template>
                                </div>
                                <div class="ml-3 w-0 flex-1 pt-0.5">
                                    <p 
                                        :class="{
                                            'text-green-800 dark:text-green-200': notification.type === 'success',
                                            'text-red-800 dark:text-red-200': notification.type === 'error',
                                        }"
                                        class="text-sm font-medium" x-text="notification.message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        @vite(['resources/js/app.js'])
    </body>
</html>