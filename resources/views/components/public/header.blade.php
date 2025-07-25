{{-- resources/views/components/public/header.blade.php --}}
<header class="bg-white dark:bg-gray-800/50 backdrop-blur-sm shadow-sm sticky top-0 z-10">
    <div class="mx-auto max-w-7xl py-3 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            {{-- Logo and App Name --}}
            <a href="{{ route('public.items.index') }}" class="flex items-center space-x-3">
                 <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="h-10 w-auto">
                 <span class="text-xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">{{ config('app.name') }}</span>
            </a>

            {{-- Authentication Links --}}
            <nav class="flex items-center space-x-4">
                @guest
                    {{-- User is a guest: show public Login and Register links --}}
                    {{-- FIX: Use the 'login' route and our own translation key --}}
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        {{ __('Login') }}
                    </a>
                    @if (Route::has('register'))
                        {{-- FIX: Use the 'register' route and our own translation key --}}
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                            {{ __('Register') }}
                        </a>
                    @endif
                @else
                    {{-- User is logged in: check if they are an admin or a customer --}}
                    @if (auth()->user()->is_admin)
                        {{-- User is an Admin: link to Filament dashboard --}}
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        {{-- User is a Customer: link to public home/dashboard --}}
                        <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            {{ __('My Account') }}
                        </a>
                    @endif

                    <span class="text-gray-300 dark:text-gray-600">|</span>

                    {{-- Logout Form for both user types --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                @endauth
            </nav>
        </div>
    </div>
</header>