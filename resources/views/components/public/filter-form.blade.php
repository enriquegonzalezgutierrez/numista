@props(['filterableAttributes', 'isMobile' => false])

{{-- THE FIX: We listen to the `input` event on text fields, and `change` on selects/checkboxes --}}
<div x-data>
    <!-- Search Filter -->
    <div class="mb-6">
        <label for="search-{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.filter_search_label') }}</label>
        <input 
            type="text" 
            name="search" 
            id="search-{{ $isMobile ? 'mobile' : 'desktop' }}" 
            value="{{ request('search') }}" 
            placeholder="{{ __('public.filter_search_placeholder') }}"
            @input.debounce.500ms="$el.closest('form').submit()"
            class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        >
    </div>

    <!-- Dynamic Attribute Filters -->
    @if($filterableAttributes->isNotEmpty())
        @foreach($filterableAttributes as $attribute)
            <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                @php
                    $labelKey = 'panel.attribute_name_' . strtolower(str_replace(' ', '_', $attribute->name));
                @endphp
                <label for="attribute-{{ $attribute->id }}-{{ $isMobile ? 'mobile' : 'desktop' }}" class="text-lg font-medium text-gray-900 dark:text-white mb-2 block">
                    {{ trans()->has($labelKey) ? __($labelKey) : $attribute->name }}
                </label>
                
                @if($attribute->type === 'select' && $attribute->options->isNotEmpty())
                    <select 
                        name="attributes[{{ $attribute->id }}]" 
                        id="attribute-{{ $attribute->id }}-{{ $isMobile ? 'mobile' : 'desktop' }}"
                        @change="$el.closest('form').submit()"
                        class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">{{ __('public.filter_all_option') }}</option>
                        @foreach($attribute->options as $option)
                            @php
                                $attributeKey = strtolower(str_replace(' ', '_', $attribute->name));
                                $translationKey = "item.options.{$attributeKey}.{$option->value}";
                            @endphp
                            <option value="{{ $option->id }}" @selected(request('attributes.'.$attribute->id) == $option->id)>
                                {{ trans()->has($translationKey) ? __($translationKey) : $option->value }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input 
                        type="text" 
                        name="attributes[{{ $attribute->id }}]" 
                        id="attribute-{{ $attribute->id }}-{{ $isMobile ? 'mobile' : 'desktop' }}" 
                        value="{{ request('attributes.'.$attribute->id) }}"
                        @input.debounce.500ms="$el.closest('form').submit()"
                        class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                @endif
            </div>
        @endforeach
    @endif
</div>