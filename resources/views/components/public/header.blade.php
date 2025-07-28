{{-- resources/views/components/public/header.blade.php --}}
<header class="bg-white dark:bg-gray-800/50 backdrop-blur-sm shadow-sm sticky top-0 z-10">
    <div class="mx-auto max-w-7xl py-3 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            {{-- Logo and App Name --}}
            <a href="{{ route('public.items.index') }}" class="flex items-center space-x-3">
                 <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="h-10 w-auto">
                 
                 <span class="hidden sm:inline text-xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">{{ config('app.name') }}</span>
            </a>

            {{-- Right side navigation --}}
            <nav class="flex items-center space-x-2 sm:space-x-4">
                
                {{-- Cart Icon --}}
                <div class="flow-root">
                    <a href="{{ route('cart.index') }}" class="group -m-2 flex items-center p-2">
                        <svg class="h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        @php
                            $cartCount = count(session('cart', []));
                        @endphp
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-400 group-hover:text-gray-800">{{ $cartCount }}</span>
                        <span class="sr-only">{{ __('public.header.cart_sr_text') }}</span>
                    </a>
                </div>
                
                <span class="h-6 w-px bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                
                {{-- Authentication Links --}}
                @guest
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">{{ __('Login') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">{{ __('Register') }}</a>
                    @endif
                @else
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('filament.admin.pages.dashboard', ['tenant' => auth()->user()->tenants()->first()]) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">{{ __('My Account') }}</a>
                    @endif

                    <span class="h-6 w-px bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>

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