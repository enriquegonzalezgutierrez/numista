@extends('layouts.public')

@section('title', __('public.marketplace_title'))

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="py-12 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('public.marketplace_title') }}</h1>
        <p class="mt-4 text-base text-gray-500 dark:text-gray-400">{{ __('public.marketplace_subtitle') }}</p>
    </div>

    <div class="pt-12 lg:grid lg:grid-cols-4 lg:gap-x-8" x-data="{ mobileFiltersOpen: false }">
        {{-- Desktop Filters Sidebar --}}
        <aside class="hidden lg:block bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm h-fit sticky top-8">
            <form action="{{ route('public.items.index') }}" method="GET" id="desktop-filter-form">
                <x-public.items.filter-form 
                    :categories="$categories" 
                    :filterableAttributes="$filterableAttributes" 
                />
                <div class="mt-8 border-t dark:border-gray-700 pt-6 space-y-3">
                    <button type="submit" class="w-full rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">{{ __('public.filter_apply_button') }}</button>
                    <a href="{{ route('public.items.index') }}" class="w-full block text-center rounded-md border border-slate-300 bg-white dark:bg-slate-700 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50">{{ __('public.filter_clear_button') }}</a>
                </div>
            </form>
        </aside>

        {{-- Mobile filter dialog --}}
        <div x-show="mobileFiltersOpen" class="relative z-40 lg:hidden" role="dialog" aria-modal="true" x-cloak>
            {{-- ... (código del modal que no necesita cambios) ... --}}
        </div>

        {{-- Product grid --}}
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ trans_choice('public.results_count', $items->total(), ['count' => $items->total()]) }}</p>
                    <div class="lg:hidden">
                        <button @click="mobileFiltersOpen = true" class="inline-flex items-center rounded-md bg-white dark:bg-slate-700 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50">{{ __('public.filter_show_button') }}<svg class="ml-2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.59L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" /></svg></button>
                    </div>
                </div>
                
                @if($items->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($items as $item)
                            <x-public.items.item-card :item="$item" />
                        @endforeach
                    </div>
                    <div class="mt-12">
                        {{ $items->withQueryString()->links() }}
                    </div>
                @else
                    <div class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('public.no_items_found_filtered') }}</p>
                        <a href="{{ route('public.items.index') }}" class="mt-4 inline-block rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700">{{ __('public.clear_filters_link') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection