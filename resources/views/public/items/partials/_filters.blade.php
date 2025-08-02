<div x-data>
    <!-- Search Filter -->
    <div class="mb-6">
        <label for="search-desktop" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('public.filter_search_label') }}</label>
        <input 
            type="text" 
            name="search" 
            id="search-desktop" 
            value="{{ request('search') }}" 
            placeholder="{{ __('public.filter_search_placeholder') }}"
            @input.debounce.500ms="window.dispatchEvent(new CustomEvent('update-results'))"
            class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
        >
    </div>

    <!-- Category Filter -->
    @if($categories->isNotEmpty())
    <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('public.filter_categories_title') }}</h3>
        <div class="mt-2 space-y-2">
            @foreach($categories as $category)
                <div class="flex items-center">
                    <input 
                        id="category-{{ $category->id }}" 
                        name="categories[]" 
                        value="{{ $category->id }}" 
                        type="checkbox" 
                        @change="window.dispatchEvent(new CustomEvent('update-results'))"
                        @checked(in_array($category->id, request('categories', [])))
                        class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                    >
                    <label for="category-{{ $category->id }}" class="ml-3 text-sm text-gray-600 dark:text-gray-300">{{ $category->name }}</label>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Dynamic Attribute Filters -->
    @if($filterableAttributes->isNotEmpty())
        @foreach($filterableAttributes as $attribute)
            <div class="mb-6">
                <label for="attribute-{{ $attribute->id }}" class="block text-sm font-medium text-gray-900 dark:text-white">{{ $attribute->name }}</label>
                @if($attribute->type === 'select')
                    <select 
                        id="attribute-{{ $attribute->id }}" 
                        name="attributes[{{ $attribute->id }}]" 
                        @change="window.dispatchEvent(new CustomEvent('update-results'))"
                        class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-teal-500 focus:outline-none focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">{{ __('public.filter_all_option') }}</option>
                        @foreach($attribute->options as $option)
                            <option value="{{ $option->id }}" @selected(request('attributes.'.$attribute->id) == $option->id)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                @else
                    <input 
                        type="{{ $attribute->type === 'number' ? 'number' : 'text' }}"
                        id="attribute-{{ $attribute->id }}"
                        name="attributes[{{ $attribute->id }}]"
                        value="{{ request('attributes.'.$attribute->id) }}"
                        @input.debounce.500ms="window.dispatchEvent(new CustomEvent('update-results'))"
                        class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                @endif
            </div>
        @endforeach
    @endif
</div>