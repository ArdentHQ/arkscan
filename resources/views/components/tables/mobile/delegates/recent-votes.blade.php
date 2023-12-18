@props([
    'votes',
    'noResultsMessage' => null,
])

<x-tables.mobile.includes.encapsulated
    wire:key="{{ Helpers::generateId('votes-mobile') }}"
    :no-results-message="$noResultsMessage"
>
    @foreach ($votes as $vote)
        <x-tables.rows.mobile
            wire:key="{{ Helpers::generateId('vote-mobile', $vote->id()) }}"
            expandable
        >
            <x-slot name="header">
                <div class="flex flex-1 justify-between">
                    <x-tables.rows.mobile.encapsulated.transaction-id :model="$vote" />

                    <x-tables.rows.mobile.encapsulated.age
                        :model="$vote"
                        class="leading-4.25"
                    />
                </div>
            </x-slot>

            <div>
                <x-tables.rows.mobile.encapsulated.transaction
                    :model="$vote"
                    :label="trans('tables.recent-votes.addressing')"
                    class="sm:hidden"
                    always-show-address
                />

                <x-tables.rows.mobile.encapsulated.transaction
                    :model="$vote"
                    :label="trans('tables.recent-votes.addressing')"
                    class="hidden sm:block"
                    always-show-address
                    without-truncate
                />
            </div>

            <x-tables.rows.mobile.encapsulated.delegates.vote-address
                :transaction="$vote"
                class="sm:text-right"
                value-class="sm:justify-end"
            />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
