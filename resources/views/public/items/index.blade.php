@extends('layouts.public')

@section('title', __('public.marketplace_title'))

@section('content')
    <div class="bg-white dark:bg-gray-800">
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
            <div class="pb-12 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('public.marketplace_title') }}</h1>
                <p class="mt-4 text-base text-gray-500 dark:text-gray-400">{{ __('public.marketplace_subtitle') }}</p>
            </div>


            @if($items->count())
                <div class="mt-8 grid grid-cols-1 gap-y-12 sm:grid-cols-2 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-8">
                    @foreach($items as $item)
                        {{-- The <a> tag is now the main container for the card --}}
                        <a href="{{ route('public.items.show', $item) }}" class="group block">
                            <div class="relative">
                                <div class="relative h-72 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                                    <img src="{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : asset('images/placeholder.png') }}"
                                         alt="{{ $item->images->first()?->alt_text ?? $item->name }}"
                                         class="h-full w-full object-cover object-center transition-transform duration-300 ease-in-out group-hover:scale-105">
                                    
                                    {{-- The price overlay is now inside the link, but positioned visually --}}
                                    <div class="absolute inset-x-0 bottom-0 h-36 bg-gradient-to-t from-black opacity-50" aria-hidden="true"></div>
                                    <p class="absolute bottom-4 right-4 text-lg font-semibold text-white">{{ number_format($item->sale_price, 2, ',', '.') }} €</p>
                                </div>
                                
                                {{-- Text content below the image --}}
                                <div class="relative mt-4">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $item->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->tenant->name }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $items->links() }}
                </div>
            @else
                <div class="mt-12 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">{{ __('public.no_items_found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Vuelve a intentarlo más tarde o añade nuevos ítems a la venta.</p>
                </div>
            @endif
        </div>
    </div>
@endsection