@props([
    'validators',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('validators') }}"
    class="hidden w-full rounded-t-none md:block"
    :rounded="false"
    :paginator="$validators"
    :no-results-message="$noResultsMessage"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.text
                name="tables.validators.rank"
                width="70"
                sorting-id="rank"
                livewire-sort
            />

            <x-tables.headers.desktop.address
                name="tables.validators.validator"
                sorting-id="name"
                livewire-sort
            />

            <x-tables.headers.desktop.status name="tables.validators.status" />

            <x-tables.headers.desktop.number
                name="tables.validators.no_of_voters"
                sorting-id="no_of_voters"
                class="whitespace-nowrap"
                livewire-sort
            />

            <x-tables.headers.desktop.number
                name="tables.validators.votes"
                :name-properties="['currency' => Network::currency()]"
                class="whitespace-nowrap"
                responsive
                sorting-id="votes"
                livewire-sort
            />

            <x-tables.headers.desktop.number
                name="tables.validators.percentage"
                responsive
                breakpoint="lg"
                sorting-id="percentage_votes"
                livewire-sort
                class="!py-2.5"
                :tooltip="trans('tables.validators.info.percentage')"
            />

            <x-tables.headers.desktop.number
                name="tables.validators.missed_blocks"
                class="whitespace-nowrap"
                sorting-id="missed_blocks"
                livewire-sort
            />

            <x-tables.headers.desktop.text width="70" />
        </tr>
    </thead>

    <tbody>
        @foreach($validators as $validator)
            <x-ark-tables.row wire:key="validator-{{ $validator->address() }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.validators.rank :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <div class="flex items-center space-x-2">
                        <x-tables.rows.desktop.encapsulated.address
                            :model="$validator"
                            without-clipboard
                            validator-name-class="md:w-[100px] md-lg:w-auto"
                        />

                        <div x-show="votingForAddress === '{{ $validator->address() }}'">
                            <div data-tippy-content="@lang('pages.validators.arkconnect.voting_for_tooltip')">
                                <x-ark-icon
                                    name="check-mark-box"
                                    size="sm"
                                />
                            </div>
                        </div>
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.validators.validator-status :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.number-of-voters :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                >
                    <x-tables.rows.desktop.encapsulated.validators.votes :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                    breakpoint="lg"
                >
                    <x-tables.rows.desktop.encapsulated.validators.votes-percentage :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.missed-blocks :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.vote-link :model="$validator" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
