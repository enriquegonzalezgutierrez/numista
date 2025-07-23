@extends('layouts.public')

@section('title', $item->name)

@section('content')

<div x-data="{
        isModalOpen: false,
        successMessage: '{{ session('success') }}',
        errorMessage: '{{ session('error') }}',
        mainImageUrl: '{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : '/images/placeholder.svg' }}'
    }">
    
    <div class="bg-white dark:bg-gray-800">
        <div class="pt-6">
            {{-- Breadcrumb and main product info --}}
            {{-- This part remains the same as your correct version --}}
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

            <div class="mx-auto max-w-2xl px-4 pt-10 pb-16 sm:px-6 lg:grid lg:max-w-7xl lg:grid-cols-3 lg:gap-x-8 lg:px-8 lg:pt-16 lg:pb-24">
                <div class="lg:col-span-2 lg:pr-8">
                    <div class="aspect-[4/3] w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                        <img :src="mainImageUrl" alt="{{ $item->name }}" class="h-full w-full object-cover object-center">
                    </div>
                    @if($item->images->count() > 1)
                        <div class="mx-auto mt-6 hidden w-full max-w-2xl sm:block lg:max-w-none">
                            <div class="grid grid-cols-4 gap-6">
                                @foreach($item->images as $image)
                                    <button @click="mainImageUrl = '{{ route('public.images.show', ['image' => $image->id]) }}'" class="relative flex h-24 cursor-pointer items-center justify-center rounded-md bg-white dark:bg-gray-800 text-sm font-medium uppercase text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring focus:ring-opacity-50 focus:ring-offset-4 dark:focus:ring-offset-gray-800">
                                        <span class="absolute inset-0 overflow-hidden rounded-md">
                                            <img src="{{ route('public.images.show', ['image' => $image->id]) }}" alt="" class="h-full w-full object-cover object-center">
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

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
                        @if($item->attributes->isNotEmpty())
                        <div class="mt-10">
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('public.item_details') }}</h3>
                            <div class="mt-4">
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                                    @foreach($item->attributes->sortBy('name') as $attribute)
                                        <div>
                                            <dt class="font-medium text-gray-500 dark:text-gray-400">
                                                @php($key = 'panel.attribute_name_' . strtolower(str_replace(' ', '_', $attribute->name)))
                                                {{ trans()->has($key) ? __($key) : $attribute->name }}
                                            </dt>
                                            <dd class="text-gray-900 dark:text-white mt-1">
                                                @if($attribute->type === 'select' && strtolower($attribute->name) === 'grade')
                                                    {{ __("item.grade_{$attribute->pivot->value}") ?? $attribute->pivot->value }}
                                                @else
                                                    {{ $attribute->pivot->value }}
                                                @endif
                                            </dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                        @endif
                        @if($item->categories->isNotEmpty())
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
                        <button @click="isModalOpen = true" type="button" class="flex w-full items-center justify-center rounded-md border border-transparent bg-teal-600 px-8 py-3 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-offset-gray-800">
                            {{ __('public.contact_seller') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div x-show="successMessage" x-transition x-init="setTimeout(() => successMessage = '', 5000)" class="fixed bottom-5 right-5 z-20 bg-green-500 text-white py-2 px-4 rounded-lg shadow-lg" style="display: none;">
        <p x-text="successMessage"></p>
    </div>
    <div x-show="errorMessage" x-transition x-init="setTimeout(() => errorMessage = '', 5000)" class="fixed bottom-5 right-5 z-20 bg-red-500 text-white py-2 px-4 rounded-lg shadow-lg" style="display: none;">
        <p x-text="errorMessage"></p>
    </div>

    <!-- Contact Seller Modal -->
    <div 
        x-show="isModalOpen" 
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
        class="fixed inset-0 z-10 overflow-y-auto bg-gray-500 bg-opacity-75" 
        style="display: none;"
        @keydown.escape.window="isModalOpen = false"
    >
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div 
                @click.outside="isModalOpen = false"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
            >
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    {{ __('public.contact_modal_title', ['itemName' => $item->name]) }}
                </h3>
                
                {{-- In resources/views/public/items/show.blade.php, inside the modal --}}
                <form action="{{ route('public.items.contact', $item) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.contact_modal_name') }}</label>
                        {{-- ADDED px-3 py-2 --}}
                        <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.contact_modal_email') }}</label>
                        {{-- ADDED px-3 py-2 --}}
                        <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.contact_modal_message') }}</label>
                        {{-- ADDED px-3 py-2 --}}
                        <textarea name="message" id="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="{{ __('public.contact_modal_message_placeholder') }}"></textarea>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="isModalOpen = false" type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                            {{ __('public.contact_modal_cancel') }}
                        </button>
                        <button type="submit" class="rounded-md border border-transparent bg-teal-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('public.contact_modal_send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection