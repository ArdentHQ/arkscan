@props([
    'votes',
    'noResultsMessage' => null,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('recent-votes') }}"
    class="hidden w-full rounded-t-none md:block"
    :rounded="false"
    :paginator="$votes"
    :no-results-message="$noResultsMessage"
    sticky
>
    <thead>
        <tr>
            <x-tables.headers.desktop.id
                name="tables.recent-votes.id"
                class="whitespace-nowrap"
                width="200"
            />

            <x-tables.headers.desktop.text
                name="tables.recent-votes.age"
                breakpoint="xl"
                responsive
                sorting-id="age"
                livewire-sort
            />

            <x-tables.headers.desktop.text
                name="tables.recent-votes.addressing"
                sorting-id="address"
                livewire-sort
            />
            <x-tables.headers.desktop.text
                name="tables.recent-votes.type"
                sorting-id="type"
                livewire-sort
            />
            <x-tables.headers.desktop.text
                name="tables.recent-votes.delegate"
                sorting-id="name"
                livewire-sort
            />
        </tr>
    </thead>
    <tbody>
        @foreach($votes as $vote)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('vote-item', $vote->id()) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-id :model="$vote" />
                </x-ark-tables.cell>

                <x-ark-tables.cell responsive breakpoint="xl">
                    <x-tables.rows.desktop.encapsulated.age :model="$vote" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.addressing
                        :model="$vote"
                        always-show-address
                        without-truncate
                        class="hidden lg:flex"
                        generic
                    />

                    <x-tables.rows.desktop.encapsulated.addressing
                        :model="$vote"
                        always-show-address
                        class="lg:hidden"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-type :model="$vote" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    @if ($vote->isVote())
                        <x-tables.rows.desktop.encapsulated.address
                            :model="$vote->voted()"
                            without-clipboard
                        />
                    @else
                        <x-tables.rows.desktop.encapsulated.address
                            :model="$vote->unvoted()"
                            without-clipboard
                        />
                    @endif
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-tables.encapsulated-table>
