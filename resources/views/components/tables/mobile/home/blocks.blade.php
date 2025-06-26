@props([
    'blocks',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('blocks-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($blocks as $block)
        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('blocks-mobile-row', $block->hash()) }}"
            content-class="sm:grid sm:grid-cols-5 sm:gap-6"
        >
            <x-slot name="header">
                <x-tables.headers.mobile.encapsulated.block-height
                    :model="$block"
                    class="sm:flex-1"
                />

                <x-tables.rows.mobile.encapsulated.age
                    :model="$block"
                    class="sm:flex-1 sm:text-right leading-4.25"
                />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.generated-by
                :model="$block"
                class="sm:col-span-2"
            />

            <x-tables.rows.mobile.encapsulated.transaction-count
                :model="$block"
                class="leading-4.25"
            />

            <div class="sm:flex sm:flex-1 sm:col-span-2 sm:justify-end">
                <x-tables.rows.mobile.encapsulated.volume :model="$block" />
            </div>

            <x-tables.rows.mobile.encapsulated.reward
                :model="$block"
                class="sm:col-span-2 sm:w-[142px]"
            />

            @if (Network::canBeExchanged())
                <x-tables.rows.mobile.encapsulated.value
                    :model="$block"
                    class="sm:col-span-2"
                />
            @endif
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
