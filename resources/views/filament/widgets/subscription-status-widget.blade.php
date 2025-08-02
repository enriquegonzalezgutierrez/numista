<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Show this content only if subscriptionData is available --}}
        @if ($subscriptionData)
            <div class="flex items-center gap-x-3">
                {{-- Dynamic Icon --}}
                <div @class([
                    'flex-none rounded-full p-3',
                    match ($subscriptionData['color']) {
                        'success' => 'bg-green-500/10',
                        'danger' => 'bg-red-500/10',
                        'warning' => 'bg-yellow-500/10',
                        'gray' => 'bg-gray-500/10',
                    },
                ])>
                    <x-filament::icon
                        :icon="$subscriptionData['icon']"
                        @class([
                            'h-6 w-6',
                            match ($subscriptionData['color']) {
                                'success' => 'text-green-500',
                                'danger' => 'text-red-500',
                                'warning' => 'text-yellow-500',
                                'gray' => 'text-gray-500',
                            },
                        ])
                    />
                </div>

                <div class="flex-1">
                    {{-- Title and Description --}}
                    <h2 class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $subscriptionData['title'] }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $subscriptionData['description'] }}
                    </p>
                </div>

                {{-- Action Button --}}
                @if ($subscriptionData['show_button'])
                    <div class="flex-none">
                        <x-filament::button
                            tag="a"
                            :href="route('my-account.subscription.manage')"
                            :color="$subscriptionData['color']"
                        >
                            {{ $subscriptionData['button_text'] }}
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @else
            {{-- Fallback content if something goes wrong --}}
            <p>Could not load subscription status.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>