@props(['wallets'])

<x-tables.mobile.includes.encapsulated wire:key="{{ Helpers::generateId('top-accounts-mobile') }}">
    @foreach ($wallets as $wallet)
        <x-tables.rows.mobile wire:key="{{ Helpers::generateId('top-accounts-mobile-row', $wallet->address()) }}">
            <x-slot name="header">
                <div class="flex items-center space-x-3">
                    <x-tables.headers.mobile.encapsulated.rank :results="$wallets" :index="$loop->index + 1" />

                    <x-tables.headers.mobile.encapsulated.address :model="$wallet" />
                </div>
            </x-slot>

            <div class="flex flex-col sm:flex-row sm:flex-1 leading-4.25">
                <x-tables.rows.mobile.encapsulated.username
                    :model="$wallet"
                    :class="Arr::toCssClasses([
                        'sm:flex-1 mb-4 sm:mb-0' => $wallet->username(),
                        'sm:flex-1 hidden sm:block' => ! $wallet->username(),
                    ])"
                />

                <x-tables.rows.mobile.encapsulated.balance
                    :model="$wallet"
                    class="mb-4 sm:mb-0"
                />

                <div class="sm:flex sm:flex-1 sm:justify-end">
                    <x-tables.rows.mobile.encapsulated.balance-percentage :model="$wallet" />
                </div>
            </div>
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
