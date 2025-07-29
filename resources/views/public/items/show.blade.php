@extends('layouts.public')

@section('title', $item->name)

@section('content')

<div x-data="{
        isModalOpen: false,
        mainImageUrl: '{{ $item->images->first()?->url ?? '/images/placeholder.svg' }}',
        isLightboxOpen: false,
        activeTab: 'description'
    }">

    <div class="bg-white dark:bg-gray-800">
        <div class="pt-6">
            
            <nav aria-label="Breadcrumb" class="mx-auto flex max-w-7xl items-center space-x-2 px-4 sm:px-6 lg:px-8">
                <ol role="list" class="flex items-center space-x-2">
                    <li>
                        <div class="flex items-center">
                            <a href="{{ route('public.items.index') }}" class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ __('public.marketplace_title') }}</a>
                            <svg width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true" class="h-5 w-4 text-gray-300 dark:text-gray-500"><path d="M5.697 4.34L8.98 16.532h1.327L7.025 4.341H5.697z" /></svg>
                        </div>
                    </li>
                    <li class="text-sm">
                        <a href="#" aria-current="page" class="font-medium text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300">{{ $item->name }}</a>
                    </li>
                </ol>
            </nav>

            
            <div class="mx-auto max-w-2xl px-4 pt-10 pb-16 sm:px-6 lg:grid lg:max-w-7xl lg:grid-cols-2 lg:gap-x-8 lg:px-8 lg:pt-16 lg:pb-24">
                
                
                <div class="lg:col-span-1">
                    <div class="aspect-[4/3] w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                        <button @click="isLightboxOpen = true" class="w-full h-full">
                            <img :src="mainImageUrl" alt="{{ $item->name }}" class="h-full w-full object-cover object-center cursor-zoom-in">
                        </button>
                    </div>
                    @if($item->images->count() > 1)
                        <div class="mx-auto mt-6 hidden w-full max-w-2xl sm:block lg:max-w-none">
                            <div class="grid grid-cols-4 gap-6">
                                @foreach($item->images as $image)
                                    <button @click="mainImageUrl = '{{ $image->url }}'" 
                                            :class="{ 'ring-2 ring-offset-2 ring-teal-500': mainImageUrl === '{{ $image->url }}' }"
                                            class="relative flex h-24 cursor-pointer items-center justify-center rounded-md bg-white dark:bg-gray-800 text-sm font-medium uppercase text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-offset-gray-800">
                                        <span class="absolute inset-0 overflow-hidden rounded-md">
                                            <img src="{{ $image->url }}" alt="" class="h-full w-full object-cover object-center">
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                
                <div class="mt-10 lg:col-span-1 lg:mt-0">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">{{ $item->name }}</h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('public.from_collection') }} <strong>{{ $item->tenant->name }}</strong></p>
                    
                    <div class="mt-4">
                        <p class="text-3xl tracking-tight text-gray-900 dark:text-white">{{ number_format($item->sale_price, 2, ',', '.') }} €</p>
                    </div>

                    
                    <div class="mt-10 hidden lg:block">
                        <form action="{{ route('cart.add', $item) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center rounded-md border border-transparent bg-teal-600 px-8 py-3 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                {{ __('public.add_to_cart') }}
                            </button>
                        </form>
                    </div>
                    
                    
                    <div class="mt-10 hidden lg:block">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'description'" :class="{ 'border-teal-500 text-teal-600': activeTab === 'description', 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700': activeTab !== 'description' }" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">{{ __('public.description') }}</button>
                                @if($item->attributes->isNotEmpty())
                                <button @click="activeTab = 'details'" :class="{ 'border-teal-500 text-teal-600': activeTab === 'details', 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700': activeTab !== 'details' }" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">{{ __('public.item_details') }}</button>
                                @endif
                                @if($item->categories->isNotEmpty())
                                <button @click="activeTab = 'categories'" :class="{ 'border-teal-500 text-teal-600': activeTab === 'categories', 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700': activeTab !== 'categories' }" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">{{ __('public.categories') }}</button>
                                @endif
                            </nav>
                        </div>
                        <div class="py-6">
                            @include('public.items.partials.item-info-panels')
                        </div>
                    </div>
                </div>

                
                <div class="mt-10 divide-y divide-gray-200 dark:divide-gray-700 border-t border-gray-200 dark:border-gray-700 lg:hidden">
                     @include('public.items.partials.item-info-accordion')
                </div>
            </div>

        </div>
    </div>

    
    <div class="fixed bottom-0 left-0 right-0 lg:hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700 p-4">
        <form action="{{ route('cart.add', $item) }}" method="POST">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center rounded-md border border-transparent bg-teal-600 px-8 py-3 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                {{ __('public.add_to_cart') }} - {{ number_format($item->sale_price, 2, ',', '.') }} €
            </button>
        </form>
    </div>

    
    <div 
        x-show="isLightboxOpen" 
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4" 
        style="display: none;"
        @keydown.escape.window="isLightboxOpen = false"
    >
        <div @click.self="isLightboxOpen = false" class="relative w-full h-full flex items-center justify-center">
            <img :src="mainImageUrl" alt="{{ $item->name }}" class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl">
            <button @click="isLightboxOpen = false" class="absolute top-4 right-4 text-white hover:text-gray-300" aria-label="{{ __('public.lightbox.close') }}">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>
</div>
@endsection