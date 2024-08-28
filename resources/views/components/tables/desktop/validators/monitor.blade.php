@props([
    'validators',
    'round',
    'overflowValidators',
    'noResultsMessage' => null,
])

<div>
    <x-tables.encapsulated-table
        x-data="TableSorting('header-favorite', 'desc', 'header-order', 'asc')"
        wire:key="{{ Helpers::generateId('validator-monitor', $round) }}"
        class="hidden w-full md:block validator-monitor"
        :with-bottom-border="count($overflowValidators) === 0"
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
                    class="w-[374px]"
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
                <x-tables.rows.desktop.validators.monitor-row :validator="$validator" />
            @endforeach
        </tbody>
    </x-ark-tables.table>

    @if (count($overflowValidators) > 0)
        <x-tables.encapsulated-table
            x-data="{}"
            wire:key="{{ Helpers::generateId('validator-monitor', $round) }}-overflow"
            class="hidden w-full md:block validator-monitor"
            :with-header="false"
            :rounded="false"
        >
            <tbody x-ref="tbody">
                @foreach($overflowValidators as $validator)
                    <x-tables.rows.desktop.validators.monitor-row :validator="$validator" />
                @endforeach
            </tbody>
        </x-ark-tables.table>
    @endif
</div>
