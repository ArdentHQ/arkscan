@props([
    'blocks',
    'wallet' => null,
    'state' => [],
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('blocks', ...$state) }}"
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
            />
            <x-tables.headers.desktop.text
                name="tables.blocks.age"
                breakpoint="xl"
                responsive
            />
            <x-tables.headers.desktop.number name="tables.blocks.transactions" />
            <x-tables.headers.desktop.number
                name="tables.blocks.volume"
                :name-properties="['currency' => Network::currency()]"
            >
                <x-tables.headers.desktop.includes.tooltip :text="trans('pages.wallets.blocks.volume_tooltip')" />
            </x-tables.headers.desktop.number>
            <x-tables.headers.desktop.number
                name="tables.blocks.total_reward"
                :name-properties="['currency' => Network::currency()]"
                last-on="md-lg"
                class="last-until-md-lg"
            >
                <x-tables.headers.desktop.includes.tooltip :text="trans('pages.wallets.blocks.total_reward_tooltip')" />
            </x-tables.headers.desktop.number>
            <x-tables.headers.desktop.number
                name="tables.blocks.value"
                :name-properties="['currency' => Settings::currency()]"
                breakpoint="md-lg"
                responsive
            >
                <x-tables.headers.desktop.includes.tooltip :text="trans('pages.wallets.blocks.value_tooltip')" />
            </x-tables.headers.desktop.number>
        </tr>
    </thead>
    <tbody>
        @foreach($blocks as $block)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('block-item', $block->id(), ...$state) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.block-height :model="$block" />
                </x-ark-tables.cell>

                <x-ark-tables.cell responsive breakpoint="md-lg">
                    <x-tables.rows.desktop.encapsulated.age :model="$block" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.transaction-count :model="$block" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.encapsulated.volume :model="$block" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    last-on="md-lg"
                >
                    <x-tables.rows.desktop.encapsulated.reward :model="$block" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                    breakpoint="md-lg"
                >
                    <x-tables.rows.desktop.encapsulated.value :model="$block" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-tables.encapsulated-table>
