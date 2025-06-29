@extends('layouts.public')

@section('title', $item->name)

@section('content')
<div class="bg-white dark:bg-gray-800">
    <div class="pt-6">
        <nav aria-label="Breadcrumb" class="mx-auto flex max-w-7xl items-center space-x-2 px-4 sm:px-6 lg:px-8">
            {{-- ... breadcrumb ... --}}
            <ol role="list" class="flex items-center space-x-2">
                <li>
                    <div class="flex items-center">
                        <a href="{{ route('public.items.index') }}" class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ __('public.marketplace_title') }}</a>
                        <svg width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true" class="h-5 w-4 text-gray-300 dark:text-gray-500">
                            <path d="M5.697 4.34L8.98 16.532h1.327L7.025 4.341H5.697z" />
                        </svg>
                    </div>
                </li>
                <li class="text-sm">
                    <a href="#" aria-current="page" class="font-medium text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300">{{ $item->name }}</a>
                </li>
            </ol>
        </nav>

        <!-- Product info -->
        <div 
            class="mx-auto max-w-2xl px-4 pt-10 pb-16 sm:px-6 lg:grid lg:max-w-7xl lg:grid-cols-3 lg:gap-x-8 lg:px-8 lg:pt-16 lg:pb-24"
            x-data="{ 
                mainImageUrl: '{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : asset('images/placeholder.png') }}' 
            }"
        >
            {{-- Columna Izquierda: Galería de Imágenes --}}
            <div class="lg:col-span-2 lg:pr-8">
                <!-- Main Image -->
                <div class="aspect-[4/3] w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                    <img :src="mainImageUrl" alt="{{ $item->name }}" class="h-full w-full object-cover object-center">
                </div>

                <!-- Thumbnail Gallery -->
                @if($item->images->count() > 1)
                    <div class="mx-auto mt-6 hidden w-full max-w-2xl sm:block lg:max-w-none">
                        <div class="grid grid-cols-4 gap-6">
                            @foreach($item->images as $image)
                                <button 
                                    @click="mainImageUrl = '{{ route('public.images.show', ['image' => $image->id]) }}'"
                                    class="relative flex h-24 cursor-pointer items-center justify-center rounded-md bg-white dark:bg-gray-800 text-sm font-medium uppercase text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring focus:ring-opacity-50 focus:ring-offset-4 dark:focus:ring-offset-gray-800"
                                >
                                    <span class="sr-only">{{ $image->alt_text ?? $item->name }}</span>
                                    <span class="absolute inset-0 overflow-hidden rounded-md">
                                        <img src="{{ route('public.images.show', ['image' => $image->id]) }}" alt="" class="h-full w-full object-cover object-center">
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Columna Derecha: Toda la información del ítem --}}
            <div class="mt-4 lg:col-span-1 lg:mt-0">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">{{ $item->name }}</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('public.from_collection') }} <strong>{{ $item->tenant->name }}</strong></p>
                <div class="mt-4">
                    <p class="text-3xl tracking-tight text-gray-900 dark:text-white">{{ number_format($item->sale_price, 2, ',', '.') }} €</p>
                </div>
                
                <div class="mt-10 border-t border-gray-200 dark:border-gray-700 pt-10">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('public.description') }}</h3>
                    <div class="prose prose-sm mt-4 text-gray-600 dark:text-gray-300">
                        <p>{{ $item->description }}</p>
                    </div>

                    @if($item->categories->count())
                    <div class="mt-10">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('public.categories') }}</h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($item->categories as $category)
                                <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-200 dark:ring-gray-600">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="mt-10">
                    <button type="button" class="flex w-full items-center justify-center rounded-md border border-transparent bg-primary-600 px-8 py-3 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-primary-500 dark:hover:bg-primary-600 dark:focus:ring-offset-gray-800">
                        {{ __('public.contact_seller') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection