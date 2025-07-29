@extends('layouts.public')

@section('title', config('app.name'))

@section('content')
    <div class="bg-white dark:bg-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="relative overflow-hidden rounded-lg">
                <div class="absolute inset-0">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1999&auto=format&fit=crop" alt="Hero background" class="h-full w-full object-cover">
                </div>
                <div class="relative bg-gray-900 bg-opacity-60 px-6 py-32 sm:px-12 sm:py-40 lg:px-16">
                    <div class="relative mx-auto flex max-w-3xl flex-col items-center text-center">
                        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Descubre un Mundo de Coleccionables</h2>
                        <p class="mt-3 text-xl text-white">Desde monedas raras hasta relojes vintage, encuentra piezas únicas para tu colección.</p>
                        <a href="{{ route('public.items.index') }}" class="mt-8 block w-full rounded-md border border-transparent bg-white px-8 py-3 text-base font-medium text-gray-900 hover:bg-gray-100 sm:w-auto">Explorar Marketplace</a>
                    </div>
                </div>
            </div>

            <!-- Featured Collections Section -->
            @if($featuredCollections->isNotEmpty())
            <div class="py-16 sm:py-24">
                <div class="sm:flex sm:items-baseline sm:justify-between">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Colecciones Destacadas</h2>
                    {{-- <a href="#" class="hidden text-sm font-semibold text-teal-600 hover:text-teal-500 sm:block">Ver todas<span aria-hidden="true"> →</span></a> --}}
                </div>
                <div class="mt-6 grid grid-cols-1 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 sm:gap-x-6 lg:gap-x-8">
                    @foreach($featuredCollections as $collection)
                        <x-public.collections.collection-card :collection="$collection" />
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Latest Items Section -->
            @if($latestItems->isNotEmpty())
            <div class="py-16 sm:py-24 border-t border-gray-200 dark:border-gray-700">
                <div class="sm:flex sm:items-baseline sm:justify-between">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Últimos Ítems Añadidos</h2>
                    <a href="{{ route('public.items.index') }}" class="hidden text-sm font-semibold text-teal-600 hover:text-teal-500 sm:block">Ver todo el marketplace<span aria-hidden="true"> →</span></a>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                    @foreach($latestItems as $item)
                        <x-public.items.item-card :item="$item" />
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection