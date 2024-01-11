@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('blocks-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($blocks as $block)
        <x-tables.rows.mobile wire:key="{{ Helpers::generateId('blocks-mobile-row', $block->id()) }}">
            <x-slot name="header">
                <x-tables.headers.mobile.encapsulated.block-height
                    :model="$block"
                    class="sm:flex-1"
                />

                <x-tables.rows.mobile.encapsulated.transaction-count
                    :model="$block"
                    class="hidden sm:justify-end leading-4.25 sm:w-[142px]"
                    flex-direction="sm:flex-row-reverse"
                    value-class="sm:mr-1"
                />

                <x-tables.rows.mobile.encapsulated.age
                    :model="$block"
                    class="sm:flex-1 sm:text-right leading-4.25"
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.transaction-count
                :model="$block"
                class="sm:hidden"
            />

            <x-tables.rows.mobile.encapsulated.volume
                :model="$block"
                class="sm:flex-1"
            />

            <x-tables.rows.mobile.encapsulated.reward
                :model="$block"
                class="sm:w-[142px]"
            />

            <div @class([
                'sm:flex sm:flex-1 sm:justify-end',
                'hidden sm:flex' => ! Network::canBeExchanged(),
            ])>
                @if (Network::canBeExchanged())
                    <x-tables.rows.mobile.encapsulated.value :model="$block" />
                @endif
            </div>
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
