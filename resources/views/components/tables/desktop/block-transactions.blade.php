@props([
    'transactions',
    'withLazyLoading' => false,
])

<x-tables.encapsulated-table
    wire:key="{{ Helpers::generateId('block-transactions') }}"
    class="hidden w-full rounded-b-xl md:block"
>
    <thead>
        <tr>
            <x-tables.headers.desktop.id
                name="tables.transactions.id"
                class="whitespace-nowrap md-lg:w-[130px] lg:w-[150px]"
            />
            <x-tables.headers.desktop.text
                name="tables.transactions.method"
                class="md-lg:w-[100px] lg:w-[150px]"
            />
            <x-tables.headers.desktop.text name="tables.transactions.addressing" />
            <x-tables.headers.desktop.number
                name="tables.transactions.amount"
                :name-properties="['currency' => Network::currency()]"
                last-on="xl"
                class="last-until-xl"
            />
            <x-tables.headers.desktop.number
                name="tables.transactions.fee"
                :name-properties="['currency' => Network::currency()]"
                responsive
                breakpoint="xl"
            />
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <x-ark-tables.row wire:key="{{ Helpers::generateId('transaction-item', $transaction->hash()) }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-id
                        :model="$transaction"
                        without-age
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.transaction-type :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.addressing-generic :model="$transaction" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    last-on="xl"
                >
                    <x-tables.rows.desktop.encapsulated.amount
                        :model="$transaction"
                        breakpoint="xl"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    responsive
                    breakpoint="xl"
                >
                    <x-tables.rows.desktop.encapsulated.fee :model="$transaction" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach

        @if ($withLazyLoading && ! $this->isOnLastPage())
            <tr
                class="hidden"
                wire:loading.class.remove="hidden"
            >
                <td colspan="5">
                    <div class="flex justify-center items-center">
                        <x-ark-loader-icon
                            class="w-8 h-8"
                            path-class="fill-theme-primary-600 dark:fill-theme-dark-blue-400"
                            circle-class="stroke-theme-primary-100 dark:stroke-theme-dark-700"
                        />
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</x-tables.encapsulated-table>
