@props([
    'delegates',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('delegate-monitor-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($this->delegates as $delegate)
        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('delegate-mobile', $delegate->wallet()->address()) }}"
            :expand-class="Arr::toCssClasses([
                'space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700' => ! $delegate->wallet()->isResigned(),
            ])"
            expandable
        >
            <x-slot name="header">
                <div class="flex flex-1 min-w-0 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                    <div class="flex items-center">
                        <div class="hidden items-center pr-3 sm:flex">
                            <x-ark-icon name="star" />
                        </div>

                        <span class="text-sm font-semibold leading-4.25 min-w-[32px]">
                            {{ $loop->index + 1 }}
                        </span>
                    </div>

                    <div class="flex flex-1 justify-between items-center pl-3 min-w-0">
                        <x-tables.rows.mobile.encapsulated.delegates.address
                            :model="$delegate->wallet()"
                            class="min-w-0"
                            identity-class="min-w-0"
                            identity-content-class="min-w-0"
                            identity-link-class="pr-2 min-w-0"
                            without-clipboard
                            without-label
                        />

                        <div class="flex items-center sm:space-x-3 h-[21px]">
                            <div class="flex items-center sm:hidden">
                                <x-tables.rows.desktop.encapsulated.delegates.monitor.forging-status
                                    :model="$delegate"
                                    :with-text="false"
                                />
                            </div>

                            <div class="hidden sm:block">
                                <x-tables.rows.desktop.encapsulated.delegates.monitor.forging-status :model="$delegate" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-tables.rows.mobile.encapsulated.delegates.monitor.forging-status
                :model="$delegate"
                class="sm:hidden"
            />

            <x-tables.rows.mobile.encapsulated.delegates.monitor.time-to-forge :model="$delegate" />

            <x-tables.rows.mobile.encapsulated.delegates.monitor.block-height
                :model="$delegate"
                class="sm:w-[8.75rem]"
            />

            <x-tables.rows.mobile.encapsulated.delegates.monitor.favorite
                :model="$delegate"
                class="pt-4 mt-4 border-t sm:hidden border-theme-secondary-300"
            />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
