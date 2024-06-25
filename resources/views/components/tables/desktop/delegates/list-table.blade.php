@props([
    'delegates',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('delegates') }}"
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
                sorting-id="rank"
                livewire-sort
            />

            <x-tables.headers.desktop.address
                name="tables.delegates.delegate"
                sorting-id="name"
                livewire-sort
            />

            <x-tables.headers.desktop.status name="tables.delegates.status" />

            <x-tables.headers.desktop.number
                name="tables.delegates.no_of_voters"
                sorting-id="no_of_voters"
                class="whitespace-nowrap"
                livewire-sort
            />

            <x-tables.headers.desktop.number
                name="tables.delegates.votes"
                :name-properties="['currency' => Network::currency()]"
                class="whitespace-nowrap"
                responsive
                sorting-id="votes"
                livewire-sort
            />

            <x-tables.headers.desktop.number
                name="tables.delegates.percentage"
                responsive
                breakpoint="lg"
                sorting-id="percentage_votes"
                livewire-sort
                class="!py-2.5"
                :tooltip="trans('tables.delegates.info.percentage')"
            />

            <x-tables.headers.desktop.number
                name="tables.delegates.missed_blocks"
                class="whitespace-nowrap"
                sorting-id="missed_blocks"
                livewire-sort
            />

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
                    <div class="flex items-center space-x-2">
                        <x-tables.rows.desktop.encapsulated.address
                            :model="$delegate"
                            without-clipboard
                            delegate-name-class="md:w-[100px] md-lg:w-auto"
                        />

                        @if (config('arkscan.arkconnect.enabled'))
                            <div
                                x-data="{}"
                                x-show="votingForAddress === '{{ $delegate->address() }}'"
                            >
                                <div data-tippy-content="@lang('pages.delegates.arkconnect.voting_for_tooltip')">
                                    <x-ark-icon
                                        name="check-mark-box"
                                        size="sm"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
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

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.missed-blocks :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.delegates.vote-link :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
