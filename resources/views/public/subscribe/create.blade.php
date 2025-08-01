@extends('layouts.public')

@section('title', __('public.subscribe_title'))

@section('content')
<div class="bg-white dark:bg-gray-800 py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">{{ __('public.subscribe_page_title', ['tenantName' => $tenant->name]) }}</h2>
            <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">{{ __('public.subscribe_page_subtitle') }}</p>
        </div>

        <div x-data="{ processing: false }" class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-2">
            <!-- Monthly Plan -->
            <div class="flex flex-col rounded-2xl border border-gray-200 dark:border-gray-700 p-8 text-center shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('public.plan_monthly') }}</h3>
                <p class="mt-4 text-5xl font-bold tracking-tight text-gray-900 dark:text-white">15€<span class="text-base font-medium text-gray-500 dark:text-gray-400">/mes</span></p>
                <p class="mt-6 text-gray-500 dark:text-gray-400">{{ __('public.monthly_billing') }}</p>
                <form action="{{ route('subscription.store', $tenant) }}" method="POST" @submit="processing = true" class="mt-8">
                    @csrf
                    <input type="hidden" name="price_id" value="{{ $priceMonthly }}">
                    <button type="submit" :disabled="processing" class="w-full rounded-md bg-teal-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-teal-700 disabled:opacity-50">
                        <span x-show="!processing">{{ __('public.subscribe_button') }}</span>
                        <span x-show="processing" class="italic">{{ __('public.redirecting_to_stripe') }}</span>
                    </button>
                </form>
            </div>

            <!-- Yearly Plan -->
            <div class="relative flex flex-col rounded-2xl border-2 border-teal-500 p-8 text-center shadow-sm">
                <div class="absolute top-0 -translate-y-1/2 transform self-center rounded-full bg-teal-500 px-3 py-1 text-sm font-semibold text-white">
                    {{ __('public.most_popular') }}
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('public.plan_yearly') }}</h3>
                <p class="mt-4 text-5xl font-bold tracking-tight text-gray-900 dark:text-white">150€<span class="text-base font-medium text-gray-500 dark:text-gray-400">/año</span></p>
                <p class="mt-6 text-gray-500 dark:text-gray-400">{{ __('public.yearly_billing') }}</p>
                <form action="{{ route('subscription.store', $tenant) }}" method="POST" @submit="processing = true" class="mt-8">
                    @csrf
                    <input type="hidden" name="price_id" value="{{ $priceYearly }}">
                    <button type="submit" :disabled="processing" class="w-full rounded-md bg-teal-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-teal-700 disabled:opacity-50">
                        <span x-show="!processing">{{ __('public.subscribe_button') }}</span>
                        <span x-show="processing" class="italic">{{ __('public.redirecting_to_stripe') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection