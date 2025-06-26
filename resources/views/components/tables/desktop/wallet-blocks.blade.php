@props([
    'blocks',
    'wallet' => null,
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('blocks') }}"
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
                breakpoint="md-lg"
                responsive
            />
            <x-tables.headers.desktop.number name="tables.blocks.transactions" />
            <x-tables.headers.desktop.number
                name="tables.blocks.volume"
                :name-properties="['currency' => Network::currency()]"
                :tooltip="trans('pages.wallets.blocks.volume_tooltip')"
            />

            @if (Network::canBeExchanged())
                <x-tables.headers.desktop.number
                    name="tables.blocks.total_reward"
                    :name-properties="['currency' => Network::currency()]"
                    last-on="lg"
                    class="whitespace-nowrap last-until-lg"
                    :tooltip="trans('pages.wallets.blocks.total_reward_tooltip')"
                />

                <x-tables.headers.desktop.number
                    name="tables.blocks.value"
                    :name-properties="['currency' => Settings::currency()]"
                    breakpoint="lg"
                    responsive
                    class="whitespace-nowrap"
                    :tooltip="trans('pages.wallets.blocks.value_tooltip')"
                />
            @else
                <x-tables.headers.desktop.number
                    name="tables.blocks.total_reward"
                    :name-properties="['currency' => Network::currency()]"
                    :tooltip="trans('pages.wallets.blocks.total_reward_tooltip')"
                />
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($blocks as $block)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('block-item', $block->hash()) }}">
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

                @if (Network::canBeExchanged())
                    <x-ark-tables.cell
                        class="text-right"
                        last-on="lg"
                    >
                        <x-tables.rows.desktop.encapsulated.reward
                            :model="$block"
                            :without-value="false"
                        />
                    </x-ark-tables.cell>

                    <x-ark-tables.cell
                        class="text-right"
                        responsive
                        breakpoint="lg"
                    >
                        <x-tables.rows.desktop.encapsulated.value :model="$block" />
                    </x-ark-tables.cell>
                @else
                    <x-ark-tables.cell class="text-right">
                        <x-tables.rows.desktop.encapsulated.reward :model="$block" />
                    </x-ark-tables.cell>
                @endif
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-tables.encapsulated-table>
