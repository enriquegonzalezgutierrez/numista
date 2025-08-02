<fieldset x-data="{ selectedAddressId: '{{ $addresses->first()?->id }}' }"> {{-- THE FIX: Initialize Alpine state --}}
    <legend class="sr-only">{{ __('public.shipping_address') }}</legend>
    <div class="space-y-4">
        @if($addresses->isNotEmpty())
            <div class="flex items-center">
                <input id="address_option_existing" name="address_option" type="radio" value="existing" x-model="addressOption" class="h-4 w-4 border-gray-300 text-teal-600 focus:ring-teal-500">
                <label for="address_option_existing" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('public.address.use_saved') }}</label>
            </div>

            <div x-show="addressOption === 'existing'" x-collapse class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach($addresses as $address)
                    <label for="address-{{ $address->id }}" 
                           class="relative block cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none ring-teal-500"
                           :class="{ 'bg-teal-50 dark:bg-teal-900/20 border-teal-300 dark:border-teal-700': selectedAddressId == '{{ $address->id }}' && addressOption === 'existing', 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600': !(selectedAddressId == '{{ $address->id }}' && addressOption === 'existing') }">
                        
                        <input type="radio" 
                               name="selected_address_id" 
                               id="address-{{ $address->id }}" 
                               value="{{ $address->id }}" 
                               x-model="selectedAddressId" {{-- THE FIX: Bind to Alpine state --}}
                               :disabled="addressOption !== 'existing'"
                               class="sr-only" 
                               aria-labelledby="address-{{ $address->id }}-label" 
                               aria-describedby="address-{{ $address->id }}-description">
                        
                        <p id="address-{{ $address->id }}-label" class="text-sm font-medium text-gray-900 dark:text-white">{{ $address->label }}</p>
                        <address id="address-{{ $address->id }}-description" class="mt-1 not-italic text-sm text-gray-500 dark:text-gray-400">
                            {{ $address->recipient_name }}<br>
                            {{ $address->street_address }}<br>
                            {{ $address->postal_code }} {{ $address->city }}
                        </address>
                        
                        {{-- Visual indicator for selection --}}
                        <span class="pointer-events-none absolute -inset-px rounded-lg border-2" 
                              :class="{ 'border-teal-500': selectedAddressId == '{{ $address->id }}' && addressOption === 'existing', 'border-transparent': !(selectedAddressId == '{{ $address->id }}' && addressOption === 'existing') }"
                              aria-hidden="true"></span>
                    </label>
                @endforeach
            </div>

            <div class="flex items-center">
                <input id="address_option_new" name="address_option" type="radio" value="new" x-model="addressOption" class="h-4 w-4 border-gray-300 text-teal-600 focus:ring-teal-500">
                <label for="address_option_new" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('public.address.add_new') }}</label>
            </div>

            <div x-show="addressOption === 'new'" x-collapse>
                @include('public.my-account.addresses.partials.form-fields', [
                    'address' => new \Numista\Collection\Domain\Models\Address(),
                    'countries' => $countries,
                    'fieldPrefix' => 'shipping_address',
                    'disabled' => "addressOption !== 'new'"
                ])
            </div>
        @else
            <input type="hidden" name="address_option" value="new">
            <div>
                @include('public.my-account.addresses.partials.form-fields', [
                    'address' => new \Numista\Collection\Domain\Models\Address(),
                    'countries' => $countries,
                    'fieldPrefix' => 'shipping_address'
                ])
            </div>
        @endif
    </div>
</fieldset>