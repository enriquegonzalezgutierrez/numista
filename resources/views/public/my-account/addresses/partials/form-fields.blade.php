@props(['address', 'countries', 'fieldPrefix' => ''])

@php
    // Helper function to generate field names with optional prefix
    $fieldName = fn($name) => $fieldPrefix ? "{$fieldPrefix}[{$name}]" : $name;
    // Helper function for retrieving old input data
    $oldValue = fn($name) => old($fieldPrefix ? "{$fieldPrefix}.{$name}" : $name, $address->{$name} ?? '');
@endphp

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

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div>
        <label for="{{ $fieldName('label') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.label') }}</label>
        {{-- THE FIX: Bind the 'required' attribute to the Alpine.js state --}}
        <input type="text" name="{{ $fieldName('label') }}" id="{{ $fieldName('label') }}" value="{{ $oldValue('label') }}" :required="addressOption === 'new'"
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
    </div>
    <div>
        <label for="{{ $fieldName('recipient_name') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.recipient_name') }}</label>
        <input type="text" name="{{ $fieldName('recipient_name') }}" id="{{ $fieldName('recipient_name') }}" value="{{ $oldValue('recipient_name') }}" :required="addressOption === 'new'"
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
    </div>
    <div class="sm:col-span-2">
        <label for="{{ $fieldName('street_address') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.street_address') }}</label>
        <input type="text" name="{{ $fieldName('street_address') }}" id="{{ $fieldName('street_address') }}" value="{{ $oldValue('street_address') }}" :required="addressOption === 'new'"
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
    </div>
    <div>
        <label for="{{ $fieldName('city') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.city') }}</label>
        <input type="text" name="{{ $fieldName('city') }}" id="{{ $fieldName('city') }}" value="{{ $oldValue('city') }}" :required="addressOption === 'new'"
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
    </div>
    <div>
        <label for="{{ $fieldName('postal_code') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.postal_code') }}</label>
        <input type="text" name="{{ $fieldName('postal_code') }}" id="{{ $fieldName('postal_code') }}" value="{{ $oldValue('postal_code') }}" :required="addressOption === 'new'"
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
    </div>
    <div>
        <label for="{{ $fieldName('state') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.state') }}</label>
        <input type="text" name="{{ $fieldName('state') }}" id="{{ $fieldName('state') }}" value="{{ $oldValue('state') }}"
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
    </div>
    <div>
        <label for="{{ $fieldName('country_code') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.country') }}</label>
        <select name="{{ $fieldName('country_code') }}" id="{{ $fieldName('country_code') }}" :required="addressOption === 'new'"
                class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
            @foreach($countries as $code => $name)
                <option value="{{ $code }}" @selected($oldValue('country_code') == $code || (empty($oldValue('country_code')) && $code == 'ES'))>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label for="{{ $fieldName('phone') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.address.phone') }}</label>
        <input type="tel" name="{{ $fieldName('phone') }}" id="{{ $fieldName('phone') }}" value="{{ $oldValue('phone') }}"
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500 disabled:opacity-50">
    </div>
</div>