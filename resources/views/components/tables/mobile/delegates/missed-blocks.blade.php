@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('missed-blocks-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($blocks as $block)
        @php ($delegate = $block->delegate())

        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('block-mobile', $block->timestamp()) }}"
            :expandable="$delegate !== null"
        >
            <x-slot name="header">
                <div class="flex flex-1 justify-between">
                    <x-tables.headers.mobile.encapsulated.block-height
                        :model="$block"
                        class="font-semibold sm:flex-1 text-theme-secondary-900 dark:text-theme-dark-50"
                        without-link
                    />

                    @if ($delegate)
                        <x-tables.rows.mobile.encapsulated.delegates.address
                            :model="$delegate"
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

            @if ($delegate)
                <x-tables.rows.mobile.encapsulated.delegates.address
                    :model="$delegate"
                    class="sm:hidden"
                />

                <x-tables.rows.mobile.encapsulated.delegates.number-of-voters
                    :model="$delegate"
                    class="sm:flex-1"
                />

                <div class="sm:flex-1">
                    <x-tables.rows.mobile.encapsulated.delegates.votes :model="$delegate" />
                </div>

                <div class="sm:flex sm:justify-end sm:min-w-[110px]">
                    <x-tables.rows.mobile.encapsulated.delegates.votes-percentage :model="$delegate" />
                </div>
            @endif
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
