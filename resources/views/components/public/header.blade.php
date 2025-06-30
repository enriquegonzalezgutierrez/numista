<header class="bg-white dark:bg-gray-800/50 backdrop-blur-sm shadow-sm sticky top-0 z-10">
    <div class="mx-auto max-w-7xl py-3 px-4 sm:px-6 lg:px-8">
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