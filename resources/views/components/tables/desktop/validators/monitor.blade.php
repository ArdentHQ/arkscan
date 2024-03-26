@props([
    'validators',
    'round',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    x-data="TableSorting('header-favorite', 'desc', 'header-order', 'asc')"
    wire:key="{{ Helpers::generateId('validator-monitor', $round) }}"
    class="hidden w-full md:block validator-monitor"
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
                name="tables.validator-monitor.order"
                width="60"
                sorting-id="header-order"
                hide-sorting
            />

            <x-tables.headers.desktop.address
                name="tables.validator-monitor.validator"
                width="190"
            />

            <x-tables.headers.desktop.status
                name="tables.validator-monitor.status"
                breakpoint="md-lg"
                responsive
            />

            <x-tables.headers.desktop.status
                name="tables.validator-monitor.status_time_to_forge"
                class="md-lg:hidden"
                breakpoint="md"
                responsive
            />

            <x-tables.headers.desktop.text
                name="tables.validator-monitor.time_to_forge"
                class="whitespace-nowrap"
                breakpoint="md-lg"
                responsive
            />

            <x-tables.headers.desktop.number
                name="tables.validator-monitor.block_height"
                class="whitespace-nowrap"
            />
        </tr>
    </thead>

    <tbody x-ref="tbody">
        @foreach($validators as $validator)
            <x-ark-tables.row
                x-data="Validator('{{ $validator->publicKey() }}')"
                wire:key="validator-{{ $validator->order() }}-{{ $validator->wallet()->address() }}-{{ $validator->roundNumber() }}"
                ::class="{
                    'validator-monitor-favorite': isFavorite === true,
                }"
            >
                <x-ark-tables.cell ::data-value="isFavorite ? 1 : 0">
                    <x-validators.favorite-toggle :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell data-value="{{ $validator->order() }}">
                    <span class="text-sm font-semibold leading-4.25">
                        {{ $validator->order() }}
                    </span>
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address
                        :model="$validator->wallet()"
                        without-clipboard
                        validator-name-class="md:w-[200px] md-lg:w-auto"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    breakpoint="md-lg"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.validators.monitor.forging-status :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="md-lg:hidden"
                    breakpoint="md"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.validators.monitor.forging-status
                        :model="$validator"
                        with-time
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    breakpoint="md-lg"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.validators.monitor.time-to-forge :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.monitor.block-height :model="$validator" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
