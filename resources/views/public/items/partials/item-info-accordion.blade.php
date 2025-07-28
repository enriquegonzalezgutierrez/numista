{{-- This partial contains the accordion interface for mobile. --}}
<div>
    <h3 class="border-b border-gray-200 dark:border-gray-700">
        <button @click="activeTab = activeTab === 'description' ? null : 'description'" type="button" class="flex w-full items-center justify-between py-6 text-left text-gray-400">
            <span class="text-base font-medium text-gray-900 dark:text-white">{{ __('public.description') }}</span>
            <span class="ml-6 flex items-center">
                <svg :class="{ 'rotate-180': activeTab === 'description', '-rotate-0': activeTab !== 'description' }" class="h-6 w-6 transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
            </span>
        </button>
    </h3>
    <div x-show="activeTab === 'description'" x-collapse>
        <div class="prose prose-sm pb-6 text-gray-600 dark:text-gray-300">
            <p>{{ $item->description }}</p>
        </div>
    </div>
</div>

@if($item->attributes->isNotEmpty())
<div>
    <h3 class="border-b border-gray-200 dark:border-gray-700">
        <button @click="activeTab = activeTab === 'details' ? null : 'details'" type="button" class="flex w-full items-center justify-between py-6 text-left text-gray-400">
            <span class="text-base font-medium text-gray-900 dark:text-white">{{ __('public.item_details') }}</span>
            <span class="ml-6 flex items-center">
                <svg :class="{ 'rotate-180': activeTab === 'details', '-rotate-0': activeTab !== 'details' }" class="h-6 w-6 transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
            </span>
        </button>
    </h3>
    <div x-show="activeTab === 'details'" x-collapse>
        <div class="py-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                @foreach($item->attributes->sortBy('name') as $attribute)
                    <div>
                        <dt class="font-medium text-gray-500 dark:text-gray-400">
                            @php($key = 'panel.attribute_name_' . strtolower(str_replace(' ', '_', $attribute->name)))
                            {{ trans()->has($key) ? __($key) : $attribute->name }}
                        </dt>
                        <dd class="text-gray-900 dark:text-white mt-1">
                            @if($attribute->type === 'select' && strtolower($attribute->name) === 'grade')
                                {{ __("item.options.grade.{$attribute->pivot->value}") ?? $attribute->pivot->value }}
                            @else
                                {{ $attribute->pivot->value }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</div>
@endif

@if($item->categories->isNotEmpty())
<div>
    <h3 class="border-b border-gray-200 dark:border-gray-700">
        <button @click="activeTab = activeTab === 'categories' ? null : 'categories'" type="button" class="flex w-full items-center justify-between py-6 text-left text-gray-400">
            <span class="text-base font-medium text-gray-900 dark:text-white">{{ __('public.categories') }}</span>
            <span class="ml-6 flex items-center">
                <svg :class="{ 'rotate-180': activeTab === 'categories', '-rotate-0': activeTab !== 'categories' }" class="h-6 w-6 transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
            </span>
        </button>
    </h3>
    <div x-show="activeTab === 'categories'" x-collapse>
        <div class="py-6 flex flex-wrap gap-2">
            @foreach($item->categories as $category)
                <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-200 dark:ring-gray-600">
                    {{ $category->name }}
                </span>
            @endforeach
        </div>
    </div>
</div>
@endif