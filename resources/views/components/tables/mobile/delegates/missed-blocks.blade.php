@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('missed-blocks-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($blocks as $block)
        <x-tables.rows.mobile expandable>
            <x-slot name="header">
                <div class="flex flex-1 justify-between">
                    <x-tables.headers.mobile.encapsulated.block-height
                        :model="$block"
                        class="font-semibold sm:flex-1 text-theme-secondary-900 dark:text-theme-dark-50"
                        without-link
                    />

                    <x-tables.rows.mobile.encapsulated.delegates.address
                        :model="$block->delegate()"
                        class="hidden sm:block"
                        without-label
                    />

                    <x-tables.rows.mobile.encapsulated.age
                        :model="$block"
                        class="sm:flex-1 sm:text-right leading-[17px]"
                    />
                </div>
            </x-slot>

            <x-tables.rows.mobile.encapsulated.delegates.address
                :model="$block->delegate()"
                class="sm:hidden"
            />

            <x-tables.rows.mobile.encapsulated.delegates.number-of-voters :model="$block->delegate()" />

            <x-tables.rows.mobile.encapsulated.delegates.votes :model="$block->delegate()" />

            <x-tables.rows.mobile.encapsulated.delegates.votes-percentage :model="$block->delegate()" />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
