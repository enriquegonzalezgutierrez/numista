@props(['categories', 'filterableAttributes', 'isMobile' => false])

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
            {{-- THE FIX: Instead of submitting, dispatch a custom event --}}
            @input.debounce.500ms="$dispatch('update-results')"
            class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        >
    </div>

    <!-- Category Filter -->
    @if($categories->isNotEmpty())
    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('public.filter_categories_title') }}</h3>
        <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
            @foreach($categories as $category)
                <div class="flex items-center">
                    <input 
                        id="category-{{ $category->id }}-{{ $isMobile ? 'mobile' : 'desktop' }}" 
                        name="categories[]" 
                        value="{{ $category->id }}" 
                        type="checkbox" 
                        @checked(in_array($category->id, request('categories', []))) 
                        {{-- THE FIX: Dispatch the event on change --}}
                        @change="$dispatch('update-results')"
                        class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                    >
                    <label for="category-{{ $category->id }}-{{ $isMobile ? 'mobile' : 'desktop' }}" class="ml-3 text-sm text-gray-600 dark:text-gray-400">{{ $category->name }}</label>
                </div>
            @endforeach
        </div>
    </div>
    @endif

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
                        {{-- THE FIX: Dispatch the event on change --}}
                        @change="$dispatch('update-results')"
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
                        {{-- THE FIX: Dispatch the event on input --}}
                        @input.debounce.500ms="$dispatch('update-results')"
                        class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                @endif
            </div>
        @endforeach
    @endif
</div>