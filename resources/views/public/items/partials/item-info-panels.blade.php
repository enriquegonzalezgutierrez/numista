{{-- This partial contains the content panels for the tabbed interface on desktop. --}}
<div x-show="activeTab === 'description'" x-cloak>
    <div class="prose prose-sm mt-4 text-gray-600 dark:text-gray-300">
        <p>{{ $item->description }}</p>
    </div>
</div>

@if($item->attributes->isNotEmpty())
<div x-show="activeTab === 'details'" x-cloak>
    <div class="mt-4">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
            @foreach($item->attributes->sortBy('name') as $attribute)
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">
                        @php
                            $labelKey = 'panel.attribute_name_' . strtolower(str_replace(' ', '_', $attribute->name));
                            echo trans()->has($labelKey) ? __($labelKey) : $attribute->name;
                        @endphp
                    </dt>
                    <dd class="text-gray-900 dark:text-white mt-1">
                        @php
                            $displayValue = $attribute->pivot->value; // Default value

                            if ($attribute->type === 'select' && $attribute->pivot->attribute_option_id) {
                                $option = $attribute->options->firstWhere('id', $attribute->pivot->attribute_option_id);
                                
                                if ($option) {
                                    $attributeKey = strtolower(str_replace(' ', '_', $attribute->name));
                                    $translationKey = "item.options.{$attributeKey}.{$option->value}";
                                    $displayValue = trans()->has($translationKey) ? __($translationKey) : $option->value;
                                }
                            }
                            
                            echo e($displayValue); // THE FIX: Use echo and escape the output
                        @endphp
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>
@endif

@if($item->categories->isNotEmpty())
<div x-show="activeTab === 'categories'" x-cloak>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach($item->categories as $category)
            <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-200 dark:ring-gray-600">
                {{ $category->name }}
            </span>
        @endforeach
    </div>
</div>
@endif