@props([
    'validators',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('validators-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($validators as $validator)
        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('validator-mobile', $validator->address()) }}"
            :expand-class="Arr::toCssClasses(['space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700' => ! $validator->isResigned(),
            ])"
            expandable
            :content-class="config('arkscan.arkconnect.enabled') ? '!pb-0 sm:!pb-3' : ''"
        >
            <x-slot name="header">
                <div class="flex flex-1 min-w-0 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
                    <x-tables.rows.mobile.encapsulated.validators.rank
                        :model="$validator"
                        class="min-w-[32px]"
                    />

                    <div class="flex flex-1 justify-between items-center pl-3 min-w-0">
                        <x-tables.rows.mobile.encapsulated.validators.address
                            :model="$validator"
                            class="min-w-0"
                            identity-class="min-w-0"
                            identity-content-class="min-w-0"
                            identity-link-class="pr-2 min-w-0"
                            without-clipboard
                            without-label
                        />

                        <div class="flex items-center">
                            <x-tables.rows.mobile.encapsulated.validators.status
                                :model="$validator"
                                class="hidden sm:block"
                                without-label
                            />

                            <x-tables.rows.mobile.encapsulated.validators.vote-link
                                :model="$validator"
                                class="sm:pl-3 sm:ml-3 sm:border-l border-theme-secondary-300 dark:border-theme-dark-700"
                            />
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-tables.rows.mobile.encapsulated.validators.status :model="$validator" />

            <x-tables.rows.mobile.encapsulated.validators.number-of-voters
                :model="$validator"
                class="sm:hidden"
            />

            <x-tables.rows.mobile.encapsulated.validators.votes :model="$validator" />

            <x-tables.rows.mobile.encapsulated.validators.votes-percentage :model="$validator" />

            <x-tables.rows.mobile.encapsulated.validators.missed-blocks :model="$validator" />

            @if (config('arkscan.arkconnect.enabled'))
                <div class="sm:hidden">
                    <x-tables.rows.mobile.encapsulated.validators.voting-for :model="$validator" />
                </div>
            @endif
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
