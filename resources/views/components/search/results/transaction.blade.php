@props(['transaction'])

<x-search.results.result :model="$transaction">
    <x-tables.rows.mobile class="md:hidden">
        <x-slot name="header">
            <div class="min-w-0 link group-hover/result:no-underline hover:text-theme-primary-600">
                <x-truncate-middle>
                    {{ $transaction->id() }}
                </x-truncate-middle>
            </div>
        </x-slot>

        <x-search.results.mobile.transaction-type :transaction="$transaction" />

        <x-search.results.mobile.detail :title="trans('general.search.value_currency', ['currency' => Network::currency()])">
            {{ $transaction->amountWithFee() }}
        </x-search.results.mobile.detail>
    </x-tables.rows.mobile>

    <div class="hidden flex-col space-y-2 md:flex">
        <div class="flex items-center space-x-2">
            <x-general.encapsulated.transaction-direction-badge width="min-w-[92px]">
                <x-general.encapsulated.transaction-type :transaction="$transaction" />
            </x-general.encapsulated.transaction-direction-badge>

            <div class="flex-1 min-w-0 link group-hover/result:no-underline hover:text-theme-primary-600">
                <x-truncate-middle :length="20">
                    {{ $transaction->id() }}
                </x-truncate-middle>
            </div>
        </div>

        <div class="flex flex-col space-y-2 md:flex-row md:items-center md:space-y-0 md:space-x-4">
            <div class="flex items-center space-x-2 text-xs isolate">
                <x-general.encapsulated.transaction-direction-badge>
                    @lang('general.search.from')
                </x-general.encapsulated.transaction-direction-badge>

                @if ($transaction->isVote() || $transaction->isUnvote() || $transaction->isVoteCombination())
                    <x-general.identity
                        :model="$transaction->isUnvote() ? $transaction->unvoted() : $transaction->voted()"
                        without-link
                        class="text-theme-secondary-900 dark:text-theme-dark-50"
                    />
                @else
                    <x-general.identity
                        :model="$transaction->sender()"
                        without-link
                        class="text-theme-secondary-900 dark:text-theme-dark-50"
                    />
                @endif
            </div>

            <div class="flex items-center space-x-2 text-xs isolate">
                <x-general.encapsulated.transaction-direction-badge>
                    @lang('general.search.to')
                </x-general.encapsulated.transaction-direction-badge>

                @if ($transaction->isTransfer())
                    <x-general.identity
                        :model="$transaction->recipient()"
                        without-link
                        class="text-theme-secondary-900 dark:text-theme-dark-50"
                    />
                @elseif ($transaction->isVoteCombination())
                    <x-general.identity
                        :model="$transaction->voted()"
                        without-link
                        class="text-theme-secondary-900 dark:text-theme-dark-50"
                    />
                @elseif ($transaction->isMultiPayment())
                    <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                        @lang('tables.transactions.multiple')

                        ({{ $transaction->recipientsCount() }})
                    </span>
                @else
                    <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                        @lang('general.search.contract')
                    </span>
                @endif
            </div>

            <div class="flex items-center space-x-2 text-xs md:flex-1 md:justify-end md:space-x-0">
                <div class="md:hidden text-theme-secondary-500 dark:text-theme-dark-200">
                    @lang('general.search.amount')
                </div>

                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ ExplorerNumberFormatter::number($transaction->amountWithFee()) }}
                </div>
            </div>
        </div>
    </div>
</x-search.results.result>
