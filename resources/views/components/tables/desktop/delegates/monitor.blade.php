@props([
    'delegates',
    'round',
    'overflowDelegates',
    'noResultsMessage' => null,
])

<div>
    <x-tables.encapsulated-table
        id="delegate-monitor"
        x-data="TableSorting('delegate-monitor', 'delegates.monitor', 'header-favorite', 'desc', 'header-order', 'asc')"
        wire:key="{{ Helpers::generateId('delegate-monitor', $round) }}"
        class="hidden w-full md:block delegate-monitor"
        :with-bottom-border="count($overflowDelegates) === 0"
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

                <x-tables.headers.desktop.address
                    name="tables.delegate-monitor.delegate"
                    width="190"
                />

                <x-tables.headers.desktop.status
                    name="tables.delegate-monitor.status"
                    breakpoint="md-lg"
                    responsive
                    class="w-[374px]"
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

        <tbody>
            @foreach($delegates as $delegate)
                <x-tables.rows.desktop.delegates.monitor-row :delegate="$delegate" />
            @endforeach
        </tbody>
    </x-ark-tables.table>

    @if (count($overflowDelegates) > 0)
        <x-tables.encapsulated-table
            x-data="{}"
            wire:key="{{ Helpers::generateId('delegate-monitor', $round) }}-overflow-{{ microtime(true) }}"
            class="hidden w-full md:block delegate-monitor"
            :with-header="false"
            :rounded="false"
        >
            <tbody>
                @foreach($overflowDelegates as $delegate)
                    <x-tables.rows.desktop.delegates.monitor-row :delegate="$delegate" />
                @endforeach
            </tbody>
        </x-ark-tables.table>
    @endif
</div>
