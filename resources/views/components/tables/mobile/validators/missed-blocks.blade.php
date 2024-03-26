@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('missed-blocks-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($blocks as $block)
        @php ($validator = $block->validator())

        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('block-mobile', $block->timestamp()) }}"
            :expandable="$validator !== null"
        >
            <x-slot name="header">
                <div class="flex flex-1 justify-between">
                    <x-tables.headers.mobile.encapsulated.block-height
                        :model="$block"
                        class="font-semibold sm:flex-1 text-theme-secondary-900 dark:text-theme-dark-50"
                        without-link
                    />

                    @if ($validator)
                        <x-tables.rows.mobile.encapsulated.validators.address
                            :model="$validator"
                            class="hidden sm:block sm:flex-1"
                            without-label
                        />
                    @endif

                    <x-tables.rows.mobile.encapsulated.age
                        :model="$block"
                        class="sm:text-right leading-4.25 sm:min-w-[110px]"
                    />
                </div>
            </x-slot>

            @if ($validator)
                <x-tables.rows.mobile.encapsulated.validators.address
                    :model="$validator"
                    class="sm:hidden"
                />

                <x-tables.rows.mobile.encapsulated.validators.number-of-voters
                    :model="$validator"
                    class="sm:flex-1"
                />

                <div class="sm:flex-1">
                    <x-tables.rows.mobile.encapsulated.validators.votes :model="$validator" />
                </div>

                <div class="sm:flex sm:justify-end sm:min-w-[110px]">
                    <x-tables.rows.mobile.encapsulated.validators.votes-percentage :model="$validator" />
                </div>
            @endif
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
