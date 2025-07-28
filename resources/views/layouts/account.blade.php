@extends('layouts.public')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12">
        {{-- Sidebar Navigation --}}
        <aside class="lg:col-span-3 mb-8 lg:mb-0">
            <nav class="space-y-1">
                <a href="{{ route('my-account.dashboard') }}" 
                   @class([
                        'group border-l-4 px-3 py-2 flex items-center text-sm font-medium transition-colors',
                        'bg-teal-50 border-teal-500 text-teal-700 dark:bg-teal-500/10 dark:border-teal-500/30 dark:text-teal-400' => request()->routeIs('my-account.dashboard'),
                        'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300' => !request()->routeIs('my-account.dashboard'),
                   ])>
                    <svg class="text-gray-400 group-hover:text-gray-500 -ml-1 mr-3 h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="truncate">{{ __('public.dashboard') }}</span>
                </a>

                <a href="{{ route('my-account.orders') }}" 
                   @class([
                        'group border-l-4 px-3 py-2 flex items-center text-sm font-medium transition-colors',
                        'bg-teal-50 border-teal-500 text-teal-700 dark:bg-teal-500/10 dark:border-teal-500/30 dark:text-teal-400' => request()->routeIs('my-account.orders'),
                        'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300' => !request()->routeIs('my-account.orders'),
                   ])>
                    <svg class="text-gray-400 group-hover:text-gray-500 -ml-1 mr-3 h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="truncate">{{ __('public.my_orders') }}</span>
                </a>
                
                <a href="{{ route('my-account.addresses.index') }}" 
                   @class([
                        'group border-l-4 px-3 py-2 flex items-center text-sm font-medium transition-colors',
                        'bg-teal-50 border-teal-500 text-teal-700 dark:bg-teal-500/10 dark:border-teal-500/30 dark:text-teal-400' => request()->routeIs('my-account.addresses.*'),
                        'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300' => !request()->routeIs('my-account.addresses.*'),
                   ])>
                    <svg class="text-gray-400 group-hover:text-gray-500 -ml-1 mr-3 h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="truncate">{{ __('public.my_addresses') }}</span>
                </a>
                
                <a href="{{ route('my-account.profile.edit') }}" 
                   @class([
                        'group border-l-4 px-3 py-2 flex items-center text-sm font-medium transition-colors',
                        'bg-teal-50 border-teal-500 text-teal-700 dark:bg-teal-500/10 dark:border-teal-500/30 dark:text-teal-400' => request()->routeIs('my-account.profile.edit'),
                        'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300' => !request()->routeIs('my-account.profile.edit'),
                   ])>
                    <svg class="text-gray-400 group-hover:text-gray-500 -ml-1 mr-3 h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="truncate">{{ __('public.account_details') }}</span>
                </a>
            </nav>
        </aside>

        {{-- Main Content Area --}}
        <main class="lg:col-span-9">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @yield('account-content')
            </div>
        </main>
    </div>
</div>
@endsection