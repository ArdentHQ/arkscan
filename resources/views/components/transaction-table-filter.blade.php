<div x-data="{
    filterOpen: false,
    transactionTypeFilter: '{{ $type }}',
    transactionTypeFilterLabel: '@lang('forms.search.transaction_types.' . $type)',
}" x-cloak>
    <x-ark-dropdown
        wrapper-class="transaction-filter-wrapper"
        dropdown-classes="transaction-filter-dropdown"
        button-class="transaction-filter-button"
        dropdown-property="filterOpen"
        :init-alpine="false"
    >
        @slot('button')
            <div class="space-x-2 transaction-filter-button-container">
                <div>
                    <span class="text-theme-secondary-500 dark:text-theme-secondary-600">@lang('general.transaction.type'):</span>

                    <span
                        x-text="transactionTypeFilterLabel"
                        class="whitespace-nowrap text-theme-secondary-900 md:text-theme-secondary-700 dark:text-theme-secondary-200"
                    ></span>
                </div>

                <span
                    :class="{ 'rotate-180 md:bg-theme-primary-600 md:text-theme-secondary-100': filterOpen }"
                    class="transaction-filter-button-icon"
                >
                    <x-ark-icon name="chevron-down" size="xs" class="md:h-3 md:w-2" />
                </span>
            </div>
        @endslot

        <div class="block overflow-y-scroll justify-center items-center py-3 h-64 dropdown-scrolling md:h-72">
            @foreach([
                'all',
                'transfer',
                'secondSignature',
                'delegateRegistration',
                'vote',
                'voteCombination',
                'multiSignature',
                'ipfs',
                'multiPayment',
                'delegateResignation',
                'timelock',
                'timelockClaim',
                'timelockRefund',
                'magistrate',
            ] as $type)
                <div
                    class="cursor-pointer dropdown-entry text-theme-secondary-900 dark:text-theme-secondary-200"
                    :class="{
                        'dropdown-entry-selected': transactionTypeFilter === '{{ $type }}'
                    }"
                    @click="window.livewire.emit('filterTransactionsByType', '{{ $type }}'); transactionTypeFilter = '{{ $type }}'; transactionTypeFilterLabel = '@lang('forms.search.transaction_types.'.$type)'"
                >
                    @lang('forms.search.transaction_types.'.$type)
                </div>

            @endforeach
        </div>
    </x-ark-dropdown>
</div>
