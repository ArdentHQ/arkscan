@props([
    'delegates',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('transactions') }}"
    class="hidden w-full rounded-t-none md:block"
    :rounded="false"
    :paginator="$delegates"
    :no-results-message="$noResultsMessage"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.text
                name="tables.delegates.rank"
                width="70"
            />

            <x-tables.headers.desktop.address name="tables.delegates.delegate" />

            <x-tables.headers.desktop.status name="tables.delegates.status" />

            <x-tables.headers.desktop.number name="tables.delegates.no_of_voters" />

            <x-tables.headers.desktop.number
                name="tables.delegates.votes"
                :name-properties="['currency' => Network::currency()]"
                responsive
            />

            <x-tables.headers.desktop.number
                name="tables.delegates.percentage"
                responsive
                breakpoint="lg"
            >
                <x-ark-info :tooltip="trans('tables.delegates.info.percentage')" />
            </x-tables.headers.desktop.number>

            @if ($this->showMissedBlocks)
                <x-tables.headers.desktop.number name="tables.delegates.missed_blocks" />
            @endif

            <x-tables.headers.desktop.text width="70" />
        </tr>
    </thead>

    <tbody>
        @foreach($delegates as $delegate)
            <x-ark-tables.row wire:key="delegate-{{ $delegate->address() }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.delegates.rank :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        :model="$delegate"
                        without-clipboard
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.delegates.delegate-status :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.number-of-voters :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.delegates.votes :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                    breakpoint="lg"
                >
                    <x-tables.rows.desktop.encapsulated.delegates.votes-percentage :model="$delegate" />
                </x-ark-tables.cell>

                @if ($this->showMissedBlocks)
                    <x-ark-tables.cell class="text-right">
                        <x-tables.rows.desktop.encapsulated.delegates.missed-blocks :model="$delegate" />
                    </x-ark-tables.cell>
                @endif

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.vote-link :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
