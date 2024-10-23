@props([
    'validators',
    'round',
    'noResultsMessage' => null,
])

<div
    id="validator-monitor-mobile"
    x-data="MobileSorting('validator-monitor-mobile', 'validators.monitor', 'favorite', 'desc', 'order', 'asc')"
    wire:key="{{ Helpers::generateId('validator-monitor-mobile', $round) }}"
>
    <div class="table-container">
        <x-tables.mobile.includes.encapsulated
            class="validator-monitor-mobile"
            :no-results-message="$noResultsMessage"
        >
            @foreach ($validators as $validator)
                <x-tables.rows.mobile
                    x-data="Validator('{{ $validator->address() }}', {
                        isExpanded: false,
                    })"
                    wire:key="{{ Helpers::generateId('validator-mobile', $validator->order(), $validator->wallet()->address(), $validator->roundNumber(), microtime(true)) }}"
                    :expand-class="Arr::toCssClasses(['space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700' => ! $validator->wallet()->isResigned(),
                    ])"
                    ::class="{
                        'validator-monitor-favorite': isFavorite === true,
                    }"
                    expandable
                    ::data-favorite="isFavorite ? 1 : 0"
                    :data-order="$validator->order()"
                >
                    <x-slot name="header">
                        <div class="flex flex-1 min-w-0 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                            <div class="flex items-center">
                                <div class="hidden items-center pr-3 sm:flex">
                                    <x-validators.favorite-toggle
                                        :model="$validator"
                                        on-click="toggleFavorites"
                                    />
                                </div>

                                <span class="text-sm font-semibold leading-4.25 min-w-[32px] dark:text-theme-dark-200">
                                    {{ $validator->order() }}
                                </span>
                            </div>

                            <div class="flex flex-1 justify-between items-center pl-3 min-w-0">
                                <div class="flex items-center">
                                    <x-tables.rows.mobile.encapsulated.validators.address
                                        :model="$validator->wallet()"
                                        class="min-w-0"
                                        identity-class="min-w-0"
                                        identity-content-class="min-w-0"
                                        identity-link-class="pr-2 min-w-0"
                                        without-clipboard
                                        without-label
                                    />

                                    <x-validators.missed-warning :validator="$validator->wallet()" />
                                </div>

                                <div class="flex items-center sm:space-x-3 h-[21px]">
                                    <div class="flex items-center sm:hidden">
                                        <x-tables.rows.desktop.encapsulated.validators.monitor.forging-status
                                            :model="$validator"
                                            :with-text="false"
                                        />
                                    </div>

                                    <div class="hidden sm:block">
                                        <x-tables.rows.desktop.encapsulated.validators.monitor.forging-status :model="$validator" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-tables.rows.mobile.encapsulated.validators.monitor.forging-status
                        :model="$validator"
                        class="sm:hidden"
                    />

                    <x-tables.rows.mobile.encapsulated.validators.monitor.time-to-forge :model="$validator" />

                    <x-tables.rows.mobile.encapsulated.validators.monitor.block-height
                        :model="$validator"
                        class="sm:w-[8.75rem]"
                    />

                    <x-tables.rows.mobile.encapsulated.validators.monitor.favorite
                        :model="$validator"
                        class="pt-4 mt-4 border-t sm:hidden border-theme-secondary-300 dark:border-theme-dark-700"
                        on-click="toggleFavorites"
                    />
                </x-tables.rows.mobile>
            @endforeach
        </x-tables.mobile.includes.encapsulated>
    </div>
</div>
