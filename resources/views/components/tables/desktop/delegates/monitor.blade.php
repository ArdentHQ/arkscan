@props([
    'delegates',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    x-data="TableSorting('header-favorite', 'desc', 'header-order', 'asc')"
    wire:key="{{ Helpers::generateId('delegate-monitor') }}"
    class="hidden w-full md:block delegate-monitor"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.text
                width="20"
                sorting-id="header-favorite"
                hide-sorting
            />

            <x-tables.headers.desktop.text
                name="tables.delegate-monitor.order"
                width="60"
                sorting-id="header-order"
                hide-sorting
            />

            <x-tables.headers.desktop.address name="tables.delegate-monitor.delegate" />

            <x-tables.headers.desktop.status
                name="tables.delegate-monitor.status"
                breakpoint="md-lg"
                responsive
            />

            <x-tables.headers.desktop.status
                name="tables.delegate-monitor.status_time_to_forge"
                class="md-lg:hidden"
                breakpoint="md"
                responsive
            />

            <x-tables.headers.desktop.text
                name="tables.delegate-monitor.time_to_forge"
                class="whitespace-nowrap"
                breakpoint="md-lg"
                responsive
            />

            <x-tables.headers.desktop.number
                name="tables.delegate-monitor.block_height"
                class="whitespace-nowrap"
            />
        </tr>
    </thead>

    <tbody x-ref="tbody">
        @foreach($delegates as $delegate)
            <x-ark-tables.row
                x-data="{
                    isFavorite: {{ $delegate->isFavorite() ? 'true' : 'false' }},
                }"
                wire:key="delegate-{{ $delegate->wallet()->address() }}"
                ::class="{
                    'delegate-monitor-favorite': isFavorite === true,
                }"
            >
                <x-ark-tables.cell
                    ::data-value="isFavorite ? 1 : 0"
                    :data-value="$delegate->isFavorite() ? 1 : 0"
                >
                    <x-delegates.favorite-toggle
                        :model="$delegate"
                        without-data
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell data-value="{{ $delegate->order() }}">
                    <span class="text-sm font-semibold leading-4.25">
                        {{ $delegate->order() }}
                    </span>
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        :model="$delegate->wallet()"
                        without-clipboard
                        delegate-name-class="md:w-[100px] md-lg:w-auto"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    breakpoint="md-lg"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.delegates.monitor.forging-status :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="md-lg:hidden"
                    breakpoint="md"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.delegates.monitor.forging-status
                        :model="$delegate"
                        with-time
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    breakpoint="md-lg"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.delegates.monitor.time-to-forge :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.monitor.block-height :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
