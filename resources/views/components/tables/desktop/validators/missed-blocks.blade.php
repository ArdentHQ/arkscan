@props([
    'blocks',
    'noResultsMessage' => null,
])

@php ($canSort = config('database.default') !== 'sqlite')

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
                :sorting-id="$canSort ? 'height' : null"
                :livewire-sort="$canSort"
            />

            <x-tables.headers.desktop.text
                name="tables.blocks.age"
                breakpoint="md-lg"
                responsive
                :sorting-id="$canSort ? 'age' : null"
                :livewire-sort="$canSort"
            />

            <x-tables.headers.desktop.address
                name="tables.missed-blocks.validator"
                :sorting-id="$canSort ? 'name' : null"
                :livewire-sort="$canSort"
            />

            <x-tables.headers.desktop.number
                name="tables.missed-blocks.no_of_voters"
                class="whitespace-nowrap"
                :sorting-id="$canSort ? 'no_of_voters' : null"
                :livewire-sort="$canSort"
            />

            <x-tables.headers.desktop.number
                name="tables.missed-blocks.votes"
                :name-properties="['currency' => Network::currency()]"
                class="whitespace-nowrap"
                :sorting-id="$canSort ? 'votes' : null"
                :livewire-sort="$canSort"
            />

            <x-tables.headers.desktop.number
                name="tables.missed-blocks.percentage"
                :tooltip="trans('tables.missed-blocks.info.percentage')"
                :sorting-id="$canSort ? 'percentage_votes' : null"
                :livewire-sort="$canSort"
            />
        </tr>
    </thead>

    <tbody>
        @foreach($blocks as $block)
            @php ($validator = $block->validator())
            <x-ark-tables.row wire:key="{{ Helpers::generateId('block', $block->timestamp()) }}">
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
                    @if ($validator)
                        <x-tables.rows.desktop.encapsulated.address
                            :model="$block"
                            without-clipboard
                        />
                    @else
                        <span class="leading-4.25">-</span>
                    @endif
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.number-of-voters
                        :model="$validator"
                        without-breakdown
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.votes :model="$validator" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.validators.votes-percentage :model="$validator" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
