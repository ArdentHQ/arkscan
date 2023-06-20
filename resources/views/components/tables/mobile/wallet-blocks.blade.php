@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('blocks-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($blocks as $block)
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.headers.mobile.encapsulated.block-height :model="$block" />

                <x-tables.rows.mobile.encapsulated.age
                    :model="$block"
                    class="leading-[17px]"
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.transaction-count :model="$block" />

            <x-tables.rows.mobile.encapsulated.volume :model="$block" />

            <x-tables.rows.mobile.encapsulated.reward :model="$block" />

            <x-tables.rows.mobile.encapsulated.value :model="$block" />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
