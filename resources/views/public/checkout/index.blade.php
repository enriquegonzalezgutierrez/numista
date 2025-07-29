@extends('layouts.public')

@section('title', __('public.checkout'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">{{ __('public.checkout') }}</h1>
    
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative" role="alert">
            <strong class="font-bold">{{ __('Whoops! Something went wrong.') }}</strong>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start" x-data="{ addressOption: '{{ $addresses->isNotEmpty() ? 'existing' : 'new' }}' }">
            <section class="lg:col-span-7 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('public.shipping_information') }}</h2>

                <div class="mt-4">
                    <fieldset>
                        <legend class="sr-only">{{ __('public.shipping_address') }}</legend>
                        <div class="space-y-4">
                            @if($addresses->isNotEmpty())
                                {{-- Show options only if user has addresses --}}
                                <div class="flex items-center">
                                    <input id="address_option_existing" name="address_option" type="radio" value="existing" x-model="addressOption" class="h-4 w-4 border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <label for="address_option_existing" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('public.address.use_saved') }}</label>
                                </div>

                                <div x-show="addressOption === 'existing'" x-collapse class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    @foreach($addresses as $address)
                                        <label for="address-{{ $address->id }}" class="relative block cursor-pointer rounded-lg border bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none ring-teal-500">
                                            <input type="radio" name="selected_address_id" id="address-{{ $address->id }}" value="{{ $address->id }}" class="sr-only" aria-labelledby="address-{{ $address->id }}-label" aria-describedby="address-{{ $address->id }}-description" :disabled="addressOption !== 'existing'" @if($loop->first) checked @endif>
                                            <p id="address-{{ $address->id }}-label" class="text-sm font-medium text-gray-900 dark:text-white">{{ $address->label }}</p>
                                            <address id="address-{{ $address->id }}-description" class="mt-1 not-italic text-sm text-gray-500 dark:text-gray-400">
                                                {{ $address->recipient_name }}<br>
                                                {{ $address->street_address }}<br>
                                                {{ $address->postal_code }} {{ $address->city }}
                                            </address>
                                            <span class="pointer-events-none absolute -inset-px rounded-lg border-2" aria-hidden="true"></span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="flex items-center">
                                    <input id="address_option_new" name="address_option" type="radio" value="new" x-model="addressOption" class="h-4 w-4 border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <label for="address_option_new" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('public.address.add_new') }}</label>
                                </div>

                                <div x-show="addressOption === 'new'" x-collapse>
                                    @include('public.my-account.addresses.partials.form-fields', ['address' => new \Numista\Collection\Domain\Models\Address(), 'countries' => $countries, 'fieldPrefix' => 'shipping_address'])
                                </div>
                            @else
                                {{-- If no addresses, show the form directly and send a hidden input --}}
                                <input type="hidden" name="address_option" value="new">
                                <div>
                                    @include('public.my-account.addresses.partials.form-fields', ['address' => new \Numista\Collection\Domain\Models\Address(), 'countries' => $countries, 'fieldPrefix' => 'shipping_address'])
                                </div>
                            @endif
                        </div>
                    </fieldset>
                </div>
            </section>

            <section class="lg:col-span-5 mt-8 lg:mt-0 rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm h-fit sticky top-8">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('public.order_summary') }}</h2>
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                    @foreach($items as $item)
                        <li class="flex py-4 space-x-4">
                            <img src="{{ $item->images->first()?->url ?? '/images/placeholder.svg' }}" alt="{{ $item->name }}" class="h-16 w-16 rounded-md object-cover">
                            <div class="flex-auto text-sm">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</h3>
                                <p class="text-gray-500 dark:text-gray-400">{{ $cart[$item->id]['quantity'] }} x {{ number_format($item->sale_price, 2, ',', '.') }} €</p>
                            </div>
                            <p class="flex-none text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->sale_price * $cart[$item->id]['quantity'], 2, ',', '.') }} €</p>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-6 space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-base font-medium text-gray-900 dark:text-white">{{ __('panel.Total') }}</dt>
                        <dd class="text-base font-medium text-gray-900 dark:text-white">{{ number_format($total, 2, ',', '.') }} €</dd>
                    </div>
                </dl>
                <div class="mt-6">
                    <button type="submit" class="w-full flex items-center justify-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700">{{ __('public.place_order') }}</button>
                </div>
            </section>
        </div>
    </form>
</div>
<style>
    input[type="radio"]:checked + p + address + span {
        border-color: var(--c-600);
    }
</style>
@endsection