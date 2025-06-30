@props(['categories', 'isMobile' => false])

{{-- This form is now just for structure, the real submission is handled by Alpine --}}
<form x-ref="filterForm{{ $isMobile ? 'Mobile' : 'Desktop' }}" action="{{ route('public.items.index') }}" method="GET">
    {{-- Search Field --}}
    <div>
        <label for="search-{{ $isMobile ? 'mobile' : 'desktop' }}" class="block text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('public.filter_search_label') }}</label>
        <div class="relative mt-1">
            <input type="text" name="search" id="search-{{ $isMobile ? 'mobile' : 'desktop' }}" value="{{ request('search') }}"
                   class="block w-full rounded-md border-slate-300 py-2 pl-3 pr-10 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white sm:text-sm" 
                   placeholder="{{ __('public.filter_search_placeholder') }}">
            <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-slate-400 hover:text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
            </button>
        </div>
    </div>

    {{-- Category Filter --}}
    @if($categories->isNotEmpty())
    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mt-6">
        <h3 class="text-base font-medium text-slate-900 dark:text-white">{{ __('public.filter_categories_title') }}</h3>
        <div class="mt-4 space-y-4">
            @foreach($categories as $category)
                <div class="flex items-center">
                    <input id="category-{{ $category->id }}-{{ $isMobile ? 'mobile' : 'desktop' }}" name="categories[]" value="{{ $category->id }}" type="checkbox"
                           @if(in_array($category->id, request('categories', []))) checked @endif
                           class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <label for="category-{{ $category->id }}-{{ $isMobile ? 'mobile' : 'desktop' }}" class="ml-3 text-sm text-slate-600 dark:text-slate-300">{{ $category->name }}</label>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</form>