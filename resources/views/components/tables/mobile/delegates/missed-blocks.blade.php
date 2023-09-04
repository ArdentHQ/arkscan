@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('missed-blocks-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($blocks as $block)
        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('block-mobile', $block->timestamp()) }}"
            expandable
        >
            <x-slot name="header">
                <div class="flex flex-1 justify-between">
                    <x-tables.headers.mobile.encapsulated.block-height
                        :model="$block"
                        class="font-semibold sm:flex-1 text-theme-secondary-900 dark:text-theme-dark-50"
                        without-link
                    />

                    <x-tables.rows.mobile.encapsulated.delegates.address
                        :model="$block->delegate()"
                        class="hidden sm:block sm:flex-1"
                        without-label
                    />

                    <x-tables.rows.mobile.encapsulated.age
                        :model="$block"
                        class="sm:text-right leading-4.25 sm:min-w-[110px]"
                    />
                </div>
            </x-slot>

            <x-tables.rows.mobile.encapsulated.delegates.address
                :model="$block->delegate()"
                class="sm:hidden"
            />

            <x-tables.rows.mobile.encapsulated.delegates.number-of-voters
                :model="$block->delegate()"
                class="sm:flex-1"
            />

            <div class="sm:flex-1">
                <x-tables.rows.mobile.encapsulated.delegates.votes :model="$block->delegate()" />
            </div>

            <div class="sm:flex sm:justify-end sm:min-w-[110px]">
                <x-tables.rows.mobile.encapsulated.delegates.votes-percentage :model="$block->delegate()" />
            </div>
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
