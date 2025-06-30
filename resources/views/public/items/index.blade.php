@extends('layouts.public')

@section('title', __('public.marketplace_title'))

@section('content')
{{-- The main container with padding is now in the layout, so we can use a simpler div here --}}
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
        <div
            class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
            x-data="{ 
            mobileFiltersOpen: false,
            // Alpine state is not strictly needed anymore for auto-submit,
            // but can be useful for other interactions in the future.
            submitForm() {
                // Find the correct form based on visibility and submit it.
                let form = this.$refs.filterFormDesktop.offsetParent !== null 
                    ? this.$refs.filterFormDesktop 
                    : this.$refs.filterFormMobile;
                form.requestSubmit();
            }
        }">
            <div class="pt-12 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('public.marketplace_title') }}</h1>
                <p class="mt-4 text-base text-gray-500 dark:text-gray-400">{{ __('public.marketplace_subtitle') }}</p>
            </div>

            <div class="pt-12 lg:grid lg:grid-cols-4 lg:gap-x-8">
                {{-- Desktop Filters Sidebar --}}
                <aside class="hidden lg:block">
                    <x-public.items.filter-form :categories="$categories" x-ref="filterFormDesktop" />
                    <div class="mt-8 border-t dark:border-gray-700 pt-6 space-y-3">
                        <button @click="submitForm" type="button" class="w-full rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">{{ __('public.filter_apply_button') }}</button>
                        <a href="{{ route('public.items.index') }}" class="w-full block text-center rounded-md border border-slate-300 bg-white dark:bg-slate-700 dark:border-slate-600 mt-4 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50">{{ __('public.filter_clear_button') }}</a>
                    </div>
                </aside>

                {{-- Mobile filter dialog --}}
                <div x-show="mobileFiltersOpen" class="relative z-40 lg:hidden" role="dialog" aria-modal="true" x-cloak>
                    <div x-show="mobileFiltersOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-25"></div>
                    <div class="fixed inset-0 z-40 flex">
                        <div x-show="mobileFiltersOpen" @click.away="mobileFiltersOpen = false" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="relative ml-auto flex h-full w-full max-w-xs flex-col bg-white dark:bg-gray-800 shadow-xl">
                            {{-- ... (header y contenido del modal sin cambios) ... --}}
                            <div class="px-4 py-6 overflow-y-auto">
                                <x-public.items.filter-form :categories="$categories" :is-mobile="true" />
                            </div>

                            {{-- Mobile Footer with buttons --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-4 mt-auto space-y-3">
                                <button @click="$refs.filterFormMobile.requestSubmit(); mobileFiltersOpen = false" type="button" class="w-full rounded-md bg-teal-600 px-4 py-2 text-center text-sm font-medium text-white shadow-sm hover:bg-teal-700">
                                    {{ __('public.filter_apply_button') }}
                                </button>
                                {{-- AÑADE ESTE BOTÓN/ENLACE AQUÍ --}}
                                <a href="{{ route('public.items.index') }}" class="block w-full rounded-md bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 mt-4 px-4 py-2 text-center text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-600">
                                    {{ __('public.filter_clear_button') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Product grid --}}
                <div class="lg:col-span-3">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ trans_choice('public.results_count', $items->total()) }}
                        </p>
                        <div class="lg:hidden">
                            <button @click="mobileFiltersOpen = true" class="inline-flex items-center rounded-md bg-white dark:bg-slate-700 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50">{{ __('public.filter_show_button') }}<svg class="ml-2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.59L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                                </svg></button>
                        </div>
                    </div>
                    @if($items->count())
                    <div class="grid grid-cols-1 gap-y-12 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
                        @foreach($items as $item)
                        <a href="{{ route('public.items.show', $item) }}" class="group block">
                            <div class="relative">
                                <div class="relative h-72 w-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
                                    <img src="{{ $item->images->first() ? route('public.images.show', ['image' => $item->images->first()->id]) : asset('images/placeholder.png') }}" alt="{{ $item->images->first()?->alt_text ?? $item->name }}" class="h-full w-full object-cover object-center transition-transform duration-300 ease-in-out group-hover:scale-105">
                                    <div class="absolute inset-x-0 bottom-0 h-36 bg-gradient-to-t from-black opacity-50" aria-hidden="true"></div>
                                    <p class="absolute bottom-4 right-4 text-lg font-semibold text-white">{{ number_format($item->sale_price, 2, ',', '.') }} €</p>
                                </div>
                                <div class="relative mt-4">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $item->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->tenant->name }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="mt-12">{{ $items->appends(request()->query())->links() }}</div>
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
</div>
@endsection