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