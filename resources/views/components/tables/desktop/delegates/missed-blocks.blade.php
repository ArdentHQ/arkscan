@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('missed-blocks') }}"
    class="hidden w-full rounded-t-none md:block"
    :rounded="false"
    :paginator="$blocks"
    :no-results-message="$noResultsMessage"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.id
                name="tables.blocks.height"
                class="whitespace-nowrap"
                width="200"
            />

            <x-tables.headers.desktop.text
                name="tables.blocks.age"
                breakpoint="md-lg"
                responsive
            />

            <x-tables.headers.desktop.address name="tables.missed-blocks.delegate" />

            <x-tables.headers.desktop.number name="tables.missed-blocks.no_of_voters" />

            <x-tables.headers.desktop.number
                name="tables.missed-blocks.votes"
                :name-properties="['currency' => Network::currency()]"
            />

            <x-tables.headers.desktop.number name="tables.missed-blocks.percentage">
                <x-ark-info :tooltip="trans('tables.missed-blocks.info.percentage')" />
            </x-tables.headers.desktop.number>
        </tr>
    </thead>

    <tbody>
        @foreach($blocks as $block)
            <x-ark-tables.row wire:key="block-{{ $block->address() }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.block-height
                        :model="$block"
                        class="font-semibold"
                        without-link
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell responsive breakpoint="md-lg">
                    <x-tables.rows.desktop.encapsulated.age :model="$block" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        :model="$block"
                        without-clipboard
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.number-of-voters
                        :model="$block->delegate()"
                        without-breakdown
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.votes :model="$block->delegate()" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.votes-percentage :model="$block->delegate()" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>