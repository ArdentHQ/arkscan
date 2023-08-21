@props([
    'delegates',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('delegates-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($delegates as $delegate)
        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('delegate-mobile', $delegate->address()) }}"
            :expand-class="Arr::toCssClasses([
                'space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700' => ! $delegate->isResigned(),
            ])"
            expandable
        >
            <x-slot name="header">
                <div class="flex flex-1 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                    <x-tables.rows.mobile.encapsulated.delegates.rank
                        :model="$delegate"
                        class="min-w-[32px]"
                    />

                    <div class="flex flex-1 justify-between items-center pl-3">
                        <x-tables.rows.mobile.encapsulated.delegates.address
                            :model="$delegate"
                            without-clipboard
                            without-label
                        />

                        <div class="flex items-center sm:space-x-3 sm:divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                            <x-tables.rows.mobile.encapsulated.delegates.status
                                :model="$delegate"
                                class="hidden sm:block"
                                without-label
                            />

                            <x-tables.rows.mobile.encapsulated.delegates.vote-link
                                :model="$delegate"
                                class="sm:pl-3"
                            />
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-tables.rows.mobile.encapsulated.delegates.status :model="$delegate" />

            <x-tables.rows.mobile.encapsulated.delegates.number-of-voters
                :model="$delegate"
                class="sm:hidden"
            />

            <x-tables.rows.mobile.encapsulated.delegates.votes :model="$delegate" />

            <x-tables.rows.mobile.encapsulated.delegates.votes-percentage :model="$delegate" />

            @if ($this->showMissedBlocks)
                <x-tables.rows.mobile.encapsulated.delegates.missed-blocks :model="$delegate" />
            @endif
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
