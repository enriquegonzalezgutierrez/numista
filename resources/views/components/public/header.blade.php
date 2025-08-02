{{-- The entire header is now an Alpine component to manage the mobile menu state --}}
<header x-data="{ mobileMenuOpen: false }" class="bg-white dark:bg-gray-800/50 backdrop-blur-sm shadow-sm sticky top-0 z-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
            {{-- Logo --}}
            <div class="flex">
                <a href="{{ route('landing') }}" class="flex flex-shrink-0 items-center space-x-3">
                     <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
                     <span class="hidden sm:inline text-xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">{{ config('app.name') }}</span>
                </a>
            </div>

            {{-- Desktop Navigation (hidden on small screens) --}}
            <nav class="hidden lg:flex lg:items-center lg:space-x-6">
                <a href="{{ route('public.items.index') }}" 
                   @class([
                       'text-sm font-semibold transition-colors',
                       'text-teal-600 dark:text-teal-400' => request()->routeIs('public.items.*'),
                       'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => !request()->routeIs('public.items.*'),
                   ])>
                    {{ __('public.header.marketplace') }}
                </a>
                
                <a href="{{ route('register.seller') }}" 
                   @class([
                        'text-sm font-semibold transition-colors',
                        'text-teal-600 dark:text-teal-400' => request()->routeIs('register.seller'),
                        'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => !request()->routeIs('register.seller'),
                   ])>
                    {{ __('public.header.sell') }}
                </a>
            </nav>

            {{-- Right side icons and auth links --}}
            <div class="flex items-center">
                <div class="flex items-center space-x-4">
                    {{-- Cart Icon --}}
                    <div class="flow-root" x-data="{ cartCount: {{ count(session('cart', [])) }} }" @cart-updated.window="cartCount = $event.detail.cartCount">
                        <a href="{{ route('cart.index') }}" class="group -m-2 flex items-center p-2">
                            <svg class="h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <span x-text="cartCount" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-400 group-hover:text-gray-800"></span>
                            <span class="sr-only">{{ __('public.header.cart_sr_text') }}</span>
                        </a>
                    </div>
                    
                    {{-- Separator --}}
                    <span class="h-6 w-px bg-gray-200 dark:bg-gray-600 hidden lg:inline-block" aria-hidden="true"></span>
                    
                    {{-- Auth links for Desktop --}}
                    <div class="hidden lg:flex lg:items-center space-x-4">
                        @guest
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">{{ __('Login') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">{{ __('Register') }}</a>
                            @endif
                        @else
                            @if (auth()->user()->is_admin)
                                <a href="{{ route('filament.admin.pages.dashboard', ['tenant' => auth()->user()->tenants()->first()]) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">{{ __('public.dashboard') }}</a>
                            @else
                                <a href="{{ route('my-account.dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">{{ __('public.my_account') }}</a>
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
                    </div>
                </div>

                {{-- Mobile menu button --}}
                <div class="flex items-center lg:hidden ml-4">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500 dark:hover:bg-gray-700" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        {{-- Icon when menu is closed --}}
                        <svg x-show="!mobileMenuOpen" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        {{-- Icon when menu is open --}}
                        <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile menu, show/hide based on menu state. --}}
    <div x-show="mobileMenuOpen" class="lg:hidden" id="mobile-menu" x-cloak>
        <div class="space-y-1 px-2 pb-3 pt-2">
            <a href="{{ route('public.items.index') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">{{ __('public.header.marketplace') }}</a>
            <a href="{{ route('register.seller') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">{{ __('public.header.sell') }}</a>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 pb-3">
            <div class="space-y-1 px-2">
                 @guest
                    <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">{{ __('Login') }}</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">{{ __('Register') }}</a>
                    @endif
                @else
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('filament.admin.pages.dashboard', ['tenant' => auth()->user()->tenants()->first()]) }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">{{ __('public.dashboard') }}</a>
                    @else
                         <a href="{{ route('my-account.dashboard') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">{{ __('public.my_account') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); this.closest('form').submit();"
                           class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</header>